<?php

namespace Controller;

use Core\Database;
use Core\Request;

class Customer
{
    private $db;

    public function __construct($username, $password)
    {
        $this->db = new Database($username, $password);
    }

    public function getAll($params = null)
    {
        return $this->db->run("SELECT * FROM `customers`")->fetchAll();
    }

    public function getById($params = null)
    {
        return $this->db->run("SELECT * FROM `customers` WHERE `id` = ?", [$params["id"]])->fetchOne();
    }

    public function createCustomer($data = [])
    {
        return $this->db->run(
            "INSERT INTO `customers` 
            (`first_name`, `last_name`, `phone_number`, `address`) 
            VALUES (?, ?, ?, ?)",
            [
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"]
            ]
        )->insert();
    }

    public function updateCustomer($params = null)
    {
        $data = Request::getBody();
        return $this->db->run(
            "UPDATE `customers` SET 
            `first_name` = ?, `last_name` = ?, `phone_number` = ?, `address` = ?
            WHERE id = ?",
            [
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"],
                $params["id"]
            ]
        )->update();
    }

    public function deleteCustomer($params = null)
    {
        return $this->db->run("DELETE FROM `customers` WHERE id = ?", [$params["id"]])->delete();
    }

    public function createNotification($params = null)
    {
        $data = Request::getBody();
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->db->run(
                "INSERT INTO `customers_notification_settings` (`customer_id`, `type`, `enabled`) VALUES (?, ?, ?)",
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
                "UPDATE `customers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `customer_id` = ?",
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
                "UPDATE `customers_preferences` SET `setting_name` = ?, `setting_value` = ? WHERE `customer_id` = ?",
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
                "UPDATE `customers_preferences` SET `setting_value` = ? WHERE setting_name` = ? AND `customer_id` = ?",
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
