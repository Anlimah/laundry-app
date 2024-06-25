<?php
session_start();

require 'bootstrap.php';

use Controller\User;
use Controller\Customer;
use Controller\Driver;
use Controller\Manager;
use Controller\PickupRequest;
use Controller\LaundryItem;
use Controller\Invoice;
use Controller\DeliveryRequest;
use Controller\Payment;
use Controller\Notification;
use Controller\Tracking;
use Controller\Branch;
use Controller\Vehicle;
use Controller\Item;
use Core\Response;
use Auth\Auth;

header('Content-Type: application/json');

$router = new AltoRouter();

$router->map('POST', '/laundry-app/auth/register', 'Auth\Auth', 'register');
$router->map('POST', '/laundry-app/auth/login', 'Auth\Auth', 'login');

// Authenticate middleware
$router->map('GET|POST|PUT|DELETE', '/laundry-app/.*', 'Auth\Auth', 'authenticate');

// Only authenticated users of the system can access endpoints below
$router->map('GET', '/laundry-app/users/logout', 'Controller\User', 'logout');

$router->map('GET', '/laundry-app/branch', 'Controller\Branch', 'getAll');
$router->map('GET', '/laundry-app/branch/[i:id]', 'Controller\Branch', 'getById');
$router->map('POST', '/laundry-app/branch', 'Controller\Branch', 'createBranch');
$router->map('PUT', '/laundry-app/branch/[i:id]', 'Controller\Branch', 'updateBranch');

$match = $router->match();

if ($match) {
    $controller = $match['target'];
    $function = $match['name'];
    $params = $match['params'];
    $request_data = json_decode(file_get_contents('php://input'), true);
    $db_config = require("config/database.php");
    $instance = new $controller($db_config["database"]["mysql"]);
    $response = $instance->$function($request_data);
    die(Response::json($response["status_code"], $response["message"], $response["data"]));
} else {
    http_response_code(404);
}
