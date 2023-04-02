<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once "../database/database.php";

//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $data = json_decode(file_get_contents("php://input", true));
    $product_id = $data->product_id;
    $sql = $obj->select("product_details", "product_details.`id`,product_details.`product_information`,product_details.`ingredients`,product_details.`usage_instructions`, product_details.`create_at`, product_details.`update_at`", "products", "products.`id`=product_details.`product_id`", "products.`id` = '$product_id'", "", "");
    $result = $obj->getResult();
    if ($sql) {
        http_response_code(200);
        echo json_encode(
            [
                "status" => "success",
                "data" => $result,
            ]
        );
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => $result,
        ]);
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!"
    ));
}
