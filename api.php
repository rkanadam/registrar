<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/server/util/init_once.php';

use auth\Authenticator;
use db\DB;
use model\User;
use util\Log;

$config = new \Slim\Container();
$config['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        Log::error($exception->getTraceAsString());
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    };
};
$app = new \Slim\App ($config);

//Add an authorization middleware
$app->add(function ($request, $response, $next) {
    $user = User::get();

    $path = $request->getUri()->getPath();
    Log::debug("Attempting to authorize $path");
    if (strpos($path, "auth/") === 0 || $user->isAuthenticated()) {
        //Allow all paths that start with auth or any other if the user is authenticated
        return $next($request, $response);
    } else {
        Log::info("Rejecting $path as user is not authenticated");
        return $response->withStatus(403);
    }
});


$app->post('/auth/login', function (Request $request, Response $response) {
    $json = json_decode($request->getBody()->getContents());
    $auth = Authenticator::getInstance($json->authorizedBy);
    $return = $auth === null ? false : $auth->authenticate($json);
    if ($return) {
        $user = User::get();
        $profiles = $user->profiles;
        if (empty($profiles)) {
            $user->createNewProfile();
        }
    }
    $response->getBody()->write(json_encode($return));
    return $response;
});

$app->any('/me/profiles', function (Request $request, Response $response) {
    $user = User::get();
    $response->getBody()->write(json_encode($user->profiles));
    return $response;
});

$app->any('/me', function (Request $request, Response $response) {
    $user = User::get();
    $response->getBody()->write(json_encode($user));
    return $response;
});


$app->run();