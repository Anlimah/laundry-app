<?php

namespace Core;

class Response
{
    public static function json($status, $message, $data = null)
    {
        http_response_code($status);
        return json_encode(array('message' => $message, 'data' => $data));
    }
}
