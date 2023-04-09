<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: *");

//import file
include_once "../middleware/check-auth.php";
include_once("../database/database.php");
include_once("../vendor/autoload.php");

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));

        $id = $data->id;
        $sql = $obj->delete("carts", "id = $id");
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => "Delete product from cart successfully!",
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
