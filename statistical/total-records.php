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
    //users
    $sql = "SELECT COUNT(*) AS total_users FROM users;";
    $sql1 = "SELECT COUNT(*) AS new_users_last_month FROM users WHERE create_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $query = $obj->getConnection()->query($sql);
    $query1 = $obj->getConnection()->query($sql1);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    $result1 = $query1->fetchAll(PDO::FETCH_ASSOC);

    //bills
    $sql2 = "SELECT COUNT(*) AS total_orders FROM bills WHERE bills.status = 'Đã giao'";
    $sql3 = "SELECT COUNT(*) AS total_orders_last_month FROM bills WHERE create_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOW();";
    $query2 = $obj->getConnection()->query($sql2);
    $query3 = $obj->getConnection()->query($sql3);
    $result2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $result3 = $query3->fetchAll(PDO::FETCH_ASSOC);

    //revenue
    $sql4 = "SELECT  SUM(bill_details.quantity * ((bill_details.product_price + bill_details.size_additional_price) * (1 - bill_details.product_promotion / 100))) as total_revenue FROM bills JOIN bill_details ON bills.id = bill_details.bill_id WHERE status = 'Đã giao'";
    $sql5 = "SELECT 
    SUM(bill_details.quantity * ((bill_details.product_price + bill_details.size_additional_price) * (1 - bill_details.product_promotion / 100))) as total_revenue, 
    MONTH(bills.create_at) AS month, 
    YEAR(bills.create_at) AS year
    FROM 
    bills
    JOIN bill_details ON bills.id = bill_details.bill_id
    WHERE 
    status = 'Đã giao' AND
    bills.create_at BETWEEN DATE_SUB(NOW(), INTERVAL 2 MONTH) AND NOW()
    GROUP BY 
    MONTH(bills.create_at), YEAR(bills.create_at)";
    $query4 = $obj->getConnection()->query($sql4);
    $query5 = $obj->getConnection()->query($sql5);
    $result4 = $query4->fetchAll(PDO::FETCH_ASSOC);
    $result5 = $query5->fetchAll(PDO::FETCH_ASSOC);

    //products
    $sql6 = "SELECT SUM(quantity) as total_quantity FROM sizes;";
    $query6 = $obj->getConnection()->query($sql6);
    $result6 = $query6->fetchAll(PDO::FETCH_ASSOC);

    //products
    $sql7 = "SELECT COUNT(*) AS total_manu FROM manufacturers;";
    $query7 = $obj->getConnection()->query($sql7);
    $result7 = $query7->fetchAll(PDO::FETCH_ASSOC);
    if ($query) {
        $arrayValue = array();
        $arrayValue["users"]['total_users'] = $result[0]['total_users'];
        $arrayValue["users"]['new_users_last_month'] = $result1[0]['new_users_last_month'];
        $arrayValue["bills"]['total_orders'] = $result2[0]['total_orders'];
        $arrayValue["bills"]['total_orders_last_month'] = $result3[0]['total_orders_last_month'];
        $arrayValue["revenue"]['total_revenue'] = $result4[0]['total_revenue'];
        $arrayValue["revenue"]['total_revenue_last_two_month'] = $result5;
        $arrayValue["products"] =  $result6[0];
        $arrayValue["manufacturers"] =  $result7[0];

        http_response_code(200);
        echo json_encode(
            [
                "status" => "success",
                "data" => $arrayValue,
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
