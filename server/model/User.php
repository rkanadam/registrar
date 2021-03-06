<?php

namespace model;

use db\DB;
use util\Log;
use util\Session;
use \stdClass;

class User
{
    public $firstName = "";
    public $lastName = "";
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
            $this->firstName = $authResponse->given_name;
            $this->lastName = $authResponse->family_name;
            $this->name = $authResponse->name;
            $db = DB::getInstance();
            $this->profiles = $db->getProfiles($this);
        }
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

    /**
     * @return boolean
     */
    public function setSelectedProfileId($profileId)
    {
        foreach ($this->profiles as $profile) {
            Log::log("Selected profile is ", $profile, $profileId);
            if ($profile->{"_id"} === $profileId) {
                Session::set(Session::SELECTED_PROFILE_ID, $profileId);
                return true;
            }
        }
        return false;
    }

    /**
     * @return User
     */
    public function getSelectedProfile()
    {
        $profileId = Session::get(Session::SELECTED_PROFILE_ID);
        if (empty($profileId)) return null;
        foreach ($this->profiles as $profile) {
            if ($profile->{"_id"} === $profileId) {
                return $profile;
            }
        }
        return null;
    }


    public function createNewProfile()
    {
        $db = DB::getInstance();

        $profile = new stdClass();
        $profile["email"] = $this->email;
        $profile["firstName"] = $this->firstName;
        $profile["lastName"] = $this->lastName;
        $profile["name"] = $this->name;
        $profile["type"] = "profile";
        $profile["primary"] = true;
        $db->save($profile);
        Log::log("Created a new profile ", $profile);
        $profile["primaryKey"] = $profile["_id"];
        $db->save($profile);
        $this->profiles[] = $profile;
        return $this;
    }
}