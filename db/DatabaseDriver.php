<?php


class DatabaseDriver
{
    private $host = '127.0.0.1';
    private $user;
    private $pass;
    private $charset = 'utf8mb4';
    private $PDO;

    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->connect();
    }

    public function connect()
    {
        $dsn = "mysql:host=$this->host;charset=$this->charset";

        try {
            $this->PDO = new PDO($dsn, $this->user, $this->pass);
        } catch (\PDOException $e) {
            Serializer::Error(401, "Invalid credentials");
        }
    }

    public function getDatabases() {
        return $this->PDO->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDatabaseColumns($database) {
        $query = $this->PDO->query('SHOW TABLES FROM ' . $database);
        if (!$query) {
            Serializer::Error(400, 'Incorrect database name');
        }
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDataFromColumn($database, $column) {
        $this->PDO->query("use " . $database);
        $query = $this->PDO->query('SELECT * FROM ' . $column);
        if (!$query) {
            Serializer::Error(400, 'Invalid data');
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}