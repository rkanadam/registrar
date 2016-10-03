<?php
namespace auth;

use util\Log;

class GoogleAuthenticator extends Authenticator
{

    public function authenticateImpl($args)
    {
        $idToken = $args->idToken;
        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$idToken";
        Log::debug("Authentication URL is \n$url\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        Log::info("Authentication response from Goog: \n$result\n");
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            Log::info("Successfully validated token with Google");
            return json_decode($result);
        } else {
            Log::error("Authentication failed for token: $idToken. Response: $result");
        }
        return false;
    }
}