<?php
session_start();

require 'bootstrap.php';

use Core\Response;
use Auth\Auth;

header('Content-Type: application/json');

// Enforce HTTPS
// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
//     http_response_code(403);
//     echo Response::json(400, "HTTPS is required");
//     exit;
// }

// Implement Rate Limiting (simple example)
rateLimit();

// AltoRouter setup
$router = new AltoRouter();

$router->map('POST', '/laundry-app/auth/register', 'Auth\Auth#register');
$router->map('POST', '/laundry-app/auth/login', 'Auth\Auth#login');

// Authenticate middleware
$router->map('GET|POST|PUT|DELETE', '/laundry-app/.*', 'Auth\Auth#authenticate');

// Only authenticated users of the system can access endpoints below
$router->map('GET', '/laundry-app/users/logout', 'Controller\User#logout');

$router->map('GET', '/laundry-app/branch', 'Controller\Branch#getAll');
$router->map('GET', '/laundry-app/branch/[i:id]', 'Controller\Branch#getById');
$router->map('POST', '/laundry-app/branch', 'Controller\Branch#createBranch');
$router->map('PUT', '/laundry-app/branch/[i:id]', 'Controller\Branch#updateBranch');

$match = $router->match();

if ($match) {
    list($controller, $method) = explode('#', $match['target']);
    $params = $match['params'];

    // Handle input data for POST, PUT, DELETE requests
    $request_data = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $request_data = json_decode(file_get_contents('php://input'), true);
        $request_data = $request_data === null ? [] : $request_data;
    }

    // Include query parameters for GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $request_data = $_GET;
    }

    // Combine request data and URL parameters
    $all_params = array_merge($params, $request_data);

    // Validate parameters
    $validation_errors = validateParams($all_params, $controller, $method);
    if (!empty($validation_errors)) {
        echo Response::json(400, "Invalid parameters", $validation_errors);
        exit;
    }

    // Instantiate the controller with necessary dependencies
    $db_config = require("config/database.php");
    $controller_instance = new $controller($db_config["database"]["mysql"]);

    // Check if the method exists and is callable
    if (!method_exists($controller_instance, $method) || !is_callable([$controller_instance, $method])) {
        echo Response::json(405, "Method Not Allowed", ["error" => "The method $method is not allowed for this controller."]);
        exit;
    }

    try {
        $response = call_user_func_array([$controller_instance, $method], [$all_params]);
        echo Response::json($response["status_code"], $response["message"], $response["data"]);
    } catch (Exception $e) {
        // Log the error
        error_log($e->getMessage());

        // Return a generic error message to the client
        echo Response::json(500, "Internal Server Error", ["error" => "An unexpected error occurred. Please try again later."]);
    }
} else {
    http_response_code(404);
    echo json_encode(["status_code" => 404, "message" => "Not Found"]);
}

// Example validation function
function validateParams($params, $controller, $method)
{
    $errors = [];
    // Define required parameters for each controller method
    $required_params = [
        'Auth\Auth' => [
            'register' => ['email', 'password'],
            'login' => ['email', 'password']
        ],
        'Controller\Branch' => [
            'createBranch' => ['name', 'location'],
            'updateBranch' => ['id', 'name', 'location']
        ]
        // Add other controllers and methods as needed
    ];

    // Check if the controller and method have defined required parameters
    if (isset($required_params[$controller][$method])) {
        $method_params = $required_params[$controller][$method];
        foreach ($method_params as $param) {
            if (!isset($params[$param])) {
                $errors[] = "Missing required parameter: $param";
            }
        }
    }

    return $errors;
}

// Simple rate limiting function
function rateLimit()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = time();

    // Simple in-memory storage for rate limiting (replace with a persistent storage in production)
    static $rateLimitData = [];

    if (!isset($rateLimitData[$ip])) {
        $rateLimitData[$ip] = ['count' => 0, 'last_request_time' => $time];
    }

    $rateLimitData[$ip]['count']++;
    $elapsed_time = $time - $rateLimitData[$ip]['last_request_time'];

    // Allow a maximum of 100 requests per minute
    if ($elapsed_time < 60 && $rateLimitData[$ip]['count'] > 100) {
        http_response_code(429);
        echo json_encode(['status_code' => 429, 'message' => 'Too Many Requests']);
        exit;
    }

    // Reset count and last_request_time every minute
    if ($elapsed_time >= 60) {
        $rateLimitData[$ip]['count'] = 1;
        $rateLimitData[$ip]['last_request_time'] = $time;
    }
}
