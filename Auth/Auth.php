<?php

namespace Auth;

use Core\Database;

class Authenticate extends Database
{
    public function authenticate($username, $password)
    {
        $user = $this->run(
            "SELECT * FROM users WHERE `username` = ? AND `password` = ?",
            array($username, $password)
        )->fetchOne();
        if ($user) return $user['id'];
        else return false;
    }

    public function generateToken($user_id)
    {
        $token = bin2hex(random_bytes(16));
        $token_id = $this->run(
            "INSERT INTO tokens (user_id, token) VALUES (?, ?)",
            array($user_id, $token)
        )->insert();
        if ($token_id) return $token;
    }

    public function validateToken($token)
    {
        $token_data = $this->run(
            "SELECT * FROM tokens WHERE token = ?",
            array($token)
        )->fetchOne();
        if ($token_data) return $token_data['user_id'];
        else return false;
    }
}
