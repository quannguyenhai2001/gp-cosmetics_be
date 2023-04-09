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
        $sql = $obj->select("carts", "carts.`id`, carts.`quantity`, carts.`product_id`, products.`name`, products.`price`, products.`promotion`, products.`thumbnail_url`, sizes.`id`, sizes.`label`, sizes.`additional_price`, carts.`create_at`, carts.`update_at`", "products JOIN sizes ", "carts.`product_id` = products.`id` and carts.`size_id` = sizes.`id`", "carts.`user_id` = $payload[id]", "", "");
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
        "message" => "Access denied!"
    ));
}
