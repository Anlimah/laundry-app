<?php

namespace Controller;

class DeliveryRequest
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM DeliveryRequests";
        return $this->db->run($query)->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM DeliveryRequests WHERE id = :id";
        $params = array(":id" => $id);
        return $this->db->run($query, $params)->fetchOne();
    }

    public function createDeliveryRequest($data)
    {
        $query = "INSERT INTO DeliveryRequests 
        (`customer_id`, `branch_id`, `delivery_date`, `delivery_time`, `location`, `payment_mode`, `status`) 
        VALUES(:customer_id, :branch_id, :delivery_date, :delivery_time, :location, :payment_mode, :status)";
        $params = array(
            "customer_id" => $data['customer_id'],
            "branch_id" => $data['branch_id'],
            "delivery_date" => $data['delivery_date'],
            "delivery_time" => $data['delivery_time'],
            "location" => $data['location'],
            "payment_mode" => $data['payment_mode'],
            "status" => $data['status']
        );
        return $this->db->run($query, $params)->insert();
    }

    public function updateDeliveryRequest($id, $data)
    {
        $query = "UPDATE DeliveryRequests 
        SET customer_id = ?, branch_id = ?, delivery_date = ?, delivery_time = ?, location = ?, payment_mode = ?, status = ? 
        WHERE id = ?";
        $params = [
            $data['customer_id'],
            $data['branch_id'],
            $data['delivery_date'],
            $data['delivery_time'],
            $data['location'],
            $data['payment_mode'],
            $data['status'],
            $id
        ];
        return $this->db->run($query, $params)->update();
    }

    public function deleteDeliveryRequest($id)
    {
        $query = "DELETE FROM DeliveryRequests WHERE id = :id";
        $params = array(":id" => $id);
        return $this->db->run($query, $params)->delete();
    }
}
