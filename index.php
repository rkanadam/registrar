<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/server/util/init_once.php';

use model\User;
use util\Log;

$user = User::get();
if (!$user->isAuthenticated()) {
    header('Location: unauth/login.php');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, user-scalable=yes">

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/lodash/dist/lodash.min.js"></script>
    <script src="i18nMessages.js"></script>
    <script src="bower_components/webcomponentsjs/webcomponents-lite.js"></script>
    <script type="text/javascript">window.AppBehaviors = {}</script>
    <link rel="import" href="elements/registrar-app.html">
</head>
<body>
<registrar-app/>
</body>
</html>
