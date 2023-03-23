<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//import file
include_once("../database/database.php");
include_once "../middleware/check-server-error.php";

//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input", true));
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    //convert to string
    $display_name = htmlspecialchars(strip_tags($data->display_name));
    $username = htmlspecialchars(strip_tags($data->username));
    $email = htmlspecialchars(strip_tags($data->email));
    $password = htmlentities(strip_tags($data->password));
    $phone_number = htmlspecialchars(strip_tags($data->phone_number));
    $address = htmlspecialchars(strip_tags($data->address));
    $sex = htmlspecialchars(strip_tags($data->sex));
    $age = htmlspecialchars(strip_tags($data->age));
    $role = "user";

    //hash password
    $newPassword = password_hash($password, PASSWORD_DEFAULT);

    //check user by email
    $sql = $obj->select("users", "email", null, null, "email='$email'", null, null);
    $data = $obj->getResult();
    $isServerError = checkServerError($sql);
    if (!count($data) && !$isServerError) {
        $array_param = [
            'display_name' => $display_name,
            'username' => $username,
            'email' => $email,
            'password' => $newPassword,
            'phone_number' => $phone_number,
            'address' => $address,
            'sex' => $sex,
            'age' => $age,
            'role' => $role,
            'create_at' => date("y-m-d H:i:s"),
        ];

        //add new user to database
        $isSql = $obj->insert('users', $array_param);
        $result = $obj->getResult();
        if ($isSql) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Create user successfully!",
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Error!",
            ]);
        }
    } else if (count($data) && !$isServerError) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "field" => "email",
            "message" => "Email already exist!",
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!"
    ));
}
