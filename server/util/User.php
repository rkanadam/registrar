<?php

namespace util;

class User
{
    public $isAuthenticated = false;
    public $name = "";
    public $email = "";
    public $_id = "";
    public $authenticationResponse = null;

    private function __construct($isAuthenticated, $auth)
    {
        $this->isAuthenticated = $isAuthenticated;
        $this->authenticationResponse = $auth;
        if ($isAuthenticated) {
            $this->email = $auth->email;
            $this->name = $auth->name;
        }
    }

    public static function init($isAuthenticated, $auth)
    {
        return new User($isAuthenticated, $auth);
    }
}