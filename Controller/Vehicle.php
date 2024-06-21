<?php

namespace Controller;

use Core\Database;

class Vehicle extends Database
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
    }

    public function getAll()
    {
        return $this->db->run("SELECT * FROM Vehicle")->fetchAll();
    }

    public function getById($id)
    {
        return $this->db->run("SELECT * FROM Vehicles WHERE `id` = ?", [$id])->fetchOne();
    }

    public function getByNumber($number)
    {
        return $this->db->run("SELECT * FROM Vehicles WHERE `number` = ?", [$number])->fetchOne();
    }

    public function getByBranchId($id)
    {
        return $this->db->run("SELECT * FROM Vehicles WHERE `branch_id` = ?", [$id])->fetchOne();
    }

    public function add($data)
    {
        return $this->db->run(
            "INSERT INTO Vehicles (`name`, `unit_cost`) VALUES (?, ?)",
            [$data['name'], $data['unit_cost']]
        )->insert();
    }

    public function edit($id, $data)
    {
        return $this->db->run(
            "UPDATE Vehicles SET `name` = ?, `unit_cost` = ? WHERE id = ?",
            [
                $data['name'],
                $data['unit_cost'],
                $id
            ]
        )->update();
    }

    public function remove($id)
    {
        return $this->db->run("DELETE FROM Vehicle WHERE id = ?", [$id])->delete();
    }
}
