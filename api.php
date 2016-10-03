<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$base = dirname($_SERVER["SCRIPT_FILENAME"]);
require "$base/vendor/autoload.php";

$app = new \Slim\App;
$app->post('/auth/login', function (Request $request, Response $response) {
    $return = false;
    $json = json_decode($request->getBody()->getContents());
    if ($json->authorizedBy === "google") {
        $idToken = $json->idToken;
        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$idToken";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            session_start();
            $return = json_decode($result);
            $_SESSION["authenticated"] = true;
            $_SESSION["auth"] = $return;
            session_write_close();
        }
        curl_close($ch);
    }
    $response->getBody()->write(json_encode($return));
    return $response;
});
$app->run();