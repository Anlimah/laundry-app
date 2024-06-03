<?php

namespace Controller;

use Core\Database;

class Customer extends Database
{
    public function getAll()
    {
        return $this->run("SELECT * FROM `managers`")->fetchAll();
    }

    public function getById($id)
    {
        return $this->run("SELECT * FROM `managers` WHERE `id` = ?", [$id])->fetchOne();
    }

    public function getByBranchId($id)
    {
        return $this->run("SELECT * FROM `managers` WHERE `branch_id` = ?", [$id])->fetchOne();
    }

    public function createAccount($data)
    {
        return $this->run(
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

    public function updateAccount($id, $data)
    {
        return $this->run(
            "UPDATE `managers` SET 
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
            "UPDATE `managers` SET `password` = ? WHERE id = ?",
            [
                password_hash($data["password"], PASSWORD_BCRYPT),
                $id
            ]
        )->update();
    }

    public function deleteAccount($id)
    {
        return $this->run("DELETE FROM `managers` WHERE id = ?", [$id])->delete();
    }

    public function createNotification($id, $data)
    {
        $added = 0;
        foreach ($data as $notification) {
            $added += $this->run(
                "INSERT INTO `managers_notification_settings` (`manager_id`, `type`, `enabled`) VALUES (?, ?, ?)",
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
                "UPDATE `managers_notification_settings` SET `type` = ?, `enabled` = ? WHERE `manager_id` = ?",
                [
                    $notification["type"],
                    $notification["enabled"],
                    $id
                ]
            )->insert();
        }
        return $added;
    }
}
