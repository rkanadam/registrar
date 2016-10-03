<?php

namespace auth;

use Logger;

abstract class Authenticator
{
    public $logger = null;

    function __construct()
    {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function authenticate($json)
    {
        $this->logger->debug("Authenticating with json args: " . json_encode($json));
        if (empty($json)) {
            return false;
        }

        $response = $this->authenticateImpl($json);
        if ($response !== false) {
            session_start();
            $_SESSION["authenticated"] = true;
            $_SESSION["authenticationResponse"] = $response;
            session_write_close();
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