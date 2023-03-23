<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";
include_once("../vendor/autoload.php");
include_once "../middleware/check-server-error.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "user");
    if ($payload) {
        if (isset($_POST['password'])) {
            $isUser = $obj->select("users", "*", null, null, "id='$payload[id]'", null, null);
            $data = $obj->getResult();
            $isServerError = checkServerError($isUser);
            if (!$isServerError) {
                if (count($data)) {
                    if (password_verify($_POST['oldPassword'], $data[0]['password'])) {
                        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $sql = $obj->update("users", ['password' => $newPassword], "`users`.`id` = $payload[id]");
                        if ($sql) {
                            http_response_code(200);
                            echo json_encode([
                                "status" => "success",
                                "message" => "User updated successfully!"
                            ]);
                        } else {
                            http_response_code(400);
                            echo json_encode([
                                "status" => "error",
                                "message" => "User not updated!"
                            ]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array(
                            "status" => "error",
                            "message" => "Password is wrong!",
                        ));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "user not found!",
                    ));
                }
            }
        } else {
            $arr = array();
            if (isset($_POST['display_name'])) {
                $arr['displayName'] = $_POST['displayName'];
            }
            if (isset($_POST['sex'])) {
                $arr['sex'] = $_POST['sex'];
            }
            if (isset($_POST['address'])) {
                $arr['address'] = $_POST['address'];
            }
            if (isset($_POST['age'])) {
                $arr['age'] = $_POST['age'];
            }
            if (isset($_FILES['avatar'])) {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $_ENV['CLOUD_NAME_CLOUDINARY'],
                        'api_key' => $_ENV['API_KEY_CLOUDINARY'],
                        'api_secret' => $_ENV['API_SECRET_KEY_CLOUDINARY']
                    ],
                    'url' => [
                        'secure' => true
                    ]
                ]);
                $data = (new UploadApi())->upload($_FILES['avatar']['tmp_name'], [
                    'folder' => 'cosmetics/avatars/',
                    'public_id' => $_FILES['avatar']['name'],
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]);

                $arr['avatar'] = $data['secure_url'];
            }
            $sql = $obj->update("users", $arr, "`users`.`id` = $payload[id]");
            $isServerError = checkServerError($sql);
            if (!$isServerError) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "User updated successfully!"
                ]);
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!",
    ));
}
