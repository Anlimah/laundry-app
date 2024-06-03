<?php

namespace Core;

use Core\Base;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controller)
    {
        $this->routes[] = array("uri" => $uri, "controller" => $controller, "method" => $method);
    }

    public function get($uri, $controller): mixed
    {
        $this->add("GET", $uri, $controller);
    }

    public function post($uri, $controller): mixed
    {

        $this->add("POST", $uri, $controller);
    }

    public function put($uri, $controller): mixed
    {

        $this->add("PUT", $uri, $controller);
    }

    public function patch($uri, $controller): mixed
    {

        $this->add("PATCH", $uri, $controller);
    }

    public function delete($uri, $controller): mixed
    {

        $this->add("DELETE", $uri, $controller);
    }

    public function route($uri, $method): mixed
    {
        foreach ($this->routes as $route) {
            if ($route["uri"] === $uri && $route["method"] === strtoupper($method))
                return require Base::base_path($route["controller"]);
        }
    }
}
