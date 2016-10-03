<?php

namespace util;

session_start();
session_write_close();

use \Logger;

$logFile = dirname($_SERVER["SCRIPT_FILENAME"]) . "/logs/server.log";

if (!file_exists($logFile)) {
    $fh = fopen($logFile, "a+");
    fflush($fh);
    fclose($fh);
    chmod($logFile, 0777);
}

Logger::configure(array(
    'rootLogger' => array(
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutSimple'
            ),
            "threshold" => "debug",
            'params' => array(
                'file' => $logFile,
                'append' => true
            )
        )
    )
));

global $user;
$user = User::init($_SESSION["authenticated"], $_SESSION["authenticationResponse"]);

