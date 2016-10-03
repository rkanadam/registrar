<?php


namespace db;

use \Sag;
use util\Passwords;
use \Exception;
use Logger;
use util\User;

class DB
{
    private static $instance = null;

    private $sag = null;
    private $logger = null;

    private function __construct()
    {
        $this->logger = Logger::getLogger(__CLASS__);
        try {
            $sag = new Sag("rkanadam.cloudant.com");
            list($userName, $password) = Passwords::forDB();
            $sag->login($userName, $password);
            $sag->setDatabase("registrar");
            $this->sag = $sag;
        } catch (Exception $e) {
            $this->logger->error("Encountered and exception while connecting to cloudant: " . ($e->getMessage()));
        }
    }

    public static function getInstance()
    {
        if (DB::$instance === null) {
            DB::$instance = new DB();
        }
        return DB::$instance;
    }

    public function getInvitesAndProfiles()
    {
        $user = User::get();
        return $this->sag->get(
            "/_design/invitesAndProfiles/_view/invitesAndProfiles?limit=100&reduce=false&start_key=[\""
            . ($user->email)
            . "\"]");
    }
}