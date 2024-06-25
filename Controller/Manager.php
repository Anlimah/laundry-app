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

    public function getAll($data = [])
    {
        return $this->db->run("SELECT * FROM `managers`")->fetchAll();
    }

    public function getById($data = [])
    {
        return $this->db->run("SELECT * FROM `managers` WHERE `id` = ?", [$data["id"]])->fetchOne();
    }

    public function getByBranchId($data = [])
    {
        return $this->db->run("SELECT * FROM `managers` WHERE `branch_id` = ?", [$data["id"]])->fetchOne();
    }

    public function createAccount($user_id, $data = [])
    {
        return $this->db->run(
            "INSERT INTO `managers` (`user_id`, `branch_id`, `first_name`, `last_name`, `phone_number`) VALUES (?, ?, ?, ?, ?)",
            [
                $user_id,
                $data["branch_id"],
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"]
            ]
        )->insert(true, null);
    }

    public function updateAccount($data = [])
    {
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
                $data["id"]
            ]
        )->update();
    }

    public function updatePassword($data = [])
    {
        return $this->db->run(
            "UPDATE `managers` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $data["id"]
            ]
        )->update();
    }

    public function deleteAccount($data = [])
    {
        return $this->db->run("DELETE FROM `managers` WHERE id = ?", [$data["id"]])->delete();
    }

    public function createNotification($data = [])
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "INSERT INTO `managers_notification_settings` (`manager_id`, `type`, `enabled`) VALUES (?, ?, ?)",
                [
                    $data["id"],
                    $notification["type"],
                    $notification["enabled"]
                ]
            )->insert();
        }
        return $added;
    }

    public function updateNotification($data = [])
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "UPDATE `managers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `manager_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $data["id"]
                ]
            )->insert();
        }
        return $added;
    }
}
