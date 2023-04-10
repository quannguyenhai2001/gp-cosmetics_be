<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";
//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        $data = json_decode(file_get_contents("php://input", true));

        $sql = $obj->select("carts", "carts.`id`, carts.`quantity`, carts.`product_id`, products.`name`, products.`price`, products.`promotion`, products.`thumbnail_url`, sizes.`id` as size_id, sizes.`label`, sizes.`additional_price`, carts.`create_at`, carts.`update_at`", "products JOIN sizes ", "sizes.`product_id` = products.`id` and carts.`size_id` = sizes.`id`", "carts.`user_id` = $payload[id]", "", "");
        $products = $obj->getResult();
        $isProductStock = "";
        foreach ($products as $product) {
            $size_id = intval($product['size_id']);
            // select amount from product to calculate new amount
            $amount = $obj->select("sizes", "quantity", "", "", "id = '$size_id'", "", "", "", "", "");
            $amount = $obj->getResult();
            $value = intval($amount[0]['quantity']) - intval($product['quantity']);
            if ($value < 0) {
                $isProductStock = $product;
                break;
            }
        }
        if (!$isProductStock) {
            $array_param = [
                'receiver_name' => $data->receiver_name,
                'phone_number' => $data->phone_number,
                'delivery_address' => $data->delivery_address,
                'payment_method' => $data->payment_method,
                'note' => $data->note,
                'status' => "Pending",
                'total_price' => $data->total_price,
                'create_at' => date("d-m-Y"),
                'user_id' => $payload['id']
            ];
            $isPayment = $obj->insert('bills', $array_param);
            $bill_id = $obj->getResult();
            if ($isPayment) {
                foreach ($products as $product) {
                    $array_param = [
                        'quantity' => floatval($product['quantity']),
                        'bill_id' => $bill_id,
                        'product_name' => $product['name'],
                        'product_name' => $product['name'],
                        'product_thumbnail_url' => $product['thumbnail_url'],
                        'product_promotion' => $product['promotion'],
                        'product_price' => $product['price'],
                        'size_label' => $product['label'],
                        'size_additional_price' => $product['additional_price'],
                        'product_id' => $product['id'],
                        'create_at' => date("d-m-Y"),
                    ];
                    $addProInfo = $obj->insert('bill_details', $array_param);
                    //convert pro_Id from product
                    $size_id = intval($product['size_id']);
                    // select amount from product to caculate new amount
                    $amount = $obj->select("sizes", "quantity", "", "", "id = '$size_id'", "", "", "", "", "");
                    $result = $obj->getResult();
                    $value = intval($result[0]['quantity']) - intval($product['quantity']);
                    $updateAmount = $obj->update("sizes", ["quantity" => $value], "id = '$size_id'");
                }
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Payment success!",
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => $result,
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Sản phẩm " . $isProductStock["name"] . " đã hết hàng!",
            ]);
        }
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!"
    ));
}
