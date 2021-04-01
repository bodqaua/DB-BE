<?php


class Serializer
{
    public static function json($data) {
        header(http_response_code(200));
        echo json_encode($data);
    }


    public static function errorResponse($status, $message) {
        header(http_response_code($status));
        echo json_encode([
            "status" => $status,
            "message" => $message
        ]);
    }
}