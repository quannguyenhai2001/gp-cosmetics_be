<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";

//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $sql = $obj->select("products", "products.`id`,`products`.`name` as product_name,products.`thumbnail_url`,products.`price`,products.`promotion`, products.`create_at`, products.`update_at`", "cart_details JOIN cart JOIN ", "products.`id` = cart_details.`product_id` and cart_details.`cart_id` = cart`.id`", "cart.`user_id` = $payload[id]", "", "");
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "data" => $result,
            ));
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
        "message" => "access denied"
    ));
}
