<?php

namespace Core;

class Request
{
    public static function getBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}
