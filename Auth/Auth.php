<?php

namespace Auth;

use Core\Database;
use Core\Status;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;
use Controller\User;

class Auth
{
    private $db;
    private $db_config;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
        $this->db_config = $db_config;
    }

    public function generateSecretKey(): string
    {
        return base64_encode(openssl_random_pseudo_bytes(32));
    }

    public function getSecretKey($user_id): mixed
    {
        $result = $this->db->run(
            "SELECT `secret_key` FROM `user_secret_keys` WHERE `user_id` = ?",
            [$user_id]
        )->fetchOne();
        return !empty($result) ? $result["secret_key"] : null;
    }

    public function getExpiredToken($token): mixed
    {
        return $this->db->run(
            "SELECT `token` FROM `expired_tokens` WHERE `token` = ?",
            [$token]
        )->fetchOne();
    }

    private function storeTokenInDatabase($user_id, $token, $type, $expiry)
    {
        $this->db->run(
            "INSERT INTO `login_tokens` (`user_id`, `token`, `type`, `created_at`, `expire_at`) VALUES (?, ?, ?, ?, ?)",
            [
                $user_id,
                $token,
                $type,
                date('Y-m-d H:i:s'),
                $expiry
            ]
        )->insert(true, null);
    }

    public function getTokensByUserId($user_id, $type = null)
    {
        if ($type) {
            $query = "SELECT * FROM `login_tokens` WHERE `user_id` = ? AND `type` = ?";
            $params = [$user_id, $type];
            return $this->db->run($query, $params)->fetchOne();
        } else {
            $query = "SELECT * FROM `login_tokens` WHERE `user_id` = ?";
            $params = [$user_id];
            return $this->db->run($query, $params)->fetchAll();
        }
    }

    public function getUserIDByToken($token): mixed
    {
        $result = $this->db->run(
            "SELECT `user_id` FROM `login_tokens` WHERE `token` = ?",
            [$token]
        )->fetchOne();
        return !empty($result) ? $result["user_id"] : null;
    }

    public function generateToken($data, $secret_key, $type)
    {
        $issuedAt = new \DateTime();

        if ($type === 'access') {
            $expirationTime = new \DateTime();
            $expirationTime->add(new \DateInterval('PT1H')); // Token valid for 1 hour
        } elseif ($type === 'refresh') {
            $expirationTime = new \DateTime();
            $expirationTime->add(new \DateInterval('P14D')); // Token valid for 2 weeks
        } else {
            throw new Exception('Invalid token type specified');
        }

        $payload = array(
            "iat" => $issuedAt->getTimestamp(),
            "exp" => $expirationTime->getTimestamp(),
            "data" => $data
        );

        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        $this->storeTokenInDatabase($data['id'], $jwt, $type, $expirationTime->format('Y-m-d H:i:s'));

        return $jwt;
    }

    public function validateToken($token): mixed
    {
        try {
            if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
            $cleaned_token = trim($token);

            $expired = $this->getExpiredToken($cleaned_token);
            if ($expired) return array("success" => false, "message" => "Token has expired!");

            $user_id = $this->getUserIDByToken($cleaned_token);
            if (!$user_id) return array("success" => false, "message" => "Invalid token!");

            $secret_key = $this->getSecretKey($user_id);
            if (!$secret_key) return array("success" => false, "message" => "Invalid token!");

            $decoded_token = JWT::decode($cleaned_token, new Key($secret_key, 'HS256'));
            return array("success" => true, "message" => "access granted!", "data" => $decoded_token->data);
        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }
    }

    public function authenticate(): mixed
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        if (!$token) {
            return array(
                "status_code" => Status::$HTTP_401_UNAUTHORIZED,
                "message" => 'Token is missing!',
                "data" => $token
            );
        }

        $validated_token = $this->validateToken($token);
        if (!$validated_token["success"]) {
            return array(
                "status_code" => Status::$HTTP_401_UNAUTHORIZED,
                "message" => $validated_token["message"],
                "data" => null
            );
        }

        return array(
            "status_code" => Status::$HTTP_200_OK,
            "message" => $validated_token["message"],
            "data" => $validated_token["data"]
        );
    }

    public function refreshToken($refreshToken)
    {
        try {
            $user_id = $_SESSION["user"]["id"];
            $secret_key = $this->getSecretKey($user_id); // Replace with your secret key
            $decoded_token = JWT::decode($refreshToken, new Key($secret_key, 'HS256'));

            $userData = $decoded_token->data;

            $newAccessToken = $this->generateToken($userData, $secret_key, "access");
            $newRefreshToken = $this->generateToken($userData, $secret_key, "refresh");

            return array(
                "status_code" => Status::$HTTP_200_OK,
                "message" => 'Token refreshed successfully',
                "data" => array(
                    "access_token" => $newAccessToken,
                    "refresh_token" => $newRefreshToken
                )
            );
        } catch (Exception $e) {
            return array(
                "status_code" => Status::$HTTP_401_UNAUTHORIZED,
                "message" => 'Invalid refresh token!',
                "data" => null
            );
        }
    }

    public function register($data = []): mixed
    {
        $userObj = new User($this->db_config);
        $user_exists = $userObj->userExists($data);
        if ($user_exists) {
            return array(
                "status_code" => Status::$HTTP_200_OK,
                "message" => "User with this email already exists! Please try logging in to continue.",
                "data" => null
            );
        }

        $user = $userObj->createUserAccount($data);
        if (!$user["success"]) {
            $user["status_code"] = Status::$HTTP_400_BAD_REQUEST;
            $user["data"] = null;
            return $user;
        }

        $account = $userObj->createAccountBasedOnCategory($user["data"], $data);
        if (!$account["success"]) return $account;

        $data["id"] = $user["data"];
        if (isset($data["password"])) unset($data["password"]);

        return array(
            "success" => true,
            "status_code" => Status::$HTTP_201_CREATED,
            "message" => "Account successfully created!",
            "data" => $data
        );
    }

    public function login($data = []): mixed
    {
        $userObj = new User($this->db_config);

        $user_exists = $userObj->userExists($data);
        if (!$user_exists) return array(
            "status_code" => Status::$HTTP_404_NOT_FOUND,
            "message" => "No account found with this email! Please register to continue.",
            "data" => null
        );

        $verified = password_verify($data["password"], $user_exists["password"]);
        if (!$verified) return array(
            "status_code" => Status::$HTTP_400_BAD_REQUEST,
            "message" => "Incorrect email or password!",
            "data" => null
        );

        $accessTokenData = $this->getTokensByUserId($user_exists["id"], 'access');
        $refreshTokenData = $this->getTokensByUserId($user_exists["id"], 'refresh');

        $current_time = time();
        $generate_new_access_tokens = true;
        $generate_new_refresh_tokens = true;

        if (!empty($accessTokenData) && !empty($refreshTokenData)) {
            $accessToken = $accessTokenData['token'];
            $refreshToken = $refreshTokenData['token'];
            $accessTokenExpiry = $accessTokenData['expire_at'];
            $refreshTokenExpiry = $refreshTokenData['expire_at'];
            if ($accessTokenExpiry > $current_time) $generate_new_access_tokens = false;
            if ($refreshTokenExpiry > $current_time) $generate_new_refresh_tokens = false;
        }

        if ($generate_new_refresh_tokens) {
            $secret_key = $this->getSecretKey($user_exists["id"]);
            $user_data = array(
                "id" => $user_exists["id"],
                "email" => $user_exists["email"],
                "role" => $user_exists["role_id"]
            );
            $accessToken = $this->generateToken($user_data, $secret_key, "access");
            $refreshToken = $this->generateToken($user_data, $secret_key, "refresh");
        }

        if ($generate_new_access_tokens && !$generate_new_refresh_tokens) {
            $secret_key = $this->getSecretKey($user_exists["id"]);
            $user_data = array(
                "id" => $user_exists["id"],
                "email" => $user_exists["email"],
                "role" => $user_exists["role_id"]
            );
            $accessToken = $this->generateToken($user_data, $secret_key, "refresh");
        }

        if (isset($data["password"])) unset($data["password"]);

        return array(
            "status_code" => Status::$HTTP_200_OK,
            "message" => "Login successful!",
            "data" => array("access_token" => $accessToken, "refresh_token" => $refreshToken)
        );
    }
}
