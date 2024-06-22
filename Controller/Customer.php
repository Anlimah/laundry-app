<?php

namespace Controller;

use Core\Database;
use Core\Request;

class Customer
{
    private $db;

    public function __construct($config)
    {
        $this->db = new Database($config);
    }

    public function getAll($data = [])
    {
        return $this->db->run("SELECT * FROM `customers`")->fetchAll();
    }

    public function getById($data = [])
    {
        return $this->db->run("SELECT * FROM `customers` WHERE `id` = ?", [$data["id"]])->fetchOne();
    }

    public function createAccount($user_id, $data = [])
    {
        return $this->db->run(
            "INSERT INTO `customers` (`user_id`, `first_name`, `last_name`, `phone_number`) VALUES (?, ?, ?, ?)",
            [
                $user_id,
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"]
            ]
        )->insert(true, null);
    }

    public function updateAccount($data = [])
    {
        return $this->db->run(
            "UPDATE `customers` SET 
            `first_name` = ?, `last_name` = ?, `phone_number` = ?, `address` = ?
            WHERE id = ?",
            [
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"],
                $data["id"]
            ]
        )->update();
    }

    public function deleteAccount($data = [])
    {
        return $this->db->run("DELETE FROM `customers` WHERE id = ?", [$data["id"]])->delete();
    }

    public function createNotification($data = [])
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "INSERT INTO `customers_notification_settings` (`customer_id`, `type`, `enabled`) VALUES (?, ?, ?)",
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
                "UPDATE `customers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `customer_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $data["id"]
                ]
            )->insert();
        }
        return $added;
    }

    public function addPreferences($data = [])
    {
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->db->run(
                "UPDATE `customers_preferences` SET `setting_name` = ?, `setting_value` = ? WHERE `customer_id` = ?",
                [
                    $preference["name"],
                    $preference["value"],
                    $data["id"]
                ]
            )->insert();
        }
        return $added;
    }

    public function updatePreferences($data = [])
    {
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->db->run(
                "UPDATE `customers_preferences` SET `setting_value` = ? WHERE setting_name` = ? AND `customer_id` = ?",
                [
                    $preference["value"],
                    $preference["name"],
                    $data["id"]
                ]
            )->insert();
        }
        return $added;
    }
}
