<?php

namespace util;

use \Logger;

class Log
{
    private static $logger = null;

    public static function log()
    {
        return Log::info(Log::format(func_get_args()));
    }

    public static function debug()
    {
        $logger = Log::get();
        return $logger->debug(Log::format(func_get_args()));
    }

    public static function info()
    {
        $logger = Log::get();
        return $logger->info(Log::format(func_get_args()));
    }

    public static function warn()
    {
        $logger = Log::get();
        return $logger->warn(Log::format(func_get_args()));
    }

    public static function error($str)
    {
        $logger = Log::get();
        return $logger->error(Log::format(func_get_args()));
    }


    private static function get()
    {
        if (Log::$logger === null) {
            Log::$logger = Logger::getLogger(__CLASS__);
        }
        return Log::$logger;
    }

    private function format($args)
    {
        $str = "";
        foreach (func_get_args() as $arg) {
            if (is_string($arg)) {
                $str .= $arg;
            } else {
                $str .= print_r($arg, true);
            }
        }
        return $str;
    }
}