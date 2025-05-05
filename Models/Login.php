<?php
// Models/Login.php

class Login
{
    // Later you can pass a PDO connection into the constructor

    private $users = [
        'admin@bmc.com' => 'admin123',
        'user@bmc.com'  => 'user123'
    ];

    public function authenticate($email, $password)
    {
        return isset($this->users[$email]) && $this->users[$email] === $password;
    }
}
