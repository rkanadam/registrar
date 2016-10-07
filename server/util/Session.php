<?php
/**
 * Created by IntelliJ IDEA.
 * User: rkanadam
 * Date: 03/10/16
 * Time: 1:25 PM
 */

namespace util;


class Session
{
    const AUTH_RESPONSE = "authResponse";
    const SELECTED_PROFILE_ID = "selectedProfileId";

    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function set($key, $value)
    {
        session_start();
        $_SESSION[$key] = $value;
        session_write_close();
        return Session::get($key);
    }

    public static  function remove($key) {
        session_start();
        unset($_SESSION[$key]);
        session_write_close();
    }
}

session_start();
session_write_close();