<?php
require_once('./db/DatabaseDriver.php');
require_once('./controllers/Serializer.php');
require_once('./router/Router.php');

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

$routes = [
    "databases" => "App::FetchDatabases",
    "database/:dbName" => "App::FetchDatabaseByName",
    "database/:database/:column" => "App::getDataFromColumn",
];

$router = new Router($routes);
$router->run();
