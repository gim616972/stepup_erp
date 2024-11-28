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

        if (isset($_POST['edit_product_promt']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {

                $editProductId = test_input($_POST['uid']);
                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :username && member_id = :member_id");
                $stmt->execute([":username"=>$editProductId, ":member_id"=>$ac_user]);
                $editProduct = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($editProduct)>0) {
                    foreach ($editProduct as $productData) {
                    ?>
                        <input type="hidden" id="edit_courier_id" value="<?php echo $productData['sku']; ?>">
                        <div class="mb-3">
                            <label for="edit_images" class="form-label">Images</label>
                            <input type="text" id="edit_images" class="form-control" value="<?php echo $productData['images']; ?>" placeholder="Enter your Name">
                        </div>
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" id="edit_name" class="form-control" value="<?php echo $productData['name']; ?>" placeholder="Enter your Phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_categories" class="form-label">categories</label>
                            <input type="text" id="edit_categories" class="form-control" value="<?php echo $productData['categories']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="mb-3">
                            <label for="edit_weight" class="form-label">weight</label>
                            <input type="text" id="edit_weight" class="form-control" value="<?php echo $productData['weight']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">price</label>
                            <input type="text" id="edit_price" class="form-control" value="<?php echo $productData['price']; ?>" placeholder="Enter your email address">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="edit_product_Data" class="btn btn-primary text-white rounded-0">Edit User</button>
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
        } else if (isset($_POST['edit_product_changes']) && isset($_POST['edit_courier_id']) && isset($_POST['edit_images']) && isset($_POST['edit_name']) && isset($_POST['edit_categories']) && isset($_POST['edit_weight']) && isset($_POST['edit_weight']) && isset($_POST['edit_price'])) {
            if(!empty($_POST['edit_product_changes']) && !empty($_POST['edit_courier_id']) && !empty($_POST['edit_images']) && !empty($_POST['edit_name']) && !empty($_POST['edit_categories']) && !empty($_POST['edit_weight']) && !empty($_POST['edit_weight']) && !empty($_POST['edit_price'])){
                $id = test_input($_POST['edit_courier_id']);
                $image = test_input($_POST['edit_images']);
                $name     = test_input($_POST['edit_name']);
                $categories    = test_input($_POST['edit_categories']);
                $weight    = test_input($_POST['edit_weight']);
                $price    = test_input($_POST['edit_price']);
            
                //UPDATE THE USER DATA
                $stmt = $conn->prepare("UPDATE tbl_product SET images = :images, name = :name, categories = :categories, weight = :weight, price = :price WHERE sku = :id && member_id = :member_id");
                $row = $stmt->execute([":member_id"=>$ac_user, ":id"=>$id, ":images" => $image, ":name"=>$name, ":categories"=>$categories, ":weight" => $weight, ":price" => $price]);
                if ($row) {
                    echo "Courier updated successfully !";
                } else {
                    echo "Failed to update Courier !";
                }
            }
        } else if (isset($_POST['delete_product']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $deleteProductId = test_input($_POST['uid']);
                $stmt = $conn->prepare("DELETE FROM tbl_product WHERE sku = ?");
                $deleteProduct = $stmt->execute([$deleteProductId]);
                if ($deleteProduct) {
                    echo "success";
                } else {
                    echo "Failed to delete user !";
                }
            }
        } else if (isset($_POST['change_product_status']) && isset($_POST['uid'])) {
            if (!empty($_POST['uid'])) {
                $changeProductStatusId = test_input($_POST['uid']);
                $stmt = $conn->prepare("UPDATE tbl_product SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END WHERE sku = ?");
                $changeProductStatus = $stmt->execute([$changeProductStatusId]);
                $stmt = null;
                if ($changeProductStatus) {
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