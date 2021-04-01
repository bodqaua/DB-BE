<?php


class App
{
    public static function FetchDatabases() {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $db = new DatabaseDriver($user, $pass);
        $databases = $db->getDatabases();
        Serializer::Json($databases);
    }

    public static function FetchDatabaseByName($params) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $db = new DatabaseDriver($user, $pass);
        $databases = $db->getDatabaseColumns($params['database']);
        Serializer::Json($databases);
    }

    public static function getDataFromColumn($params) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $db = new DatabaseDriver($user, $pass);
        $databases = $db->getDataFromColumn($params['database'], $params['column']);
        Serializer::Json($databases);
    }
}