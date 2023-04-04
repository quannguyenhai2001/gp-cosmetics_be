
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
        $isExistCart = $obj->select("cart", "id", "", "", "user_id = $payload[id]");
        $result = $obj->getResult();
        if ($isExistCart) {
            if (count($result)) {
                $cartId = intval($obj->getResult($isExistCart)[0]['id']);
            } else {
            }
        } else {
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}

?>