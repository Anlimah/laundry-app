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

    public function register($data): mixed
    {
        $role["id"] = $this->getUserRole($data["category"]);
        if (empty($role)) return Response::json(Status::$HTTP_400_BAD_REQUEST, "Couldn't find a role for specified the user category");
        $user_id =  $this->db->run(
            "INSERT INTO `users` (`role_id`, `email`, `password`) VALUES(?, ?, ?)",
            [
                $role["id"],
                $data["email"],
                $data["username"],
                password_hash($data["password"], PASSWORD_BCRYPT)
            ]
        )->insert(true, null);

        if (!$user_id) return Response::json(Status::$HTTP_400_BAD_REQUEST, "");

        switch ($data["category"]) {
            case 'customer':
                $customerObj = new Customer($this->db_config);
                $user = $customerObj->createAccount($user_id, $data);
                break;
            case 'driver':
                $customerObj = new Driver($this->db_config);
                $user = $customerObj->createAccount($user_id, $data);
                break;
            case 'manager':
                $customerObj = new Manager($this->db_config);
                $user = $customerObj->createAccount($user_id, $data);
                break;

            default:
                $message = "No match found for specified user category!";
                return Response::json(Status::$HTTP_201_CREATED, $message, $data = null);
                break;
        }

        if (!$user) {
            $message = "Failed to create user account!";
            return Response::json(Status::$HTTP_201_CREATED, $message, $data = null);
        }

        $data["id"] = $user_id;
        $message = "Account successfully created!";
        return Response::json(Status::$HTTP_201_CREATED, $message, $data);
    }

    public function login($data): mixed
    {
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
