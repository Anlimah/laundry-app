<?php

namespace Controller;

use Core\Database;

class Tracking
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
    }

    public function getTrackings()
    {
        return $this->db->run("SELECT * FROM Tracking")->fetchAll();
    }

    public function getTracking($id)
    {
        return $this->db->run("SELECT * FROM Tracking WHERE id = ?", [$id])->fetchOne();
    }

    public function createTracking($data)
    {
        return $this->db->run(
            "INSERT INTO Tracking (request_id, driver_id, location, timestamp) VALUES (?, ?, ?, ?)",
            [$data['request_id'], $data['driver_id'], $data['location'], $data['timestamp']]
        )->insert();
    }

    public function updateTracking($id, $data)
    {
        return $this->db->run(
            "UPDATE Tracking SET request_id = ?, driver_id = ?, location = ?, timestamp = ? WHERE id = ?",
            [$data['request_id'], $data['driver_id'], $data['location'], $data['timestamp'], $id]
        )->update();
    }

    public function deleteTracking($id)
    {
        return $this->db->run("DELETE FROM Tracking WHERE id = ?", [$id])->delete();
    }
}
