<?php

namespace util;

use \Logger;

class Log
{
    private static $logger = null;

    public static function log($str)
    {
        return Log::info($str);
    }

    public static function debug($str)
    {
        $logger = Log::get();
        return $logger->debug($str);
    }

    public static function info($str)
    {
        $logger = Log::get();
        return $logger->info($str);
    }

    public static function warn($str)
    {
        $logger = Log::get();
        return $logger->warn($str);
    }

    public static function error($str)
    {
        $logger = Log::get();
        return $logger->error($str);
    }


    private static function get()
    {
        if (Log::$logger === null) {
            Log::$logger = Logger::getLogger(__CLASS__);
        }
        return Log::$logger;
    }
}