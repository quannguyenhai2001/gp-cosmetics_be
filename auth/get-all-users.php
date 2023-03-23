<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once "../middleware/check-auth.php";
include_once("../database/database.php");
include_once("../vendor/autoload.php");
include_once "../middleware/check-server-error.php";

//initialize database
$obj = new Database();


//check method request
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $sql = $obj->select("users", "*", "", "", "", "", "");
        $isServerError = checkServerError($sql);
        if (!$isServerError) {
            $data = $obj->getResult();
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $data,
            ]);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
