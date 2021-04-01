<?php


class DatabaseDriver
{
    private $host = '127.0.0.1';
    private $user;
    private $pass;
    private $charset = 'utf8mb4';

    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
    }

    public function connect()
    {
        $dsn = "mysql:host=$this->host;charset=$this->charset";

        try {
            $pdo = new PDO($dsn, $this->user, $this->pass);
            Serializer::json("Connected");
        } catch (\PDOException $e) {
            Serializer::errorResponse(401, "Invalid credentials");
        }
    }
}