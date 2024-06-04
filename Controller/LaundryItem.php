<?php

namespace Controller;

class LaundryItem
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllLaundryItems()
    {
        return $this->db->run("SELECT * FROM LaundryItems")->fetchAll();
    }

    public function getLaundryItem($id)
    {
        return $this->db->run(
            "SELECT * FROM LaundryItems WHERE id = ?",
            [$id]
        )->fetchOne();
    }

    public function createLaundryItem($data)
    {
        $query = "INSERT INTO LaundryItems (invoice_id, item_id, quantity, total) VALUES (?, ?, ?, ?)";
        $params = [
            $data['invoice_id'],
            $data['item_id'],
            $data['quantity'],
            $data['total']
        ];
        return $this->db->run($query, $params)->insert();
    }

    public function updateLaundryItem($id, $data)
    {
        $query = "UPDATE LaundryItems SET invoice_id = ?, item_id = ?, quantity = ?, total = ? WHERE id = ?";
        $params = [
            $data['invoice_id'],
            $data['item_id'],
            $data['quantity'],
            $data['total'],
            $id
        ];
        return $this->db->run($query, $params)->update();
    }

    public function deleteLaundryItem($id)
    {
        return $this->db->run(
            "DELETE FROM LaundryItems WHERE id = ?",
            [$id]
        )->execute();
    }
}
