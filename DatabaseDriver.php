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
            Serializer::errorResponse(401, "Invalid credentials");
        }
    }

    public function getDatabases() {
        $databases = $this->PDO->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        Serializer::json($databases);
    }

    public function getDatabaseColumns($database) {
        $query = $this->PDO->query('SHOW TABLES FROM ' . $database);
        if (!$query) {
            Serializer::errorResponse(400, 'Incorrect database name');
        }
        $tables = $query->fetchAll(PDO::FETCH_COLUMN);
        Serializer::json($tables);
    }

    public function getDataFromColumn($database, $column) {
        $this->PDO->query("use " . $database);
        $query = $this->PDO->query('SELECT * FROM ' . $column);
        if (!$query) {
            Serializer::errorResponse(400, 'Invalid data');
        }
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        Serializer::json($data);
    }
}