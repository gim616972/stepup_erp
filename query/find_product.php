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
        
        if (isset($_POST['sr_value']) && !empty($_POST['sr_value'])) {
            if (hasPermission($ac_username, $ac_user, "add_order", $conn)) {
                $sr_value = test_input($_POST['sr_value']);
                // find the product
                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE status = :status AND name LIKE :name LIMIT 10");
                $stmt->execute([':status' => 1,':name' => '%' . $sr_value . '%']);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)>0) {
                    foreach ($result as $pd_data) {
                        echo '<li class="list-group-item list-group-item-action px-3 py-2 pd-hov" p_sku="'.$pd_data['sku'].'" p_name="'.$pd_data['name'].'" p_price="'.$pd_data['price'].'">'.$pd_data['name'].'</li>';
                    }
                }
            }
        }
    }
}
?>