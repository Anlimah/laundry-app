<?php
require 'bootstrap.php';

use Controller\User;
use Controller\Branch;
use Controller\Customer;
use Controller\Driver;
use Controller\Item;
use Controller\DeliveryRequest;
use Controller\Invoice;
use Controller\LaundryItem;
use Controller\Notification;
use Controller\Payment;
use Controller\PickupRequest;
use Controller\Manager;
use Controller\Status;
use Controller\Tracking;
use Controller\Vehicle;

use Core\Container;
use Core\App;
use Core\Database;

header('Content-Type: application/json');
$router = new AltoRouter();

$router->map('POST', '/laundry-app/user/register', 'Controller\User', 'register');
$router->map('POST', '/laundry-app/user/login', 'Controller\User', 'login');

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

    $cContainer = Container::bind($controller, function ($controller, $function, $params) {
        $instance = new $controller(DB);
        return $instance->$function($params);
    });

    App::setContainer($cContainer);
    $response = App::getContainer()->resolve($controller, [$controller, $function, $params]);

    die($response);
} else {
    http_response_code(404);
}
