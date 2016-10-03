<?php


namespace db;

use \Sag;
use util\Passwords;
use \Exception;
use util\Log;
use util\User;

class DB
{
    private static $instance = null;

    private $sag = null;

    private function __construct()
    {
        try {
            $sag = new Sag("rkanadam.cloudant.com");
            list($userName, $password) = Passwords::forDB();
            $sag->login($userName, $password);
            $sag->setDatabase("registrar");
            $this->sag = $sag;
        } catch (Exception $e) {
            Log::error("Encountered and exception while connecting to cloudant: " . ($e->getMessage()));
        }
    }

    public static function getInstance()
    {
        if (DB::$instance === null) {
            DB::$instance = new DB();
        }
        return DB::$instance;
    }

    public function getProfiles()
    {
        $user = User::get();
        $response = $this->sag->get(
            "/_design/invitesAndProfiles/_view/profiles?limit=100&reduce=false&start_key=[\""
            . ($user->email)
            . "\"]");
        return $this->processResponse($response);
    }

    private function processResponse($response)
    {
        return $response->rows;
    }

    public function save($obj)
    {
        if (!empty($obj->{"_id"})) {
            return $this->sag->put($obj->{"_id"}, $obj);
        } else {
            return $this->sag->post($obj);
        }
    }
}