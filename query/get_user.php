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
        if (isset($_POST['edit_user_promt']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $edituserId = test_input($_POST['uid']);
                $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE username = :username && member_id = :member_id");
                $stmt->execute([":username"=>$edituserId, ":member_id"=>$ac_user]);
                $editUser = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($editUser)>0) {
                    foreach ($editUser as $userData) {
                    ?>
                        <input type="hidden" id="user_id" value="<?php echo $userData['username']; ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="edit_name" class="form-control" value="<?php echo $userData['name']; ?>" placeholder="Enter your Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="edit_email" class="form-control" value="<?php echo $userData['email']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="edit_password" class="form-control" placeholder="Enter your Password">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="editUserData" class="btn btn-primary text-white rounded-0">Edit User</button>
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
        } else if (isset($_POST['edit_permission_promt']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $editPermId = test_input($_POST['uid']);
                $stmt = $conn->prepare("SELECT permission FROM tbl_user_permission WHERE username = :username && member_id = :member_id");
                $stmt->execute([":username"=>$editPermId, ":member_id"=>$ac_user]);
                $currentPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Permission categories
                $orderPermissions     = ['view_order', 'add_order', 'edit_order', 'delete_order', 'edit_order_status'];
                $productPermissions   = ['view_product', 'add_product', 'edit_product', 'delete_product'];
                $customerPermissions  = ['view_customer', 'add_customer', 'edit_customer', 'delete_customer'];
                $courierPermissions   = ['view_courier', 'add_courier', 'edit_courier', 'delete_courier'];
                $warehousePermissions = ['view_warehouse', 'add_warehouse', 'edit_warehouse', 'delete_warehouse'];
                $companyPermission    = ['view_company_settings', 'add_company_settings', 'update_company_settings', 'remove_company_settings']
                ?>
                    <input type="hidden" name="user_id" value="<?php echo $editPermId; ?>">
                    
                    <!-- Order Permissions Section -->
                    <h5 class="text-primary">Order Permission</h5>
                    <?php foreach ($orderPermissions as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
                
                    <!-- Product Permissions Section -->
                    <h5 class="text-primary">Product Permission</h5>
                    <?php foreach ($productPermissions as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
                
                    <!-- Customer Permissions Section -->
                    <h5 class="text-primary">Customer Permission</h5>
                    <?php foreach ($customerPermissions as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
                
                    <!-- Courier Permissions Section -->
                    <h5 class="text-primary">Courier Permission</h5>
                    <?php foreach ($courierPermissions as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
                
                    <!-- Warehouse Permissions Section -->
                    <h5 class="text-primary">Warehouse Permission</h5>
                    <?php foreach ($warehousePermissions as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
                    
                    <!-- Company Settings Permissions Section -->
                    <h5 class="text-primary">Company Settings Permission</h5>
                    <?php foreach ($companyPermission as $permission): ?>
                        <label class="me-2 mb-4">
                            <input type="checkbox" name="permissions[]" value="<?php echo $permission; ?>"
                            <?php if (in_array($permission, $currentPermissions)) echo 'checked'; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </label>
                    <?php endforeach; ?>
    
                    <div class="d-flex justify-content-end pt-2">
                        <button type="button" id="editPermissionData" class="btn btn-primary text-white rounded-0">Update Permissions</button>
                    </div>
                <?php
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