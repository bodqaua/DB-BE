<?php
require_once('DatabaseDriver.php');
require_once('Serializer.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    return;
}

$user = $_POST['username'];
$pass = $_POST['password'];
$database = $_POST['database'];
$column = $_POST['column'];

$PDO = new DatabaseDriver($user, $pass);
//$PDO->getDatabases();
//$PDO->getDatabaseColumns($database);
$PDO->getDataFromColumn($database, $column);
