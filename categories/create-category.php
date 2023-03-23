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
        $name = htmlspecialchars(strip_tags($data->name));
        $father_category_id = htmlspecialchars(strip_tags($data->father_category_id));
        $sql = $obj->insert("categories", [
            "name" => $name,
            "father_category_id" => $father_category_id,
            'create_at' => date("y-m-d H:i:s"),
        ]);
        $result = $obj->getResult();
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => 'success',
                "message" => "Category created successfully!"
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
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
