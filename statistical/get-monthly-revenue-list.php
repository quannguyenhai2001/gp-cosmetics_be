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
    $sql = "SELECT YEAR(bills.create_at) as year, MONTH(bills.create_at) as month, SUM(bill_details.quantity * ((bill_details.product_price + bill_details.size_additional_price) * (1 - bill_details.product_promotion / 100))) as revenue
    FROM bills
    JOIN bill_details ON bills.id = bill_details.bill_id
    JOIN products ON bill_details.product_id = products.id
    JOIN sizes ON bill_details.size_id = sizes.id
    WHERE bills.status = 'Đã giao'
    GROUP BY YEAR(bills.create_at), MONTH(bills.create_at)
     
    ORDER BY YEAR(bills.create_at), MONTH(bills.create_at)";
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
