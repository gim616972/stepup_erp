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
$date = date('M d, Y');

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
        
        if (isset($_POST['add_customer']) && isset($_POST['cm_phone']) && isset($_POST['cm_name']) && isset($_POST['cm_address']) && isset($_POST['cm_email']) && isset($_POST['cm_source'])) {
            if (!empty($_POST['cm_phone']) && !empty($_POST['cm_name']) && !empty($_POST['cm_address']) && !empty($_POST['cm_source'])) {
                if (hasPermission($ac_username, $ac_user, "add_customer", $conn)) {
                    
                    $cm_id      = "C-".rand(111111,999999);
                    $cm_phone   = test_input($_POST['cm_phone']);
                    $cm_name    = test_input($_POST['cm_name']);
                    $cm_address = test_input($_POST['cm_address']);
                    $cm_email   = test_input($_POST['cm_email']);
                    $cm_source  = test_input($_POST['cm_source']);
                    $country    = "BD";
                    $lastName   = "";
                    
                    // check ecisting customer
                    $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE phone = :phone");
                    $stmt->execute([":phone"=>$cm_phone]);
                    $users_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($users_data)>0) {
                        echo "User already exist !";
                    } else {
                        // add customer
                        $stmt = $conn->prepare("INSERT INTO tbl_customers (member_id, cm_id, phone, first_name, last_name, address_1, country, email, created_at, source) VALUES (:member_id, :cm_id, :phone, :first_name, :last_name, :address_1, :country, :email, :created_at, :source)");
                        $row = $stmt->execute([":member_id"=>$ac_user, ":cm_id"=>$cm_id, ":phone"=>$cm_phone, ":first_name"=>$cm_name, ":last_name"=>$lastName, ":address_1"=>$cm_address, ":country"=>$country, ":email"=>$cm_email, ":created_at"=>$date, ":source"=>$cm_source]);
                        if ($row) {
                            echo "Customer added successfully !";
                        } else {
                            echo "Failed to add Customer !";
                        }
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