<?php

namespace Controller;

class Status
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getStatuses()
    {
        return $this->db->query("SELECT * FROM Statuses")->fetchAll();
    }

    public function getStatus($id)
    {
        return $this->db->run("SELECT * FROM Statuses WHERE id = ?", [$id])->fetchOne();
    }

    public function createStatus($data)
    {
        return $this->db->run(
            "INSERT INTO Statuses (name, description) VALUES (?, ?)",
            [$data['name'], $data['description']]
        )->insert();
    }

    public function updateStatus($id, $data)
    {
        return $this->db->run(
            "UPDATE Statuses SET name = ?, description = ? WHERE id = ?",
            [$data['name'], $data['description'], $id]
        )->update();
    }

    public function deleteStatus($id)
    {
        return $this->db->run("DELETE FROM Statuses WHERE id = ?", [$id])->delete();
    }
}
