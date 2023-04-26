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
include_once "../middleware/check-auth.php";

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));
        $star_rating = $data->star_rating;
        $comment = $data->comment;
        $product_id  = $data->product_id;
        $bill_detail_id = $data->bill_detail_id;
        $sql = $obj->insert("ratings", [
            "star_rating" => $star_rating,
            "comment" => $comment,
            "product_id" => $product_id,
            "bill_detail_id" => $bill_detail_id,
            "user_id" => $payload['id'],
            'create_at' => date("d-m-Y"),
        ]);
        $result  = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Rating product successfully!",
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
        "message" => "access denied",
    ));
}
