<?php
include_once("../vendor/autoload.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

function checkAuth($allHeaders, $role)
{
    try {
        if (isset($allHeaders['Authorization'])) {
            $requestAuth = $allHeaders['Authorization'];
            $token = str_replace("Bearer ", "", $requestAuth);

            //decode token
            $decoded = JWT::decode($token, new Key($_ENV['PRIVATE_KEY'], 'HS256'));
            $payload = json_decode(json_encode($decoded->data, JSON_FORCE_OBJECT), true);
            if ($payload['role'] !== $role && $role !== null) {
                http_response_code(403);
                echo json_encode([
                    "status" => 'error',
                    "message" => "You are not authorized!"
                ]);
                return false;
            } else {
                return json_decode(json_encode($decoded->data, JSON_FORCE_OBJECT), true);
            }
        } else {
            throw new Exception("You are not authenticated!");
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "status" => 'error',
            "message" => "You are not authenticated!"
        ]);
        return false;
    }
}
