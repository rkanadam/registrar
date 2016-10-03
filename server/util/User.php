<?php

namespace util;

use db\DB;
use util\Log;

class User
{
    public $name = "";
    public $email = "";
    public $_id = "";
    private $authResponse = null;

    private static $instance = null;

    private function __construct($authResponse)
    {
        $this->authResponse = $authResponse;
        if ($authResponse) {
            $this->email = $authResponse->email;
            $this->name = $authResponse->name;
        }
    }

    public function isAuthenticated () {
        return !empty ($this->authResponse);
    }


    public static function get()
    {
        if (User::$instance === null) {
            User::$instance = new User(Session::get(Session::AUTH_RESPONSE));
        }
        return User::$instance;
    }

    public function createNewProfile () {
        $db = DB::getInstance();
        $saved = $db->save ($this);
        return $saved;
    }
}