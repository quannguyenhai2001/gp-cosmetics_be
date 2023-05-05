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
    $product_id = $_GET['product_id'];

    $sql = $obj->select("products", "products.`id`, product_details.product_information, product_details.ingredients, product_details.usage_instructions,`products`.`name` as product_name,products.`thumbnail_url`,products.`gallery_image_urls`, products.`price`,products.`promotion`,products.`category_id`,products.`manufacturer_id`,manufacturers.`name` as manufacturer_name, manufacturers.`address` as manufacturer_address, products.`create_at`, products.`update_at`", "manufacturers JOIN product_details", "manufacturers.`id`=`products`.`manufacturer_id` and product_details.product_id = products.id", "products.`id` = '$product_id'", "", "");
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
                $result[0]['rating'] = 0;
            }
            $sql1 = "SELECT SUM(quantity) as quantity
                    FROM sizes
                    WHERE product_id = '$product_id'
                    GROUP BY product_id";
            $resultQuantity = $obj->getConnection()->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
            if (count($resultRating)) {
                $result[0]['quantity'] = $resultQuantity[0]["quantity"];
            } else {
                $result[0]['quantity'] = 0;
            }
        }

        http_response_code(200);
        echo json_encode(
            [
                "status" => "success",
                "data" => $result[0],
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
