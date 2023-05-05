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
        $bill_id = $data->bill_id;

        $sql = $obj->update("bills", [
            "status" => "Hủy",
            'update_at' => date("y-m-d H:i:s"),
        ], "id = $bill_id");
        $result = $obj->getResult();
        if ($sql) {
            $sql = $obj->select("bill_details", "*", "", "", "bill_id = $bill_id", "", "");
            $result1 = $obj->getResult();

            foreach ($result1  as $product) {
                $sql = $obj->select("sizes", "*", "", "", "id = $product[size_id]", "", "");
                $result2 = $obj->getResult();
                $sizeID = $result2[0]["id"];
                if (count($result2)) {
                    $sql = $obj->update("sizes", [
                        "quantity" => $result2[0]["quantity"] + $product["quantity"],
                        'update_at' => date("y-m-d H:i:s"),
                    ], "id = $sizeID");
                }
            }
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Cancel bills successfully!"
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
