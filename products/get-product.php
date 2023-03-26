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
    $data = json_decode(file_get_contents("php://input", true));
    $product_id = $data->product_id;
    $sql = $obj->select("products", "products.`id`,`products`.`name` as product_name,products.`thumbnail_url`,products.`price`,products.`promotion`,products.`category_id`,products.`manufacturer_id`,manufacturers.`name` as manufacturer_name, manufacturers.`address` as manufacturer_address, products.`create_at`, products.`update_at`", "manufacturers", "manufacturers.`id`=`products`.`manufacturer_id`", "products.`id` = '$product_id'", "", "");
    $result = $obj->getResult();
    if ($sql) {
        if (count($result)) {
            $sql1 = "SELECT ROUND(AVG(star_rating), 2) star_average, COUNT(user_id) user_rating_total
                    FROM ratings
                    WHERE product_id = '$product_id'
                    GROUP BY product_id";
            $resultRating = $obj->getConnection()->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
            if (count($resultRating)) {
                $result[0]['rating'] = $resultRating[0];
            } else {
                $result[0]['rating'] = null;
            }
        }

        http_response_code(200);
        echo json_encode(
            [
                "status" => "success",
                "data" => $result,
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
        "message" => "Access denied!"
    ));
}
