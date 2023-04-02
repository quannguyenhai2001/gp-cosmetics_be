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
    //filter
    $pagination = null;
    $limit = 10;

    if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
        $offsetIndex = isset($_GET['page']) ? ($limit * floatval($_GET['page'])) - $limit : 0;
        $pagination = $limit . " OFFSET " . $offsetIndex;
    }


    $conditionString = "";
    if (isset($_GET['product_id'])) {
        $conditionString  = $conditionString . "product_id = " . $_GET['product_id'];
    }
    $sql = $obj->select("sizes", "sizes.`id`,sizes.`title`,sizes.`label`,sizes.`additional_price`, sizes.`create_at`, sizes.`update_at`", "products", "products.`id`=sizes.`product_id`", $conditionString, "", $pagination);
    $result = $obj->getResult();

    if ($sql) {

        //total
        $pageInfo = array();
        $total = $obj->getResult($obj->select("sizes", "COUNT(*)", "products", "products.`id`=sizes.`product_id`", $conditionString, "", null));

        $pageInfo["total"] = floatval($total[0]["COUNT(*)"]);
        if (isset($_GET['use_page']) && $_GET['use_page'] == 1) {
            $pageInfo["page"] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $pageInfo["limit"] = $limit;
            $pageInfo["total_page"] = ceil($total[0]["COUNT(*)"] / $limit);
        }



        http_response_code(200);
        echo json_encode(
            [
                "status" => "success",
                "data" => $result,
                "pageInfo" =>  $pageInfo,
            ]
        );
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
