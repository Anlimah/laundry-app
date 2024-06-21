<?php

namespace Controller;

use Core\Request;
use Core\Database;

class Branch
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
    }

    public function getAll()
    {
        $query = "SELECT * FROM Branches";
        return $this->db->run($query)->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM `Branches` WHERE `id` = :id";
        $params = array("id" => $id);
        return $this->db->run($query, $params)->fetchOne();
    }

    public function create($data)
    {
        $data = Request::getBody();
        $query = "INSERT INTO `branches`
        (`name`, `address`, `latitude`, `longitude`, `phone_number`, `email_address`) 
        VALUES (:name, :address, :latitude, :longitude, :phone_number, :email_address)";
        $params = array(
            "name" => $data['name'],
            "address" => $data['address'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude'],
            "phone_number" => $data['phone_number'],
            "email_address" => $data['email_address']
        );
        return $this->db->run($query, $params)->insert();
    }

    public function edit($id, $data)
    {
        $data = Request::getBody();
        $query = "UPDATE `branches` SET 
        `name` = :name, `address` = :address, `latitude` = :latitude, `longitude` = :longitude, 
        `phone_number` = :phone_number, `email_address` = :email_address WHERE `id` = :id";
        $params = array(
            "name" => $data['name'],
            "address" => $data['address'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude'],
            "phone_number" => $data['phone_number'],
            "email_address" => $data['email_address'],
            "id" => $id
        );
        return $this->db->run($query, $params)->update();
    }

    public function remove($id)
    {
        $query = "DELETE FROM `branches` WHERE `id` = :id";
        $params = array("id" => $id);
        return $this->db->run($query, $params)->delete();
    }
}
