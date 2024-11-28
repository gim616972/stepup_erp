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
        
        if (isset($_POST['add_warehouse']) && isset($_POST['w_name']) && isset($_POST['w_address'])) {
            if (!empty($_POST['w_name']) && !empty($_POST['w_address'])) {
                if (hasPermission($ac_username, $ac_user, "add_warehouse", $conn)) {
                
                    $w_name    = test_input($_POST['w_name']);
                    $w_address = test_input($_POST['w_address']);
                    $w_id      = "W-".rand(1111,9999);
    
                    // add warehouse
                    $stmt = $conn->prepare("INSERT INTO tbl_warehouse (member_id, w_id, warehouse_name, warehouse_address, date) VALUES (:member_id, :w_id, :warehouse_name, :warehouse_address, :date)");
                    $row = $stmt->execute([":member_id"=>$ac_user, ":w_id"=>$w_id, ":warehouse_name"=>$w_name, ":warehouse_address"=>$w_address, ":date"=>$dateTime]);
                    if ($row) {
                        echo "Warehouse added successfully !";
                    } else {
                        echo "Failed to add Warehouse !";
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