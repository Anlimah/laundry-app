<?php

namespace Controller;

use Core\Database;

class Driver extends Database
{
    public function getAll()
    {
        return $this->run("SELECT * FROM `drivers`")->fetchAll();
    }

    public function getById($id)
    {
        return $this->run("SELECT * FROM `drivers` WHERE `id` = ?", [$id])->fetchOne();
    }

    public function getByBranchId($id)
    {
        return $this->run("SELECT * FROM `drivers` WHERE `branch_id` = ?", [$id])->fetchOne();
    }

    public function createAccount($data)
    {
        return $this->run(
            "INSERT INTO `drivers` 
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

    public function updateAccount($id, $data)
    {
        return $this->run(
            "UPDATE `drivers` SET 
            `first_name` = ?, `last_name` = ?, `phone_number` = ?, `address` = ?, `email_address` = ?
            WHERE id = ?",
            [
                $data["first_name"],
                $data["last_name"],
                $data["phone_number"],
                $data["address"],
                $data["email_address"],
                $id
            ]
        )->update();
    }

    public function updatePassword($id, $data)
    {
        return $this->run(
            "UPDATE `drivers` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $id
            ]
        )->update();
    }

    public function deleteAccount($id)
    {
        return $this->run("DELETE FROM `drivers` WHERE id = ?", [$id])->delete();
    }

    public function createNotification($id, $data)
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->run(
                "INSERT INTO `drivers_notification_settings` (`driver_id`, `type`, `enabled`) VALUES (?, ?, ?)",
                [
                    $id,
                    $notification["type"],
                    $notification["enabled"]
                ]
            )->insert();
        }
        return $added;
    }

    public function updateNotification($id, $data)
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->run(
                "UPDATE `drivers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `driver_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $id
                ]
            )->insert();
        }
        return $added;
    }

    public function addPreferences($id, $data)
    {
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->run(
                "UPDATE `drivers_preferences` SET `setting_name` = ?, `setting_value` = ? WHERE `driver_id` = ?",
                [
                    $preference["name"],
                    $preference["value"],
                    $id
                ]
            )->insert();
        }
        return $added;
    }

    public function updatePreferences($id, $data)
    {
        $added = 0;
        foreach ($data as $preference) {
            $added += $this->run(
                "UPDATE `drivers_preferences` SET `setting_value` = ? WHERE setting_name` = ? AND `driver_id` = ?",
                [
                    $preference["value"],
                    $preference["name"],
                    $id
                ]
            )->insert();
        }
        return $added;
    }
}
