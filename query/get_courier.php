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
        
        if (isset($_POST['get_courier'])) {
            if (hasPermission($ac_username, $ac_user, "edit_order_status", $conn)) {
                $stmt = $conn->prepare("SELECT * FROM tbl_courier WHERE status = :status AND member_id = :member_id");
                $stmt->execute([':status' => 1, ":member_id"=>$ac_user]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)>0) {
                    ?>
                    <select id="select_courier" class="form-select mt-3 mb-4 rounded-0">
                        <option class="rounded-0" value="" selected>Select Courier</option>
                        <?php
                        foreach ($result as $pd_data) {
                            echo '<option class="rounded-0" value="'.$pd_data['c_id'].'">'.$pd_data['courier_name'].'</option>';
                        }
                        ?>
                    </select>
                    <?php
                }
            }
        }
    }
}
?>