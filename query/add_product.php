<?php
session_start();
include "../concat/db_con.php";
include "permission_functions.php";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
date_default_timezone_set("Asia/Dhaka");
$dateTime = date('M d, Y  g:i A');

if (isset($_SESSION['user_id']) and isset($_SESSION['user_name']) and isset($_SESSION['user_pass'])) {
    $user_id   = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $user_pass = $_SESSION['user_pass'];
    
    $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE id = :id AND username = :username AND password = :password");
    $stmt->execute([":id"=>$user_id, ":username"=>$user_name, ":password"=>$user_pass]);
    $users_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($users_data) {
        $ac_user = $users_data['member_id'];
        $ac_username = $users_data['username'];
        
        if (isset($_POST['add_product']) && isset($_POST['pd_sku']) && isset($_FILES['pd_image']['name']) && isset($_POST['pd_name']) && isset($_POST['pd_type']) &&
        isset($_POST['pd_category']) && isset($_POST['pd_weight']) && isset($_POST['pd_summary']) && isset($_POST['pd_mrp']) && isset($_POST['buy_price'])) {
            if (!empty($_POST['pd_sku']) && !empty($_FILES['pd_image']['name']) && !empty($_POST['pd_name']) && !empty($_POST['pd_type']) && !empty($_POST['pd_category']) &&
            !empty($_POST['pd_weight']) && !empty($_POST['pd_summary']) && !empty($_POST['pd_mrp']) && !empty($_POST['buy_price'])) {
                if (hasPermission($ac_username, $ac_user, "add_product", $conn)) {
                    
                    $pd_sku      = test_input($_POST['pd_sku']);
                    $pd_name     = test_input($_POST['pd_name']);
                    $pd_type     = test_input($_POST['pd_type']);
                    $pd_category = test_input($_POST['pd_category']);
                    $pd_weight   = test_input($_POST['pd_weight']);
                    $pd_summary  = test_input($_POST['pd_summary']);
                    $pd_mrp      = test_input($_POST['pd_mrp']);
                    $buy_price   = test_input($_POST['buy_price']);
                    
                    // image process
                    $max_size  = 5 * 1024 * 1024;
                    $allow_ext = ['jpg', 'jpeg', 'png', 'webp', 'heic'];
                    $pd_image  = $_FILES['pd_image']['name'];
                    $tampname  = $_FILES['pd_image']['tmp_name'];
                    $extension = pathinfo($pd_image,PATHINFO_EXTENSION);
                    $file_size = $_FILES['pd_image']['size'];
                    $new_name  = "PRODUCT_LOGO_".rand(11111,99999).".".$extension;
                    $upload    = "../assets/".$new_name;
                    $img_name  = "assets/".$new_name;
                    
                    // add product
                    if ($file_size <= $max_size) {
                        if (in_array($extension, $allow_ext)) {
                            $stmt = $conn->prepare("INSERT INTO tbl_product (member_id, sku, images, name, type, categories, weight, description, price, purchase_price) VALUES (:member_id, :sku, :images, :name, :type, :categories, :weight, :description, :price, :purchase_price)");
                            $row = $stmt->execute([":member_id"=>$ac_user, ":sku"=>$pd_sku, ":images"=>$img_name, ":name"=>$pd_name, ":type"=>$pd_type, ":categories"=>$pd_category, ":weight"=>$pd_weight, ":description"=>$pd_summary, ":price"=>$pd_mrp, ":purchase_price"=>$buy_price]);
                            if ($row) {
                                $upload_image = move_uploaded_file($tampname, $upload);
                                if ($upload_image) {
                                    echo "Product added successfully !";
                                } else {
                                    echo "Failed to upload image !";
                                }
                            } else {
                                echo "Failed to add Product !";
                            }
                        } else {
                            echo "Invalid file type. Only JPG, JPEG, PNG, WEBP, and HEIC are allowed !";
                        }
                    } else {
                        echo "File size exceeds the maximum limit of 5 MB !";
                    }
                    
                } else {
                    echo "You Are Not Allowed !";
                }
            } else {
                echo "All fields are required !";
            }
        } else {
            echo "All fields are required !";
        }
    } else {
        echo "Please Login !";
    }
} else {
    echo "Please Login !";
}
?>