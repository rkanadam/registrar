<?php

namespace util;

class User
{
    public $isAuthenticated = false;
    public $userName = "";
    public $email = "";
    public $_id = "";

    private function __construct($isAuthenticated, $auth)
    {
        $this->isAuthenticated = $isAuthenticated;
        if ($isAuthenticated) {
            $this->email = $auth->email;
            $this->userName = $auth->userName;
        }
    }

    public static function init($isAuthenticated, $auth)
    {
        return new User($isAuthenticated, $auth);
    }
}