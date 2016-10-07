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
    return function ($request, $response, $e) use ($c) {
        Log::error("Encountered an exception while processing path [", $request->getUri()->getPath(), "].\nException\n", $e->getMessage(), "\nTrace:\n", $e->getTraceAsString());
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Processing Error');
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
    $response->withJson($return);
    return $response;
});

$app->any('/me', function (Request $request, Response $response) {
    $user = User::get();
    $response->withJson($user);
    return $response;
});

$app->any('/me/profiles', function (Request $request, Response $response) {
    $user = User::get();
    $response->withJson($user->profiles);
    return $response;
});

$app->post('/me/profiles/select', function (Request $request, Response $response) {
    $user = User::get();
    $profile = $request->getParsedBody();
    $return = $user->setSelectedProfileId($profile["_id"]);
    return $response->withJson($return);
});


$app->post('/profile/{id}', function (Request $request, Response $response) {
    $user = User::get();
    $id = $request->getAttribute("id");
    $profile = $request->getParsedBody();
    $profile["_id"] = $id;
    //Now save it
    $db = DB::getInstance();
    $db->save($profile);
    return $response->withJson($profile);
});


$app->run();