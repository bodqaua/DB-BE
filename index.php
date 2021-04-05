<?php
require_once('./db/DatabaseDriver.php');
require_once('./controllers/Serializer.php');
require_once('./router/Router.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-type: application/json');


$content_type_args = explode(';', $_SERVER['CONTENT_TYPE']);
if ($content_type_args[0] == 'application/json') {
    $_POST = json_decode(file_get_contents('php://input'),true);
}


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    return;
}

$routes = [
    "login" => "App::Login",
    "databases" => "App::FetchDatabases",
    "database/:database" => "App::FetchDatabaseByName",
    "database/:database/create/:table" => "App::CreateTable",
    "database/:database/delete/:table" => "App::DeleteTable",
    "database/create/:database" => "App::CreateDatabase",
    "database/delete/:database" => "App::DropDatabase",
    "database/:database/structure/:column" => "App::getTableColumns",
    "database/:database/:column" => "App::getDataFromColumn",
];

$router = new Router($routes);
$router->run();
