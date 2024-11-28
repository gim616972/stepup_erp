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
        
        if (isset($_POST['add_courier']) && isset($_POST['courier']) && isset($_POST['api_key']) && isset($_POST['secret_key'])) {
            if (!empty($_POST['courier']) && !empty($_POST['api_key']) && !empty($_POST['secret_key'])) {
                if (hasPermission($ac_username, $ac_user, "add_courier", $conn)) {
                    
                    $courier    = test_input($_POST['courier']);
                    $api_key    = test_input($_POST['api_key']);
                    $secret_key = test_input($_POST['secret_key']);
                    $c_id       = "D-".rand(1111,9999);
    
                    // add courier
                    $stmt = $conn->prepare("INSERT INTO tbl_courier (member_id, c_id, courier_name, api_key, secret_key, date) VALUES (:member_id, :c_id, :courier_name, :api_key, :secret_key, :date)");
                    $row = $stmt->execute([":member_id"=>$ac_user, ":c_id"=>$c_id, ":courier_name"=>$courier, ":api_key"=>$api_key, ":secret_key"=>$secret_key, ":date"=>$dateTime]);
                    if ($row) {
                        echo "Courier added successfully !";
                    } else {
                        echo "Failed to add Courier !";
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