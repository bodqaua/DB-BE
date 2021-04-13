<?php


class App
{
    private static function Connect()
    {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        return new DatabaseDriver($user, $pass);
    }

    public static function Login()
    {
        self::Connect();
        Serializer::Json(["success" => true]);

    }

    public static function FetchDatabases()
    {
        $db = self::Connect();
        $databases = $db->toJson($db->getDatabases());
        Serializer::Json($databases);
    }

    public static function FetchDatabaseByName($params)
    {
        $db = self::Connect();
        $databases = $db->toJson($db->getDatabaseColumns($params['database']));
        Serializer::Json($databases);
    }

    public static function getTableColumns($params)
    {
        $db = self::Connect();
        $databases = $db->getColumnsDescription($params['database'], $params['column']);
        Serializer::Json($databases);
    }

    public static function getDataFromColumn($params)
    {
        $db = self::Connect();
        $databases = $db->getDataFromColumn($params['database'], $params['column']);
        Serializer::Json($databases);
    }

    public static function CreateDatabase($params)
    {
        $db = self::Connect();
        $db->createDatabase($params['database']);
        Serializer::Json($params['database'] . " was created");
    }

    public static function DropDatabase($params)
    {
        $db = self::Connect();
        $db->dropDatabase($params['database']);
        Serializer::Json($params['database'] . " has been deleted");
    }

    public static function CreateTable($params)
    {
        $db = self::Connect();
        $fields = $_POST['fields'];
        $db->createTable($params['database'], $params['table'], $fields);
        Serializer::Json("Table " . "`" . $params['table'] . "` was created");
    }

    public static function DropTable($params)
    {
        $db = self::Connect();
        $db->dropTable($params['database'], $params['table']);
        Serializer::Json("Table " . "`" . $params['table'] . "` has been deleted");
    }

    public static function insertIntoColumn($params)
    {
        $db = self::Connect();
        $data = $_POST['data'];
        $db->insert($params['database'], $params['table'], $data);
        Serializer::Json("Data has been inserted into " . $params['table']);
    }

    public static function deleteFromColumn($params)
    {
        $db = self::Connect();
        $id = $_POST['id'];
        $db->delete($params['database'], $params['table'], $id);
        Serializer::Json("Data has been deleted from " . $params['table']);
    }
}