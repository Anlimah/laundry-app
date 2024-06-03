<?php

namespace Src\Controller;

use Core\Database;

class Vehicle extends Database
{
    public function getAll()
    {
        return $this->run("SELECT * FROM Vehicle")->fetchAll();
    }

    public function getById($id)
    {
        return $this->run("SELECT * FROM Vehicles WHERE `id` = ?", [$id])->fetchOne();
    }

    public function getByNumber($number)
    {
        return $this->run("SELECT * FROM Vehicles WHERE `number` = ?", [$number])->fetchOne();
    }

    public function getByBranchId($id)
    {
        return $this->run("SELECT * FROM Vehicles WHERE `branch_id` = ?", [$id])->fetchOne();
    }

    public function add($data)
    {
        return $this->run(
            "INSERT INTO Vehicles (`name`, `unit_cost`) VALUES (?, ?)",
            [$data['name'], $data['unit_cost']]
        )->insert();
    }

    public function edit($id, $data)
    {
        return $this->run(
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
        return $this->run("DELETE FROM Vehicle WHERE id = ?", [$id])->delete();
    }
}
