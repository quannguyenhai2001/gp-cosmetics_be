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
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));
        $manufacturer_name = htmlspecialchars(strip_tags($data->manufacturer_name));
        $manufacturer_address = htmlspecialchars(strip_tags($data->manufacturer_address));
        $sql = $obj->insert("manufacturers", [
            "name" => $manufacturer_name,
            "address" => $manufacturer_address,
            'create_at' => date("y-m-d H:i:s"),
        ]);
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => 'success',
                "message" => "Manufacturer created successfully!",
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
