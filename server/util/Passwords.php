<?php

namespace util;


class Passwords
{
    public static function forDB()
    {
        $local = array(
            '127.0.0.1',
            '::1'
        );

        if (in_array($_SERVER['REMOTE_ADDR'], $local)) {
            return ["localhost", "admin", "admin"];
        } else {
            return ["rkanadam.cloudant.com", "somporguenesselyncommand", "d992025092e1133221da993faadb63ec8c09487e"];
        }
    }
}