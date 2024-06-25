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
    private $accessTokenExpiry = 3600; // 1 hour
    private $refreshTokenExpiry = 1209600; // 2 weeks

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

    private function storeTokenInDatabase($user_id, $token, $type)
    {
        $this->db->run(
            "INSERT INTO `login_tokens` (`user_id`, `token`, `type`, `created_at`) VALUES (?, ?, ?, ?)",
            [
                $user_id,
                $token,
                $type,
                date('Y-m-d H:i:s')
            ]
        )->insert(true, null);
    }

    public function getTokensByUserId($user_id, $type = null)
    {
        return $this->db->run(
            "SELECT `token` FROM `login_tokens` WHERE `user_id` = ? AND `type` = ?",
            [$user_id, $type]
        )->fetchOne();
    }

    public function generateAccessToken($data, $secret_key)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour
        $payload = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "data" => $data
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        $this->storeTokenInDatabase($data['id'], $jwt, 'access');
        return $jwt;
    }

    public function generateRefreshToken($data, $secret_key)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (30 * 24 * 60 * 60);
        $payload = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "data" => $data
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        $this->storeTokenInDatabase($data['id'], $jwt, 'refresh');
        return $jwt;
    }

    public function validateToken($token): mixed
    {
        try {
            $user_id = $_SESSION["user"]["id"];
            $secret_key = $this->getSecretKey($user_id);
            $decoded_token = JWT::decode($token, new Key($secret_key, 'HS256'));
            return $decoded_token->data;
        } catch (Exception $e) {
            return null;
        }
    }

    public function verifyToken($token): bool
    {
        try {
            $user_id = $_SESSION["user"]["id"];
            $secret_key = $this->getSecretKey($user_id);
            $decoded_token = JWT::decode($token, new Key($secret_key, 'HS256'));
            return $decoded_token->data;
        } catch (Exception $e) {
            return false;
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

        $user = $this->validateToken($token);
        if (!$user) {
            return array(
                "status_code" => Status::$HTTP_401_UNAUTHORIZED,
                "message" => 'Invalid token!',
                "data" => $user
            );
        }

        return array(
            "status_code" => Status::$HTTP_200_OK,
            "message" => 'accessed!',
            "data" => $user
        );
    }

    public function refreshToken($refreshToken)
    {
        try {
            $user_id = $_SESSION["user"]["id"];
            $secret_key = $this->getSecretKey($user_id); // Replace with your secret key
            $decoded_token = JWT::decode($refreshToken, new Key($secret_key, 'HS256'));

            $userData = $decoded_token->data;

            $newAccessToken = $this->generateAccessToken($userData, $secret_key);
            $newRefreshToken = $this->generateRefreshToken($userData, $secret_key);

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

        $tokens = $this->getTokensByUserId($user_exists["id"]);

        $current_time = time();
        $generate_new_tokens = true;

        if ($tokens) {
            $accessToken = $tokens['access'];
            $refreshToken = $tokens['refresh'];
            $accessTokenExpiry = $tokens['type'] === "access" ? $tokens['expiry'] : null;
            $refreshTokenExpiry = $tokens['type'] === "refresh" ? $tokens['expiry'] : null;
            if ($accessTokenExpiry > $current_time && $refreshTokenExpiry > $current_time) $generate_new_tokens = false;
        }

        if ($generate_new_tokens) {
            $secret_key = $this->getSecretKey($user_exists["id"]);
            $user_data = array(
                "id" => $user_exists["id"],
                "email" => $user_exists["email"],
                "role" => $user_exists["role_id"]
            );
            $accessToken = $this->generateAccessToken($user_data, $secret_key);
            $refreshToken = $this->generateRefreshToken($user_data, $secret_key);
        }

        if (isset($data["password"])) unset($data["password"]);

        return array(
            "status_code" => Status::$HTTP_200_OK,
            "message" => "Login successful!",
            "data" => array("access_token" => $accessToken, "refresh_token" => $refreshToken)
        );
    }
}
