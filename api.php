<?php

$base = realpath(dirname($_SERVER["SCRIPT_FILENAME"]));
require_once "$base/vendor/autoload.php";
require_once "$base/server/util/init_once.php";

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
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

$app->any('/me/notifications', function (Request $request, Response $response) {
    $user = User::get();
    $db = DB::getInstance();
    $params = $request->getQueryParams();
    $start = empty ($params) || empty($params["skip"]) ? 0 : $params["skip"];
    $response->withJson($db->getNotifications($user, $start));
    return $response;
});


$app->post('/me/profiles/select', function (Request $request, Response $response) {
    $user = User::get();
    $profile = $request->getParsedBody();
    $return = $user->setSelectedProfileId($profile["_id"]);
    return $response->withJson($return);
});

$app->get('/events', function (Request $request, Response $response) {
    $db = DB::getInstance();
    $response->withJson($db->getEvents());
    return $response;
});

$app->post('/profile/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute("id");
    $profile = $request->getParsedBody();
    $profile["_id"] = $id;
    //Now save it
    $db = DB::getInstance();
    $db->save($profile);
    return $response->withJson($profile);
});

$app->post('/events/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute("id");
    $event = $request->getParsedBody();
    $event["_id"] = empty($id) || $id === "new" ? "" : $id;
    $event["type"] = "event";
    //Now save it
    $db = DB::getInstance();
    $db->save($event);
    return $response->withJson($event);
});

$app->post('/events/{id}/register', function (Request $request, Response $response) {
    $user = User::get();
    $event = $request->getParsedBody();
    $event["_id"] = "";
    $event["notificationId"] = $request->
    $event["type"] = "registration";
    $event["profile"] = $user->getSelectedProfile();
    //Now save it
    $db = DB::getInstance();
    $db->save($event);
    return $response->withJson($event);
});


$app->run();