<?php
require 'vendor/autoload.php';

use Core\Container;
use Core\App;
use Core\Database;

use Dotenv\Dotenv;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

define("ROOT_DIR", dirname(__FILE__, 1));

$container = new Container();
App::setContainer($container);

$container = Container::bind(Database::class, function () {
    return new Database("root", "");
});

define('DB', App::getContainer()->resolve(Database::class));
