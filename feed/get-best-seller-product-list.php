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
    $sql = "SELECT bd.product_thumbnail_url, bd.product_name, COUNT(DISTINCT bd.size_id) AS num_sizes, SUM(bd.quantity) AS total_quantity
        FROM bill_details bd
        JOIN bills b ON bd.bill_id = b.id
        WHERE b.status = 'Đã giao'
        GROUP BY bd.product_name
        ORDER BY total_quantity DESC
        LIMIT 10";
    $query = $obj->getConnection()->query($sql);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if ($query) {
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
        "message" => "Access denied!",
    ));
}
