<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

//import file
include_once "../middleware/check-auth.php";
include_once("../database/database.php");
include_once("../vendor/autoload.php");

//initialize database
$obj = new Database();


//check method request
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $pagination = null;
        $limit = 15;

        if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
            $offsetIndex = isset($_GET['page']) ? ($limit * floatval($_GET['page'])) - $limit : 0;
            $pagination = $limit . " OFFSET " . $offsetIndex;
        }

        $sql = $obj->select("users", "*", "", "", "role LIKE 'user'", "", $pagination);
        $result = $obj->getResult();
        if ($sql) {
            //total
            $pageInfo = array();
            $total = $obj->getResult($obj->select("users", "COUNT(*)", "", "", "role LIKE 'user'", "", ""));

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
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
