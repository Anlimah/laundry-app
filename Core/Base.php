<?php

namespace Core;

use Core\Response;
use Core\Status;

final class Base
{
    public static function dd($data): mixed
    {
        var_dump($data);
        exit();
    }

    public static function base_path($file): mixed
    {
        return ROOT_DIR . $file;
    }

    // Helper function to get JSON input data
    public static function getInputData()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    // Function to handle the request
    public static function handleRequest($class, $method, $id)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = array("data" => $class->get($id));
                    die(Response::json(Status::$HTTP_200_OK, $response));
                } else {
                    die(json_encode($class->getAll()));
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $data = Base::getInputData();
                die(json_encode($class->create($data)));
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $data = Base::getInputData();
                die(json_encode($class->update($id, $data)));
                break;
            case 'DELETE':
                die(json_encode($class->delete($id)));
                break;
            default:
                http_response_code(405);
                die(json_encode(['message' => 'Method not allowed']));
        }
    }
}
