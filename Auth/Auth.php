<?php

namespace Auth;

use Core\Database;
use Core\Status;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
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
        $token = JWT::encode($data, $secret_key["secret_key"], 'HS256');
        error_log("\nGenerated Token: " . $token, 3, "./custom-log.log"); // Log the token for debugging
        return $token;
    }

    public function authenticateToken($token)
    {
        $user_id = $_SESSION["user"]["id"];
        try {
            $secret_key = $this->getSecretKey($user_id);
            $cleaned_secret_key = trim($secret_key["secret_key"]); // Trim any whitespace

            // Extract the token from the "Bearer" prefix
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $cleaned_token = trim($token); // Trim any whitespace
            error_log("\nSecret Key: " . $cleaned_secret_key, 3, "./custom-log.log"); // Log the secret key for debugging
            error_log("\nToken: " . $cleaned_token, 3, "./custom-log.log"); // Log the token for debugging
            $decoded_token = JWT::decode($cleaned_token, new Key($cleaned_secret_key, 'HS256'));
            error_log("\nDecoded Token: " . print_r($decoded_token, true), 3, "./custom-log.log"); // Log the decoded token for debugging
            return $decoded_token; // Return the decoded token directly
        } catch (Exception $e) {
            error_log("\nToken Authentication Failed: " . $e->getMessage(), 3, "./custom-log.log"); // Log the error message for debugging
            return null; // Return null if token is invalid
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
}
