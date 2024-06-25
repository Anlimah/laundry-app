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

    public function createAccountBasedOnCategory($user_id, $data): mixed
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
