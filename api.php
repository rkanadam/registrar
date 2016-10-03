<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/vendor/autoload.php';

$user = null;
require_once __DIR__ . '/server/util/init_once.php';

use auth\Authenticator;
use db\DB;

$app = new \Slim\App;
$app->add(function ($request, $response, $next) {
    global $user;

    $path = $request->getUri()->getPath();
    if (strpos($path, "/auth") === 0 || $user->isAuthenticated !== false) {
        return $next($request, $response);
    } else {
        return $response->withStatus(403);
    }
});


$app->post('/auth/login', function (Request $request, Response $response) {
    $json = json_decode($request->getBody()->getContents());
    $auth = Authenticator::getInstance($json->authorizedBy);
    $return = $auth === null ? false : $auth->authenticate($json);
    $response->getBody()->write(json_encode($return));
    return $response;
});

$app->any('/me/invitesAndProfiles', function (Request $request, Response $response) {
    $db = DB::getInstance();
    $response->getBody()->write(json_encode($db->getInvitesAndProfiles()));
    return $response;
});

$app->run();