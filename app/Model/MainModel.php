<?php


namespace App\Model;


class MainModel
{
    /**
     * Генерирует токен длиной 64 символа /^[0-9a-f]{64}$/
     * @param $prefix string
     * @return string
     * @throws \Exception
     */
    public static function generateToken($prefix = '')
    {
        $tokenBytes = random_bytes(32);
        return $prefix . bin2hex($tokenBytes);
    }

    public static function currentUrl()
    {
        $server = $_SERVER;
        //Figure out whether we are using http or https.
        $http = 'http';
        //If HTTPS is present in our $_SERVER array, the URL should
        //start with https:// instead of http://
        if (isset($server['HTTPS'])) {
            $http = 'https';
        }
        //Get the HTTP_HOST.
        $host = $server['HTTP_HOST'];
        //Get the REQUEST_URI. i.e. The Uniform Resource Identifier.
        $requestUri = $server['REQUEST_URI'];
        //Finally, construct the full URL.
        //Use the function htmlentities to prevent XSS attacks.
        return $http . '://' . $host . '/' . $requestUri;
    }
}