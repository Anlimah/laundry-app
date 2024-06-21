<?php

namespace Core;

use Exception;

class Container
{
    protected static $bindings = [];

    public static function bind($key, $resolver)
    {
        static::$bindings[$key] = $resolver;
        return static::$bindings;
    }

    public static function resolve($key, $function, $params)
    {
        if (!array_key_exists($key, static::$bindings)) {
            throw new Exception("No matching binding found for {$key}");
        }
        $resolver = static::$bindings[$key];
        $instance = $resolver();
        return call_user_func_array($instance, $function, $params);
    }
}
