<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";
//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));
        $product_id = $data->product_id;
        $size_id = $data->size_id;
        $quantity = $data->quantity;

        $sql = $obj->select("carts", "*", null, null, "size_id='$size_id'", null, null);
        $result = $obj->getResult();
        if ($sql) {
            if (count($result)) {
                $sql = $obj->update("carts", [
                    "quantity" => $result[0]["quantity"] + $quantity
                ], "size_id =  $size_id");
                $result = $obj->getResult();
                if ($sql) {
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Update cart successfully!"
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        "status" => "error",
                        "message" => $result,
                    ]);
                }
            } else {
                $isAddToCart = $obj->insert("carts", [
                    "product_id" => $product_id,
                    "size_id" => $size_id,
                    "quantity" => $quantity,
                    "user_id" => "$payload[id]",
                    'create_at' => date("y-m-d H:i:s"),

                ]);
                $result = $obj->getResult();
                if ($isAddToCart) {
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" =>  "Add to cart successfully!"
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
