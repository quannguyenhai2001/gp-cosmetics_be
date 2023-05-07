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
    if (isset($_GET['status']) && $_GET['status'] !== "all") {
        $conditionString  = $conditionString . "status LIKE '%{$_GET['status']}%' " . " and ";
    }
    if (isset($_GET['user_id'])) {
        $conditionString  = $conditionString . "user_id = " . $_GET['user_id']  . " and ";
    }
    $conditionString =  rtrim($conditionString, " and ");

    $sql = $obj->select("bills", "bills.*, users.username, SUM(bill_details.quantity * ((bill_details.product_price + bill_details.size_additional_price) * (1 - bill_details.product_promotion / 100))) as total_price", "users JOIN bill_details", "bills.`user_id` = users.`id` and bills.id = bill_details.bill_id", $conditionString, "create_at DESC", $pagination, "bills.id");
    $result = $obj->getResult();
    if ($sql) {
        $pageInfo = array();
        $total = $obj->getResult($obj->select("bills", "COUNT(*)", "", "", $conditionString, "", ""));

        $pageInfo["total"] = floatval($total[0]["COUNT(*)"]);
        if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
            $pageInfo["page"] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $pageInfo["limit"] = $limit;
            $pageInfo["total_page"] = ceil($total[0]["COUNT(*)"] / $limit);
        }
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $result,
            "pageInfo" =>  $pageInfo,
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
