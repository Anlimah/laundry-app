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
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->db = new Database($this->username, $this->password);
    }

    public function register($params = null): mixed
    {
        $data = Request::getBody();
        $account =  $this->db->run(
            "INSERT INTO `users` (`category`, `username`, `password`) VALUES(?, ?)",
            [
                $data["category"],
                $data["username"],
                password_hash($data["password"], PASSWORD_BCRYPT)
            ]
        )->insert(true, null);

        if (!$account) return Response::json(Status::$HTTP_400_BAD_REQUEST, "");

        switch ($data["category"]) {
            case 'customer':
                $customerObj = new Customer($this->username, $this->password);
                $user = $customerObj->createCustomer($data);
                break;
            case 'driver':
                $customerObj = new Driver($this->username, $this->password);
                $user = $customerObj->createAccount($data);
                break;
            case 'manager':
                $customerObj = new Manager($this->username, $this->password);
                $user = $customerObj->createAccount($data);
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

        $data["id"] = $account;
        $message = "Account successfully created!";
        return Response::json(Status::$HTTP_201_CREATED, $message, $data);
    }

    public function login($params = null): mixed
    {
    }

    public function updateUsername($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `users` SET `username` = ? WHERE id = ?",
            [
                $data["username"],
                $params["id"]
            ]
        )->update();
    }

    public function updatePassword($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `users` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $params["id"]
            ]
        )->update();
    }
}
