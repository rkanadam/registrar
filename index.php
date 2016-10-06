<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/server/util/init_once.php';

use model\User;
use util\Log;

$user = User::get();

Log::info ("Found the user allright: " . (json_encode($user)));
if (!$user->isAuthenticated ()) {
    header('Location: unauth/login.php');
    return;
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, user-scalable=yes">

    <script src="bower_components/webcomponentsjs/webcomponents-lite.js"></script>
    <script src="bower_components/lodash/dist/lodash.min.js"></script>
    <link rel="import" href="elements/registrar-app.html">


    <style type="text/css">
        body {
            margin: 0;
            font-family: 'Roboto'
            font-size: 12px;
        }
    </style>
</head>
<body>
<registrar-app/>
</body>
</html>
