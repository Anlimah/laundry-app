<?php

namespace Controller;

use Core\Database;
use Core\Request;

class Driver
{
    private $db;

    public function __construct($config)
    {
        $this->db = new Database($config);
    }

    public function getAll($params = null)
    {
        return $this->db->run("SELECT * FROM `drivers`")->fetchAll();
    }

    public function getById($params = null)
    {
        return $this->db->run("SELECT * FROM `drivers` WHERE `id` = ?", [$params["id"]])->fetchOne();
    }

    public function getByBranchId($params = null)
    {
        return $this->db->run("SELECT * FROM `drivers` WHERE `branch_id` = ?", [$params["id"]])->fetchOne();
    }

    public function createAccount($user_id, $data = [])
    {
        return $this->db->run(
            "INSERT INTO `drivers` (`user_id`, `branch_id`, `first_name`, `last_name`, `phone_number`) VALUES (?, ?, ?, ?, ?)",
            [
                $user_id,
                $data["branch_id"],
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"]
            ]
        )->insert(true, null);
    }

    public function updateAccount($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `drivers` SET 
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
            "UPDATE `drivers` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $params["id"]
            ]
        )->update();
    }

    public function deleteAccount($params = null)
    {
        return $this->db->run("DELETE FROM `drivers` WHERE id = ?", [$params["id"]])->delete();
    }

    public function createNotification($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "INSERT INTO `drivers_notification_settings` (`driver_id`, `type`, `enabled`) VALUES (?, ?, ?)",
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
                "UPDATE `drivers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `driver_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $params["id"]
                ]
            )->insert();
        }
        return $added;
    }

    public function addPreferences($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->db->run(
                "UPDATE `drivers_preferences` SET `setting_name` = ?, `setting_value` = ? WHERE `driver_id` = ?",
                [
                    $preference["name"],
                    $preference["value"],
                    $params["id"]
                ]
            )->insert();
        }
        return $added;
    }

    public function updatePreferences($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->db->run(
                "UPDATE `drivers_preferences` SET `setting_value` = ? WHERE setting_name` = ? AND `driver_id` = ?",
                [
                    $preference["value"],
                    $preference["name"],
                    $params["id"]
                ]
            )->insert();
        }
        return $added;
    }
}
