<?php


namespace db;

use \Sag;
use util\Passwords;

class DB
{
    private static $instance = null;

    private $sag = null;

    private function __construct()
    {
        $sag = new Sag("https://rkanadam.cloudant.com/registrar", "80");
        list($userName, $password) = Passwords::forDB();
        $sag->login($userName, $password);

        $this->sag = $sag;
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

    }
}