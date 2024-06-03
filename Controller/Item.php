<?php

namespace Controller;

class Item
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getItems()
    {
        return $this->db->run(
            "SELECT * FROM Items"
        )->fetchAll();
    }

    public function getItem($id)
    {
        return $this->db->run(
            "SELECT * FROM Items WHERE id = ?",
            ["id" => $id]
        )->fetchOne();
    }

    public function createItem($data)
    {
        return $this->db->run(
            "INSERT INTO Items (name, unit_cost) VALUES (?, ?)",
            [$data['name'], $data['unit_cost']]
        )->insert();
    }

    public function updateItem($id, $data)
    {
        return $this->db->run(
            "UPDATE Items SET name = ?, unit_cost = ? WHERE id = ?",
            [
                $data['name'],
                $data['unit_cost'],
                $id
            ]
        )->update();
    }

    public function deleteItem($id)
    {
        return $this->db->run(
            "DELETE FROM Items WHERE id = ?",
            [$id]
        )->delete();
    }
}
