<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/server/util/init_once.php';


use auth\Authenticator;

$app = new \Slim\App;

$app->post('/auth/login', function (Request $request, Response $response) {
    $json = json_decode($request->getBody()->getContents());
    $auth = Authenticator::getInstance($json->authorizedBy);
    $return = $auth === null ? false : $auth->authenticate($json);
    $response->getBody()->write(json_encode($return));
    return $response;
});
$app->run();