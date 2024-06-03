<?php

namespace Src\Controller;

class PickupRequest
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPickupRequests()
    {
        return $this->db->run("SELECT * FROM PickupRequests")->fetchAll();
    }

    public function getPickupRequest($id)
    {
        return $this->db->run("SELECT * FROM PickupRequests WHERE id = ?", [$id])->fetchOne();
    }

    public function getByUserId($user_id)
    {
        return $this->db->run('SELECT * FROM PickupRequests WHERE user_id = ?', [$user_id])->fetchAll();
    }

    public function createPickupRequest($data)
    {
        $query = "INSERT INTO PickupRequests 
        (customer_id, branch_id, service_type, pickup_date, pickup_time, location, cost, payment_mode, note, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['customer_id'],
            $data['branch_id'],
            $data['service_type'],
            $data['pickup_date'],
            $data['pickup_time'],
            $data['location'],
            $data['cost'],
            $data['payment_mode'],
            $data['note'],
            $data['status']
        ];
        return $this->db->run($query, $params)->insert();
    }

    public function updatePickupRequest($id, $data)
    {
        $query = "UPDATE PickupRequests 
        SET customer_id = ?, branch_id = ?, service_type = ?, pickup_date = ?, pickup_time = ?, 
        location = ?, cost = ?, payment_mode = ?, note = ?, status = ? WHERE id = ?";
        $params = [
            $data['customer_id'],
            $data['branch_id'],
            $data['service_type'],
            $data['pickup_date'],
            $data['pickup_time'],
            $data['location'],
            $data['cost'],
            $data['payment_mode'],
            $data['note'],
            $data['status'],
            $id
        ];
        return $this->db->run($query, $params)->update();
    }

    public function deletePickupRequest($id)
    {
        return $this->db->prepare("DELETE FROM PickupRequests WHERE id = ?", [$id])->delete();
    }
}
