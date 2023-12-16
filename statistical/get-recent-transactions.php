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
    $sql = "SELECT bills.*, users.username , SUM(bill_details.quantity * ((bill_details.product_price + bill_details.size_additional_price) * (1 - bill_details.product_promotion / 100))) as total_price
    FROM bills
    JOIN bill_details ON bills.id = bill_details.bill_id
    JOIN users ON bills.user_id = users.id
    WHERE bills.status = 'Đã giao'
    GROUP BY 
    bills.id
    ORDER BY 
    bills.create_at DESC 
    LIMIT 6";
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
