<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";
include_once("../vendor/autoload.php");


//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));
        $size_name = htmlspecialchars(strip_tags($data->size_name));
        $size_additional_price = htmlspecialchars(strip_tags($data->size_additional_price));
        $quantity = htmlspecialchars(strip_tags($data->quantity));
        $id = htmlspecialchars(strip_tags($data->id));
        $sql = $obj->update("sizes", [
            "name" => $size_name,
            "additional_price" => $size_additional_price,
            "quantity" => $quantity,
            'update_at' => date("y-m-d H:i:s"),
        ], "id = $id");
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Update size successfully!"
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
        "message" => "Access denied!",
    ));
}
