<?php

namespace Controller;

use Core\Database;
use Core\Request;

class Manager
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
    }

    public function getAll($params = null)
    {
        return $this->db->run("SELECT * FROM `managers`")->fetchAll();
    }

    public function getById($params = null)
    {
        return $this->db->run("SELECT * FROM `managers` WHERE `id` = ?", [$params["id"]])->fetchOne();
    }

    public function getByBranchId($params = null)
    {
        return $this->db->run("SELECT * FROM `managers` WHERE `branch_id` = ?", [$params["id"]])->fetchOne();
    }

    public function createAccount($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "INSERT INTO `managers` 
            (`branch_id`, `first_name`, `last_name`, `phone_number`, `address`, `email_address`, `password`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data["branch_id"],
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"],
                $data["email_address"],
                password_hash($data["password"], PASSWORD_BCRYPT)
            ]
        )->insert();
    }

    public function updateAccount($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `managers` SET 
            `first_name` = ?, `last_name` = ?, `phone_number` = ?, `address` = ?, `email_address` = ?
            WHERE id = ?",
            [
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"],
                $data["email_address"],
                $params["id"]
            ]
        )->update();
    }

    public function updatePassword($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `managers` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $params["id"]
            ]
        )->update();
    }

    public function deleteAccount($params = null)
    {
        return $this->db->run("DELETE FROM `managers` WHERE id = ?", [$params["id"]])->delete();
    }

    public function createNotification($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "INSERT INTO `managers_notification_settings` (`manager_id`, `type`, `enabled`) VALUES (?, ?, ?)",
                [
                    $params["id"],
                    $notification["type"],
                    $notification["enabled"]
                ]
            )->insert();
        }
        return $added;
    }

    public function updateNotification($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "UPDATE `managers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `manager_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $params["id"]
                ]
            )->insert();
        }
        return $added;
    }
}
