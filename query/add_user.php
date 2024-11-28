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
        
        // add user
        if (isset($_POST['add_user']) && isset($_POST['name']) && isset($_POST['userName']) && isset($_POST['email']) && isset($_POST['password'])) {
            if (!empty($_POST['name']) && !empty($_POST['userName']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $name     = test_input($_POST['name']);
                $username = test_input($_POST['userName']);
                $email    = test_input($_POST['email']);
                $password = test_input($_POST['password']);
                // hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE member_id = :member_id AND username = :username");
                $stmt->execute([":member_id"=>$ac_user, ":username"=>$username]);
                $users_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($users_info)<1) {
                    // insert user
                    $stmt = $conn->prepare("INSERT INTO tbl_user (member_id, username, name, email, password) VALUES (:member_id, :username, :name, :email, :password)");
                    $row = $stmt->execute([":member_id"=>$ac_user, ":username"=>$username, ":name"=>$name, ":email"=>$email, ":password"=>$hashed_password]);
                    if ($row) {
                        echo "User added successfully !";
                    } else {
                        echo "Failed to add User !";
                    }
                } else {
                    echo "User already exist !";
                }
            } else {
                echo "All fields are required !";
            }
            
        // edit user
        } else if (isset($_POST['edit_user']) && isset($_POST['user_id']) && isset($_POST['edit_name']) && isset($_POST['edit_email']) && isset($_POST['edit_password'])) {
            if (!empty($_POST['user_id']) && !empty($_POST['edit_name']) && !empty($_POST['edit_email']) && !empty($_POST['edit_password'])) {
                $user_id  = test_input($_POST['user_id']);
                $name     = test_input($_POST['edit_name']);
                $email    = test_input($_POST['edit_email']);
                $password = test_input($_POST['edit_password']);
                // hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // update user
                $stmt = $conn->prepare("UPDATE tbl_user SET name = :name, email = :email, password = :password WHERE username = :username && member_id = :member_id");
                $row = $stmt->execute([":member_id"=>$ac_user, ":username"=>$user_id, ":name"=>$name, ":email"=>$email, ":password"=>$hashed_password]);
                if ($row) {
                    echo "User updated successfully !";
                } else {
                    echo "Failed to update User !";
                }
            } else {
                echo "All fields are required !";
            }
        // edit permission
        } else if (isset($_POST['edit_permission']) && isset($_POST['user_id']) && isset($_POST['permissions'])) {
            if (!empty($_POST['user_id']) && !empty($_POST['permissions'])) {
                $userId = test_input($_POST['user_id']);
                $newPermissions = $_POST['permissions'];
                
                // Delete all current permissions for the user
                $stmt = $conn->prepare("DELETE FROM tbl_user_permission WHERE username = ?");
                $stmt->execute([$userId]);
                
                // Insert new permissions for the user
                foreach ($newPermissions as $permission) {
                    $stmt = $conn->prepare("INSERT INTO tbl_user_permission (member_id, username, permission) VALUES (?, ?, ?)");
                    $stmt->execute([$ac_user, $userId, $permission]);
                }
                echo "Permissions updated successfully !";
                
            } else {
                echo "All fields are required !";
            }
        // delete user 
        } else if (isset($_POST['delete_user']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $deleteUserId = test_input($_POST['uid']);
                $stmt = $conn->prepare("DELETE FROM tbl_user WHERE username = ?");
                $delUser = $stmt->execute([$deleteUserId]);
                if ($delUser) {
                    echo "success";
                } else {
                    echo "Failed to delete user !";
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