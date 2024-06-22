<?php

namespace Controller;

use Core\Database;
use Auth\Authenticate;
use Core\Request;
use Core\Response;
use Core\Status;
use Controller\Manager;
use Controller\Driver;
use Controller\Customer;
use Firebase\JWT\JWT;
use Auth\Auth;

class User
{
    private $db;
    private $db_config;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
        $this->db_config = $db_config;
    }

    public function getUserRole($category): mixed
    {
        return $this->db->run("SELECT `id` FROM `roles` WHERE `name` = ?", [$category])->fetchOne();
    }

    public function userExists($data = []): mixed
    {
        return $this->db->run("SELECT * FROM `users` WHERE `email` = ?", [$data["email"]])->fetchOne();
    }

    private function createAccountBasedOnCategory($user_id, $data): mixed
    {
        switch ($data["category"]) {
            case "customer":
                $customerObj = new Customer($this->db_config);
                return array("success" => true, "account_id" => $customerObj->createAccount($user_id, $data));
            case "driver":
                $driverObj = new Driver($this->db_config);
                return array("success" => true, "account_id" => $driverObj->createAccount($user_id, $data));
            case "manager":
                $managerObj = new Manager($this->db_config);
                return array("success" => true, "account_id" => $managerObj->createAccount($user_id, $data));
            default:
                return array(
                    "success" => false,
                    "status_code" => Status::$HTTP_400_BAD_REQUEST,
                    "message" => "Couldn't find a role for specified the user category",
                    "data" => null
                );
        }
    }

    public function createUserAccount($data = []): mixed
    {
        $role = $this->getUserRole($data["category"]);
        if (empty($role)) return array("success" => false, 'message' => "Couldn't find a role for specified the user category");
        $hashed_password = password_hash($data["password"], PASSWORD_BCRYPT);
        $user_id =  $this->db->run(
            "INSERT INTO `users` (`role_id`, `email`, `password`) VALUES(:r, :e, :p)",
            [
                ":r" => $role["id"],
                ":e" => $data["email"],
                ":p" => $hashed_password
            ]
        )->insert(true, null);

        if (!$user_id) return array("success" => false, 'message' => "Couldn't find a role for specified the user category");

        $secret_key = (new Auth($this->db_config))->generateSecretKey();
        $this->db->run(
            "INSERT INTO `user_secret_keys`(`user_id`, `secret_key`) VALUES(?,?)",
            [$user_id, $secret_key]
        )->insert(true, null);

        return array("success" => true, "data" => $user_id);
    }

    public function register($data): mixed
    {
        $user_exists = $this->userExists($data);
        if ($user_exists) {
            return array(
                "status_code" => Status::$HTTP_200_OK,
                "message" => "User with this email already exists! Please try logging in to continue.",
                "data" => null
            );
        }

        $user = $this->createUserAccount($data);
        if (!$user["success"]) {
            $user["status_code"] = Status::$HTTP_400_BAD_REQUEST;
            $user["data"] = null;
            return $user;
        }

        $account = $this->createAccountBasedOnCategory($user["data"], $data);
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

    public function login($data): mixed
    {
        $user_exists = $this->userExists($data);
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

        $auth = new Auth($this->db_config);
        $secret_key = $auth->getSecretKey($user_exists["id"]);

        $user_data = array(
            "id" => $user_exists["id"],
            "email" => $user_exists["email"],
            "role" => $user_exists["role_id"]
        );

        if (!isset($_SESSION['token'])) {
            $token = $auth->generateAPIToken($user_data, $secret_key);
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


    public function updateUsername($data)
    {
        return $this->db->run(
            "UPDATE `users` SET `username` = ? WHERE id = ?",
            [
                $data["username"],
                $data["id"]
            ]
        )->update();
    }

    public function updatePassword($data)
    {
        return $this->db->run(
            "UPDATE `users` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $data["id"]
            ]
        )->update();
    }
}
