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

        if (isset($_POST['edit_curier_promt']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $editCourierId = test_input($_POST['uid']);
                $stmt = $conn->prepare("SELECT * FROM tbl_courier WHERE c_id = :username && member_id = :member_id");
                $stmt->execute([":username"=>$editCourierId, ":member_id"=>$ac_user]);
                $editCourier = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($editCourier)>0) {
                    foreach ($editCourier as $userCourier) {
                    ?>
                        <input type="hidden" id="courier_id" value="<?php echo $userCourier['c_id']; ?>">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" id="edit_name" class="form-control" value="<?php echo $userCourier['courier_name']; ?>" placeholder="Enter your Name">
                        </div>
                        <div class="mb-3">
                            <label for="edit_api_key" class="form-label">Api-Key</label>
                            <input type="text" id="edit_api_key" class="form-control" value="<?php echo $userCourier['api_key']; ?>" placeholder="Enter your Phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_secret_key" class="form-label">secret key</label>
                            <input type="text" id="edit_secret_key" class="form-control" value="<?php echo $userCourier['secret_key']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="edit_courier_Data" class="btn btn-primary text-white rounded-0">Edit User</button>
                        </div>
                    <?php
                    }
                } else {
                ?>
                    <div class="d-flex justify-content-center text-center py-5">
                        <div class="d-grid border border-light-subtle p-5 rounded">
                            <i class="fa fa-cube" style="font-size: 50px;"></i>
                            <span style="font-weight: 600;font-size: 30px;">No Data Found!</span>
                            <p class="mb-0">No data to display currently.</p>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo "All fields are required hee !";
            }
        } else if (isset($_POST['edit_courier_changes']) && isset($_POST['edit_id']) && isset($_POST['edit_name']) && isset($_POST['edit_api_key']) && isset($_POST['edit_secret_key'])) {
            if(!empty($_POST['edit_courier_changes']) && isset($_POST['edit_id']) && !empty($_POST['edit_name']) && !empty($_POST['edit_api_key']) && !empty($_POST['edit_secret_key'])){
                $id = test_input($_POST['edit_id']);
                $name     = test_input($_POST['edit_name']);
                $api_key    = test_input($_POST['edit_api_key']);
                $secret_key    = test_input($_POST['edit_secret_key']);
            
                //UPDATE THE USER DATA
                $stmt = $conn->prepare("UPDATE tbl_courier SET courier_name = :name, api_key = :api_key, secret_key = :secret_key WHERE c_id = :id && member_id = :member_id");
                $row = $stmt->execute([":member_id"=>$ac_user, ":id"=>$id, ":name"=>$name, ":api_key"=>$api_key, ":secret_key" => $secret_key]);
                if ($row) {
                    echo "Courier updated successfully !";
                } else {
                    echo "Failed to update Courier !";
                }
            }
        } else if (isset($_POST['delete_courier']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $deleteCourierId = test_input($_POST['uid']);
                $stmt = $conn->prepare("DELETE FROM tbl_courier WHERE c_id = ?");
                $deleteCourier = $stmt->execute([$deleteCourierId]);
                if ($deleteCourier) {
                    echo "success";
                } else {
                    echo "Failed to delete user !";
                }
            }
        } else if (isset($_POST['change_status']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $changeStatusId = test_input($_POST['uid']);
                $stmt = $conn->prepare("UPDATE tbl_courier SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END WHERE c_id = ?");
                $changeStatus = $stmt->execute([$changeStatusId]);
                $stmt = null;
                if ($changeStatus) {
                    echo "success";
                } else {
                    echo "Failed to delete user !";
                }
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