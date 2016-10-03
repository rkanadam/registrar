<?php

namespace auth;

use util\Session;
use util\Log;

abstract class Authenticator
{

    public function authenticate($json)
    {
        Log::info("Authenticating with json args: " . json_encode($json));
        if (empty($json)) {
            return false;
        }

        $response = $this->authenticateImpl($json);
        Log::error("Caught an exception while processing ");

        if ($response !== false) {
            Session::set(Session::AUTH_RESPONSE, $response);
        }
        return $response;
    }

    public static function getInstance($authType)
    {
        if (strtolower($authType) === "google") {
            return new GoogleAuthenticator();
        }
        return null;
    }

    abstract protected function authenticateImpl($args);
}