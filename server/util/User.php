<?php

namespace util;

use db\DB;
use util\Log;
use \stdClass;

class User
{
    public $name = "";
    public $email = "";
    public $_id = "";
    public $profiles = array();

    private $authResponse = null;

    private static $instance = null;

    private function __construct($authResponse)
    {
        $this->authResponse = $authResponse;
        if ($authResponse) {
            $this->email = $authResponse->email;
            $this->name = $authResponse->name;
        }
        $db = DB::getInstance();
        $this->profiles = $db->getProfiles($this);
    }

    /**
     * @return boolean
     */

    public function isAuthenticated()
    {
        return !empty ($this->authResponse);
    }

    /**
     * @return User
     */
    public static function get()
    {
        if (User::$instance === null) {
            User::$instance = new User(Session::get(Session::AUTH_RESPONSE));
        }
        return User::$instance;
    }

    public function createNewProfile()
    {
        $db = DB::getInstance();

        $profile = new stdClass();
        $profile->email = $this->email;
        $profile->name = $this->name;
        $profile->type = "profile";
        $db->save($profile);

        Log::info("Created a new profile " . (print_r($profile)));
        $this->profiles[] = $profile;
        return $this;
    }
}