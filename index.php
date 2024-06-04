<?php
require 'bootstrap.php';

use Core\Base;

// Set the content type to application/json
header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'])["path"];
$method = $_SERVER['REQUEST_METHOD'];

$router = new AltoRouter();

$router->map('GET', '/laundry-app/branch', 'Branch#getAll');
$router->map('GET', '/laundry-app/branch/[i:id]', 'Branch#getById');
$router->map('POST', '/laundry-app/branch', 'Branch#createBranch');
$router->map('PUT', '/laundry-app/branch/[i:id]', 'Branch#updateBranch');

$match = $router->match();
die(json_encode($match));

if ($match) {
    $controller = $match['target'];
    $params = $match['params'];
    $controller = new $controller();
    $controller->{$match['name']}($params['id']);
} else {
    http_response_code(404);
}
