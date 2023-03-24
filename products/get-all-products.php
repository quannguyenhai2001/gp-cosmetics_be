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
    $limit = 2;
    $offsetIndex = isset($_GET['page']) ? ($limit * $_GET['page']) - $limit : 0;
    $conditionString = "";
    if (isset($_GET['category_id'])) {
        $conditionString  = $conditionString . "category_id = " . $_GET['category_id'] . " and ";
    }
    if (isset($_GET['price'])) {
        $price = explode(", ", trim($_GET['price'], "[]"));
        $conditionString  = $conditionString . "price >= " . $price[0] . " and " . "price <= " . $price[1] . " and ";
    }
    if (isset($_GET['promotion'])) {
        $conditionString  = $conditionString . "promotion >= " . $_GET['promotion'] . " and ";
    }
    if (isset($_GET['manufacturer_id'])) {
        $conditionString  = $conditionString . "manufacturer_id = " . $_GET['manufacturer_id'] . " and ";
    }
    //search
    if (isset($_GET['search_value'])) {
        $conditionString  = "(products.`name` LIKE '%{$_GET['search_value']}%') OR (manufacturers.`name` LIKE '%{$_GET['search_value']}%')" . " and ";
    }
    $conditionString =  rtrim($conditionString, " and ");

    $sql = $obj->select("products", "products.`id`,`products`.`name` as product_name,products.`thumbnail_url`,products.`price`,products.`promotion`,products.`category_id`,products.`manufacturer_id`,manufacturers.`name` as manufacturer_name, manufacturers.`address` as manufacturer_address, products.`create_at`, products.`update_at`", "manufacturers", "manufacturers.`id`=products.`manufacturer_id`", $conditionString, "", "$limit OFFSET $offsetIndex");
    $result = $obj->getResult();

    if ($sql) {
        //rating
        foreach ($result as $key => $product) {
            $sql1 = "SELECT ROUND(AVG(star_rating), 2) star_average, COUNT(user_id) user_rating_total
                    FROM ratings
                    WHERE product_id = '$product[id]'
                    GROUP BY product_id";
            $resultRating = $obj->getConnection()->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
            if (count($resultRating)) {
                $result[$key]['rating'] = $resultRating[0];
            } else {
                $result[$key]['rating'] = null;
            }
        }
        //total
        $total = $obj->getResult($obj->select("products", "COUNT(*)", "manufacturers", "manufacturers.`id`=products.`manufacturer_id`", $conditionString, "", ""));
        $pageInfo = array();
        $pageInfo["page"] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pageInfo["limit"] = $limit;
        $pageInfo["total"] = $total[0]["COUNT(*)"];
        $pageInfo["total_page"] = ceil($total[0]["COUNT(*)"] / $limit);

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
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
