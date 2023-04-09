<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once("../database/database.php");
include_once("../vendor/autoload.php");

//initialize database
$obj = new Database();


//check method request
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $pagination = null;
    $limit = 15;
    if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
        $offsetIndex = isset($_GET['page']) ? ($limit * floatval($_GET['page'])) - $limit : 0;
        $pagination = $limit . " OFFSET " . $offsetIndex;
    }
    $conditionString = "";
    if (isset($_GET['product_id'])) {
        $conditionString  = $conditionString . "product_id = " . $_GET['product_id']  . " and ";
    }
    $conditionString =  rtrim($conditionString, " and ");

    $sql = $obj->select("sizes", "*", "", "", $conditionString, "", $pagination);
    $result = $obj->getResult();
    if ($sql) {
        $data = $obj->getResult();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $result,
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => $result,
        ]);
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
