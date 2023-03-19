<?php
include_once("../vendor/autoload.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

function checkAuth($allHeaders)
{
    try {
        $requestAuth = $allHeaders['Authorization'];
        $token = str_replace("Bearer ", "", $requestAuth);

        //decode token
        $decoded = JWT::decode($token, new Key($_ENV['PRIVATE_KEY'], 'HS256'));
        return json_decode(json_encode($decoded->data, JSON_FORCE_OBJECT), true);
    } catch (Exception $e) {
        return false;
    }
}
