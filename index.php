<?php
require 'bootstrap.php';

use Core\Base;
use Core\Router;

// Set the content type to application/json
header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'])["path"];
$method = $_SERVER['REQUEST_METHOD'];
$router = new Router();
$routes = require Base::base_path("/routes.php");
$router->route($uri, $method);
die($uri);

$router = new AltoRouter();

$router->map('GET', '/branch', 'Branch::getAll');
$router->map('GET', '/branch/{id}', 'Branch::getById');
$router->map('POST', '/branch/', 'Branch::getAll');
$router->map('PUT', '/branch/{id}', 'Branch::getAll');

$match = $router->match();

if ($match) {
    $controller = $match['target'];
    $params = $match['params'];
    $controller = new $controller();
    $controller->{$match['name']}($params['id']);
} else {
    // ... handle route not found ...
}
