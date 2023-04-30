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
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));
        $category_name = htmlspecialchars(strip_tags($data->category_name));
        $id = htmlspecialchars(strip_tags($data->id));

        $father_category_id = 0;
        if (isset($data->father_category_id)) {
            $father_category_id = $data->father_category_id;
        }
        $sql = $obj->update("categories", [
            "name" => $category_name,
            "father_category_id" => $father_category_id,
            'update_at' => date("y-m-d H:i:s"),
        ], "id = $id");
        if ($sql) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Update category successfully!"
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
        "message" => "Access denied!"
    ));
}
