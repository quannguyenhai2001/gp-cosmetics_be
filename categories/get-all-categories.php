<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once "../database/database.php";

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
    $sql = $obj->select("categories", "*", "", "", "", "father_category_id = 0 DESC", $pagination);
    $result = $obj->getResult();
    $pageInfo = array();
    $total = $obj->getResult($obj->select("categories", "COUNT(*)", "", "", "", "", ""));

    $pageInfo["total"] = floatval($total[0]["COUNT(*)"]);
    if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
        $pageInfo["page"] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pageInfo["limit"] = $limit;
        $pageInfo["total_page"] = ceil($total[0]["COUNT(*)"] / $limit);
    }
    if ($sql) {
        http_response_code(200);
        echo json_encode(array(
            "status" => "success",
            "data" => $result,
            "pageInfo" =>  $pageInfo,
        ));
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
