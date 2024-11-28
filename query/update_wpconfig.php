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
        
        if (isset($_POST['wp_update']) && isset($_POST['up_uu_id']) && isset($_POST['up_wp_url']) && isset($_POST['up_cm_key']) && isset($_POST['up_cm_secret'])) {
            if (!empty($_POST['up_uu_id']) && !empty($_POST['up_wp_url']) && !empty($_POST['up_cm_key']) && !empty($_POST['up_cm_secret'])) {
                if (hasPermission($ac_username, $ac_user, "update_company_settings", $conn)) {
                    $uu_id           = test_input($_POST['up_uu_id']);
                    $store_url       = test_input($_POST['up_wp_url']);
                    $consumer_key    = test_input($_POST['up_cm_key']);
                    $consumer_secret = test_input($_POST['up_cm_secret']);
    
                    // update wp_config
                    $stmt = $conn->prepare("UPDATE tbl_wp_config SET site_url = :site_url, consumer_key = :consumer_key, consumer_secret = :consumer_secret WHERE uu_id = :uu_id && member_id = :member_id");
                    $row = $stmt->execute([":uu_id"=>$uu_id, ":site_url"=>$store_url, ":consumer_key"=>$consumer_key, ":consumer_secret"=>$consumer_secret, ":member_id"=>$ac_user]);
                    if ($row) {
                        echo "Credentials updated successfully";
                    } else {
                        echo "Failed to update credentials!";
                    }
                } else {
                    echo "You Are Not Allowed !";
                }
            } else {
                echo "All fields are required !";
            }
        } else if (isset($_POST['wp_remove']) && isset($_POST['up_uu_id'])) {
            if (!empty($_POST['up_uu_id'])) {
                if (hasPermission($ac_username, $ac_user, "remove_company_settings", $conn)) {
                    $uu_id  = test_input($_POST['up_uu_id']);
                    $stmt = $conn->prepare("DELETE FROM tbl_wp_config WHERE uu_id = :uu_id");
                    $row = $stmt->execute([":uu_id"=>$uu_id]);
                    if ($row) {
                        echo "success";
                    } else {
                        echo "Failed to remove credentials!";
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