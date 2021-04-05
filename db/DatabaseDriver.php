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
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getColumnsDescription($database, $column) {
        $this->PDO->exec("use " . $database);
        $query = $this->PDO->query('SHOW COLUMNS FROM ' . $column);
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDataFromColumn($database, $column) {
        $this->PDO->exec("use " . $database);
        $query = $this->PDO->query('SELECT * FROM ' . $column);
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDatabase($databaseName) {
        $query = $this->PDO->exec('CREATE DATABASE ' . $databaseName);
        $this->throwQueryErrorIfExists($query);

    }

    public function dropDatabase($databaseName) {
        $query = $this->PDO->query('DROP DATABASE ' . $databaseName);
        $this->throwQueryErrorIfExists($query);

    }

    /*
     * CREATE TABLE `users`.`user`
     * ( `id` INT NOT NULL AUTO_INCREMENT ,
     * `name` TEXT NOT NULL ,
     * `email` INT NOT NULL ,
     * `last_login` INT NOT NULL ,
     * PRIMARY KEY (`id`)) ENGINE = InnoDB;
     */

    public function createTable($database, $table, $fields) {
        $tableQuery = $this->generateTableQuery($fields);
        $queryString = "CREATE TABLE " . "`" . $database . "`." . "`" . $table . "`" . "(" . $tableQuery . " PRIMARY KEY (`id`)) ENGINE = InnoDB";
        $query = $this->PDO->exec($queryString);
        $this->throwQueryErrorIfExists($query);

    }

    private function generateTableQuery($fields) {
        $query = "";
        foreach ($fields as $field) {
            $array = [];
            array_push($array, "`" .  $field['key'] . "`");
            array_push($array, $field['type']);
            array_push($array, "NOT NULL");
            array_push($array, $field['ai'] ? 'AUTO_INCREMENT' : '');
            $query .= implode(" ", $array) . ",";
        }

        return trim($query);
    }

    private function throwQueryErrorIfExists($query) {
        if (!$query && $this->PDO->errorInfo()[2]) {
            Serializer::Error(400, $this->PDO->errorInfo()[2]);
        }
    }

    public function toJson($data) {
        $result = [];
        foreach ($data as $d) {
            array_push($result, ['name' => $d]);
        }
        return $result;
    }
}