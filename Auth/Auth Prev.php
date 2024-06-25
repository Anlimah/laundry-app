<?php

namespace Auth;

use Core\Database;
use Core\Status;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
        $secret = base64_encode(openssl_random_pseudo_bytes(32));
        error_log("\nGenerated Secret: " . $secret, 3, "./custom-log.log"); // Log the token for debugging
        return $secret;
    }

    public function getSecretKey($user_id): mixed
    {
        return $this->db->run("SELECT `secret_key` FROM `user_secret_keys` WHERE `user_id` = ?", [$user_id])->fetchOne();
    }

    public function generateAPIToken($data, $secret_key)
    {
        try {
            $token = JWT::encode($data, $secret_key["secret_key"], 'HS256');
            return $token;
        } catch (Exception $e) {
            error_log("\nToken Generation Failed: " . $token, 3, "./custom-log.log");
            return null;
        }
    }

    private function authenticateToken($token)
    {
        try {
            $user_id = $_SESSION["user"]["id"];
            $secret_key = $this->getSecretKey($user_id);
            $cleaned_secret_key = trim($secret_key["secret_key"]);
            if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
            $cleaned_token = trim($token);
            $decoded_token = JWT::decode($cleaned_token, new Key($cleaned_secret_key, 'HS256'));
            return $decoded_token;
        } catch (Exception $e) {
            error_log("\nToken Authentication Failed: " . $e->getMessage(), 3, "./custom-log.log");
            return null;
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

        $user = $this->authenticateToken($token);
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

        $secret_key = $this->getSecretKey($user_exists["id"]);

        $user_data = array(
            "id" => $user_exists["id"],
            "email" => $user_exists["email"],
            "role" => $user_exists["role_id"]
        );

        if (!isset($_SESSION['token'])) {
            $token = $this->generateAPIToken($user_data, $secret_key);
            $_SESSION["user"]['token'] = $token;
        }
        $_SESSION["user"]["id"] = $user_exists["id"];
        $_SESSION["user"]["email"] = $user_exists["email"];
        $_SESSION["user"]["role"] = $user_exists["role_id"];

        if (isset($data["password"])) unset($data["password"]);
        return array(
            "status_code" => Status::$HTTP_200_OK,
            "message" => "Login successful!",
            "data" => array("token" => $_SESSION["user"]['token'])
        );
    }
}
