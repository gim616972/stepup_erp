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
        
        if (isset($_POST['order_id']) and !empty($_POST['order_id'])) {
            if (hasPermission($ac_username, $ac_user, "view_product", $conn)) {
            
                $order_id = test_input($_POST['order_id']);
                $stmt = $conn->prepare("SELECT * FROM tbl_order_items WHERE inv_id = :inv_id");
                $stmt->execute([":inv_id"=>$order_id]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)>0) {
                    ?>
                    <div class="table-responsive">
                        <table class="table">
                            <tr id="heading_tr">
                                <th class="t_head">SL</th>
                                <th class="t_head">Product ID</th>
                                <th class="t_head">Product Name</th>
                                <th class="t_head">Qty</th>
                                <th>Price</th>
                            </tr>
                            <?php
                            foreach ($result as $products) {
                                $sku = $products['sku'];
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :sku");
                                $stmt->execute([":sku"=>$sku]);
                                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($res)>0) {
                                    $i=0;
                                    foreach ($res as $prod) {
                                        $i++;
                                    ?>
                                        <tr id="body_tr">
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $products['sku']; ?></td>
                                            <td><?php echo $prod['name']; ?></td>
                                            <td><?php echo $products['quantity']; ?></td>
                                            <td><?php echo $products['price']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                } else {
                    echo "failed";
                }
            } else {
                echo "failed";
            }
        } else {
            echo "failed";
        }
    } else {
        echo "failed";
    }
} else {
    echo "failed";
}
?>
