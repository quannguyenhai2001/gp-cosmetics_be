<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once "../middleware/check-auth.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
//initialize database
$obj = new Database();

//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $arr = array();
        $stringThumbnail  = "";
        if (isset($_FILES['thumbnail_url'])) {
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
            $data = (new UploadApi())->upload($_FILES['thumbnail_url']['tmp_name'], [
                'folder' => 'cosmetics/products/',
                'public_id' => pathinfo($_FILES['thumbnail_url']['name'], PATHINFO_FILENAME) . time(),
                'overwrite' => false,
                'resource_type' => 'image'
            ]);
            $arr['thumbnail_url']
                = $data['secure_url'];
        }
        $imageVal = array();
        if (isset($_FILES['gallery_image_urls'])) {
            for ($i = 0; $i < count($_FILES['gallery_image_urls']['name']); $i++) {
                if (empty($_FILES['gallery_image_urls'])) {
                    $errorMSG = json_encode(array("message" => "please select image", "status" => false));
                    echo $errorMSG;
                } else {
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
                    $data = (new UploadApi())->upload($_FILES['gallery_image_urls']['tmp_name'][$i], [
                        'folder' => 'cosmetics/products/',
                        'public_id' => pathinfo($_FILES['gallery_image_urls']['name'][$i], PATHINFO_FILENAME) . time(),
                        'overwrite' => false,
                        'resource_type' => 'image'
                    ]);
                    array_push($imageVal, $data['secure_url']);
                }
            }
        }
        if (!empty($imageVal)) {
            $arr['thumbnail_url'] =
                json_encode($imageVal);
        }

        $arr['name'] = $_POST['product_name'];
        $arr['price'] = $_POST['product_price'];
        $arr['promotion'] = $_POST['product_promotion'];
        $arr['update_at'] = date("y-m-d H:i:s");
        $arr['manufacturer_id'] = $_POST['manufacturer_id'];
        $arr['category_id'] = $_POST['category_id'];
        $sql = $obj->update("products", $arr, "id = $_POST[product_id]");
        $result = $obj->getResult();

        if ($sql) {
            // $sql = $obj->insert("product_details", [
            //     "product_information" => $_POST['productInformation'],
            //     "ingredients" => $_POST['ingredients'],
            //     "usage_instructions" => $_POST['usageInstructions'],
            //     'create_at' => date("y-m-d H:i:s"),
            //     "product_id " =>    $_POST['product_id']
            // ]);

            // foreach ($_POST['sizes'] as $size) {
            //     $sql = $obj->insert("sizes", [
            //         "name" => $size['size_name'],
            //         "additional_price" => $size['size_additional_price'],
            //         "quantity" => $size['quantity'],
            //         'create_at' => date("y-m-d H:i:s"),
            //         "product_id " => $_POST['product_id']
            //     ]);
            // }

            http_response_code(200);
            echo json_encode(array(
                "message" => "Update product successfully!",
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                "message" => "Update product failed!",

            ));
        }
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Access denied!"
    ));
}
