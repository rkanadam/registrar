<?php


namespace db;

use \Sag;
use util\Passwords;
use \Exception;
use util\Log;
use model\User;

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

    /**
     * @return DB
     */

    public static function getInstance()
    {
        if (DB::$instance === null) {
            DB::$instance = new DB();
        }
        return DB::$instance;
    }

    public function getProfiles($user)
    {
        $response = $this->sag->get(
            "/_design/profiles/_view/profiles?limit=100&reduce=false&include_docs=true&key=\""
            . ($user->email)
            . "\"");
        $docs = $this->collectDocs($response);
        foreach ($docs as $doc) {
            if ($doc->primaryKey) {
                $response = $this->sag->get(
                    "/_design/profiles/_view/profilesByPrimaryKey?limit=100&reduce=false&include_docs=true&key=\""
                    . ($doc->primaryKey)
                    . "\"");
                return $this->collectDocs($response);
            }
        }

        return [];
    }

    private function collectDocs($response)
    {
        $docs = [];
        if (!empty ($response) && !empty($response->body)) {
            foreach ($response->body->rows as $row) {
                $docs[] = $row->doc;
            }
        }
        return $docs;
    }

    private function getViewRows($response)
    {
        return empty ($response) || empty($response->body) ? null : $response->body->rows;
    }

    public function save(&$obj)
    {
        $return = false;
        if (!empty($obj["_id"])) {
            $return = $this->sag->put($obj["_id"], $obj);
            Log::log("Object update response: \n", $return);
        } else {
            $return = $this->sag->post($obj);
            Log::log("Object create response: \n", $return);
        }

        if ($return && $return->body) {
            $obj["_id"] = $return->body->id;
            $obj["_rev"] = $return->body->rev;
            return true;
        }

        return false;
    }
}