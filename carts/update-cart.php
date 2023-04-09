<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";
//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));

        $id = $data->id;
        $quantity = $data->quantity;


        $sql = $obj->select("carts", "*", null, null, "id='$id'", null, null);
        $result = $obj->getResult();
        if ($sql) {
            if (count($result)) {
                $sql = $obj->update("carts", [
                    "quantity" => $quantity
                ], "id = $id");
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
                echo 1;
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
