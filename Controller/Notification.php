<?php

namespace Src\Controller;

class Notification
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getNotifications()
    {
        return $this->db->run("SELECT * FROM Notifications")->fetchAll();
    }

    public function getNotification($id)
    {
        return $this->db->run("SELECT * FROM Notifications WHERE id = ?", [$id])->fetchOne();
    }

    public function createNotification($data)
    {
        $query = "INSERT INTO Notifications (user_id, user_type, message, is_read) VALUES (?, ?, ?, ?)";
        $params = [
            $data['user_id'],
            $data['user_type'],
            $data['message'],
            $data['is_read']
        ];
        return $this->db->run($query, $params)->insert();
    }

    public function updateNotification($id, $data)
    {
        $query = "UPDATE Notifications SET user_id = ?, user_type = ?, message = ?, is_read = ? WHERE id = ?";
        $params = [
            $data['user_id'],
            $data['user_type'],
            $data['message'],
            $data['is_read'],
            $id
        ];
        return $this->db->run($query, $params)->update();
    }

    public function deleteNotification($id)
    {
        return $this->db->run("DELETE FROM Notifications WHERE id = ?", [$id])->delete();
    }
}
