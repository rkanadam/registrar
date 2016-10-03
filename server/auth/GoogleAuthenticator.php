<?php
/**
 * Created by IntelliJ IDEA.
 * User: rkanadam
 * Date: 02/10/16
 * Time: 9:11 PM
 */

namespace auth;


class GoogleAuthenticator extends Authenticator
{

    protected function authenticateImpl($args)
    {
        if (empty($args)) {
            return false;
        }
        $idToken = $args->idToken;
        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$idToken";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            return json_decode($result);
        } else {
            $this->logger->error("Authentication failed for token: $idToken. Response: $result");
        }

        return false;
    }
}