<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type:application/x-www-form-urlencoded");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once("../vendor/autoload.php");
include_once "../middleware/check-auth.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

//initialize database
$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));

        $product_id = htmlspecialchars(strip_tags($data->product_id));
        $sizes = $data->sizes;

        if (is_iterable($sizes)) {
            foreach ($sizes as $size) {

                $sql = $obj->insert("sizes", [
                    "name" => $size->size_name,
                    "additional_price" => $size->size_additional_price,
                    "quantity" => $size->quantity,
                    'create_at' => date("y-m-d H:i:s"),
                    "product_id " =>   $product_id
                ]);
            }
        }


        http_response_code(200);
        echo json_encode(array(
            "message" => "Add size successfully!",
            "sizes" => $sizes
        ));
    } else {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Add size failed!",
        ));
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "access denied",
    ));
}
