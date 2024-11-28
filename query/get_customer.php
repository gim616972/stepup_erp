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

        // edit user promt
        if (isset($_POST['edit_customer_promt']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $editCustomerId = test_input($_POST['uid']);
                $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE cm_id = :username && member_id = :member_id");
                $stmt->execute([":username"=>$editCustomerId, ":member_id"=>$ac_user]);
                $editCustomer = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($editCustomer)>0) {
                    foreach ($editCustomer as $userCustomer) {
                    ?>
                        <input type="hidden" id="user_id" value="<?php echo $userCustomer['cm_id']; ?>">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" id="edit_name" class="form-control" value="<?php echo $userCustomer['first_name']; ?>" placeholder="Enter your Name">
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone No</label>
                            <input type="text" id="edit_phone" class="form-control" value="<?php echo $userCustomer['phone']; ?>" placeholder="Enter your Phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" id="edit_email" class="form-control" value="<?php echo $userCustomer['email']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Location</label>
                            <input type="text" id="edit_address" class="form-control" value="<?php echo $userCustomer['address_1']; ?>" placeholder="Enter your address">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="edit_customer_Data" class="btn btn-primary text-white rounded-0">Edit User</button>
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
                echo "All fields are required !";
            }
        }else if (isset($_POST['edit_customer_changes']) && isset($_POST['edit_id']) && isset($_POST['edit_name']) && isset($_POST['edit_phone']) && isset($_POST['edit_email']) && isset($_POST['edit_address'])) {
            if(!empty($_POST['edit_customer_changes']) && !empty($_POST['edit_id']) && !empty($_POST['edit_name']) && !empty($_POST['edit_phone']) && !empty($_POST['edit_email']) && !empty($_POST['edit_address'])){
                $id       = test_input($_POST['edit_id']);
                $name     = test_input($_POST['edit_name']);
                $phone    = test_input($_POST['edit_phone']);
                $email    = test_input($_POST['edit_email']);
                $address  = test_input($_POST['edit_address']);

                //UPDATE THE USER DATA
                $stmt = $conn->prepare("UPDATE tbl_customers SET phone = :phone, first_name = :name, address_1 = :address, email = :email WHERE cm_id = :id && member_id = :member_id");
                $row = $stmt->execute([":member_id"=>$ac_user, ":id"=>$id, ":phone"=>$phone, ":name"=>$name, ":address"=>$address, ":email" => $email]);
                if ($row) {
                    echo "User updated successfully !";
                } else {
                    echo "Failed to update User !";
                }
            }
        }else if (isset($_POST['delete_customer']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $deleteCustomerId = test_input($_POST['uid']);
                $stmt = $conn->prepare("DELETE FROM tbl_customers WHERE cm_id = ?");
                $deleteCustomer = $stmt->execute([$deleteCustomerId]);
                if ($deleteCustomer) {
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