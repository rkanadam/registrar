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
        return $this->authenticateImpl($json);
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