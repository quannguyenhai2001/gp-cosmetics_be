<?php
//add headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type:application/x-www-form-urlencoded");
header("Access-Control-Allow-Methods: POST");

//import file
include_once "../database/database.php";
include_once("../vendor/autoload.php");
include_once "../middleware/check-auth.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

//initialize database
$obj = new Database();
// $data = json_decode(file_get_contents("php://input", true));
// $product_name = $data->thumbnail_url;
//check method request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payload = checkAuth(getallheaders(), "admin");
    if ($payload) {
        $thumbnail_url = $_FILES['thumbnail_url'];
        http_response_code(200);
        echo json_encode(array(
            "image" => $thumbnail_url,
            "thumbnail_url" => $_POST['thumbnail_url']
        ));
        // product_name: "",
        // product_price: "",
        // product_promotion: "",
        // thumbnail_url: "",
        // gallery_image_urls: "",
        // manufacturer_id: "",
        // category_id: "",
        // product_information: "",
        // sizes: [
        //     {
        //         id: 1,
        //         size_name: "",
        //         size_additional_price: "",
        //         quantity: ""
        //     }
        // ]
        $fileNameThumbnail  =  $_FILES['gallery_image_urls']['name'];
        $tempPathThumbnail  =  $_FILES['gallery_image_urls']['tmp_name'];
        $fileNameGallery  =  $_FILES['gallery_image_urls']['name'];
        $tempPathGallery  =  $_FILES['gallery_image_urls']['tmp_name'];

        for ($i = 0; $i < count($fileNameGallery); $i++) {
            if (empty($fileNameGallery[$i])) {
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
                $data = (new UploadApi())->upload($fileNameGallery[$i], [
                    'folder' => 'cosmetics/products/',
                    'public_id' => $fileNameGallery[$i],
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]);
                array_push($imageVal, $data['secure_url']);
            }
        }
        $image = json_encode($imageVal);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $createAt = date("d-m-Y H:i:s");
        $manu_Id = $_POST['manu_Id'];
        $cate_Id = $_POST['cate_Id'];
        $sql = $obj->insert("products", [
            "id" => "",
            "name" => $name,
            "price" => $price,
            "promotion" => $promotion,
            "description" => $description,
            "size" => $size,
            "amount" => $amount,
            "image" => $image,
            "createAt" => $createAt,
            "manu_Id" => $manu_Id,
            "cate_Id" => $cate_Id
        ]);
        if ($sql) {
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "add product failed"));
        }
    }
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "access denied",
    ));
}
