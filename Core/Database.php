<?php

namespace Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private $conn = null;
    private $query;
    private $params;
    private $stmt;

    public function __construct($user, $pass, $dbServer = "mysql")
    {
        $config = array(
            "host" => "127.0.0.1",
            "port" => "3306",
            "charset" => "utf8mb4",
            "dbname" => "laundry_app"
        );
        $dsn = "{$dbServer}:" . http_build_query($config, "", ";");
        $options = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function run($query, $params = array()): mixed
    {
        $this->query = $query;
        $this->params = $params;
        try {
            $this->stmt = $this->conn->prepare($this->query);
            $this->stmt->execute($this->params);
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function type(): mixed
    {
        return explode(' ', $this->query)[0];
    }

    public function fetchAll()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetchAll();
    }

    public function fetchOne()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetch();
    }

    public function insert($autoIncrementColumn = null, $primaryKeyValue = null)
    {
        if ($this->type() == 'INSERT') {
            if ($autoIncrementColumn) return $this->conn->lastInsertId($autoIncrementColumn);
            else if ($primaryKeyValue) return $primaryKeyValue;
            else return true;
        }
        return false;
    }

    public function delete()
    {
        if ($this->type() == 'DELETE') return $this->stmt->rowCount();
        return false;
    }

    public function update()
    {
        if ($this->type() == 'UPDATE') return $this->stmt->rowCount();
        return false;
    }
}
