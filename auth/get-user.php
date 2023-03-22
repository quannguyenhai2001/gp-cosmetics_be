<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");

//import file
include_once "../middleware/check-auth.php";
include_once("../database/database.php");
include_once("../vendor/autoload.php");

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $payload = checkAuth(getallheaders());
    $sql = $obj->select("users", "*", null, null, "id='$payload[id]'", null, null);
    $data = $obj->getResult();
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $data,
    ]);
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "access denied"
    ));
}
