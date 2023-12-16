<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: *");

//import file
include_once "../middleware/check-auth.php";
include_once("../database/database.php");
include_once("../vendor/autoload.php");

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $payload = checkAuth(getallheaders(), null);
    if ($payload) {
        $category_id = $_GET['category_id'];
        $sql = $obj->select("categories", "*", null, null, "id='$category_id'", null, null);
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $result[0],
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => $result,
            ]);
        }
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!"
    ));
}
