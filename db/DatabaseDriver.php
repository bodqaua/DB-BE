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

    public function getDatabases()
    {
        return $this->PDO->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUsers() {
        return $this->PDO->query('SELECT user FROM mysql.user')->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDatabaseColumns($database)
    {
        $query = $this->PDO->query('SHOW TABLES FROM ' . $database);
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getColumnsDescription($database, $column)
    {
        $this->PDO->exec("use " . $database);
        $query = $this->PDO->query('SHOW COLUMNS FROM ' . $column);
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDataFromColumn($database, $column)
    {
        $this->PDO->exec("use " . $database);
        $query = $this->PDO->query('SELECT * FROM ' . $column);
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDatabase($databaseName)
    {
        $query = $this->PDO->exec('CREATE DATABASE ' . $databaseName);
        $this->throwQueryErrorIfExists($query);

    }

    public function dropDatabase($databaseName)
    {
        $query = $this->PDO->query('DROP DATABASE ' . $databaseName);
        $this->throwQueryErrorIfExists($query);
    }

    public function getTablesLength() {
        $query = $this->PDO->query('SELECT COUNT(*) FROM information_schema.SCHEMATA');
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $password, $isAdmin) {
        if ($isAdmin) {
            $query = $this->PDO->query("CREATE USER '$username'@'%' IDENTIFIED BY '$password';GRANT ALL PRIVILEGES ON *.* TO '$username'@'%'");
        } else {
            $query = $this->PDO->query("CREATE USER '$username'@'%' IDENTIFIED BY '$password';GRANT SELECT ON *.* TO '$username'@'%'");
        }
        $this->throwQueryErrorIfExists($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTable($database, $table, $fields)
    {
        $this->PDO->exec("use " . $database);
        $tableQuery = $this->generateTableQuery($fields);
        $queryString = "CREATE TABLE " . "`" . $database . "`." . "`" . $table . "`" . "(" . $tableQuery . " PRIMARY KEY (`id`)) ENGINE = InnoDB";
        $query = $this->PDO->exec($queryString);
        $this->throwQueryErrorIfExists($query);
    }

    public function dropTable($database, $table)
    {
        $this->PDO->exec("use " . $database);
        $queryString = "DROP TABLE " . $table;
        $query = $this->PDO->exec($queryString);
        $this->throwQueryErrorIfExists($query);
    }

    public function insert($database, $table, $data)
    {
        $this->PDO->exec("use " . $database);
        $queryString = $this->generateInsert($table, $data);
        $query = $this->PDO->exec($queryString);
        $this->throwQueryErrorIfExists($query);
    }

    public function delete($database, $table, $id)
    {
        $this->PDO->exec("use " . $database);
        $queryString = "DELETE FROM " . $table . " WHERE id = ". $id;
        $query = $this->PDO->exec($queryString);
        $this->throwQueryErrorIfExists($query);
    }

    private function generateTableQuery($fields)
    {
        $query = "";
        foreach ($fields as $field) {
            $array = [];
            array_push($array, "`" . $field['name'] . "`");
            array_push($array, $field['type']);
            array_push($array, "NOT NULL");
            array_push($array, $field['autoIncrement'] ? 'AUTO_INCREMENT' : '');
            $query .= implode(" ", $array) . ",";
        }

        return trim($query);
    }

    private function generateInsert($table, $data)
    {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            array_push($keys, "`" . $key . "`");
            array_push($values, "'" . $value . "'");
        }

        return "INSERT INTO `" . $table . "` (" . $this->innerImplode($keys, ",", ",")  . ") VALUES (" . $this->innerImplode($values, ",", ",")  . ")";
    }

    private function innerImplode($array, $separator, $trim)
    {
        return trim(implode($separator, $array), $trim);
    }

    private function throwQueryErrorIfExists($query)
    {
        if (!$query && $this->PDO->errorInfo()[2]) {
            Serializer::Error(400, $this->PDO->errorInfo()[2]);
        }
    }

    public function toJson($data)
    {
        $result = [];
        foreach ($data as $d) {
            array_push($result, ['name' => $d]);
        }
        return $result;
    }

}