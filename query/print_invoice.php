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
                    <style>
                        .print_text {
                            font-size: 30px;
                            font-weight: 600;
                            padding-right: 15px;
                            margin-bottom: 5px;
                        }
                        #printPage {
                            font-size: 30px;
                        }
                        .print-area {
                            visibility: collapse;
                            height: 0px;
                        }
                        @media print {
                            body {
                                visibility: hidden;
                            }
                            .print-area {
                                visibility: visible;
                            }
                            .p_area {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100vw;
                            }
                            .cm_name {
                                font-size: 30px;
                                font-weight: 600;
                            }
                            .p_head {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 20px;
                                font-weight: 600;
                            }
                            .bill_p {
                                display: grid;
                            }
                            .inv_p {
                                display: flex;
                                flex-direction: column;
                            }
                        }
                    </style>
                    <div class="print-area">
                        <div class="p_area">
                            <div class="p_head">
                                <img src="">
                                <p class="cm_name m-0">Company Name</p>
                                <div></div>
                            </div>
                            <?php
                            $subTotal   = 0;
                            $delivery   = 0;
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE invoice_id = :invoice_id");
                            $stmt->execute([":invoice_id"=>$order_id]);
                            $pResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($pResult)>0) {
                                foreach ($pResult as $order_data) {
                                    $delivery += $order_data['shipping_total'];
                                    $phone = $order_data['phone'];
                                    $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE phone = :phone");
                                    $stmt->execute([":phone"=>$phone]);
                                    $cResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($cResult)>0) {
                                        foreach ($cResult as $customer) {
                                        ?>
                                            <div class="p_head">
                                                <div>
                                                    <p class="m-0">Bill To:</p>
                                                    <div class="bill_p">
                                                        <span><?php echo $customer['first_name']." ".$customer['last_name']; ?></span>
                                                        <span><?php echo $customer['phone']; ?></span>
                                                        <span><?php echo $customer['address_1'].", ".$customer['country']; ?></span>
                                                    </div>
                                                </div>
                                                <div class="inv_p">
                                                    <span>Invoice NO: <span><?php echo $order_id; ?></span></span>
                                                    <span>Date: <span><?php echo $order_data['date_created']; ?></span>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    }
                                }
                            }
                            ?>
                            <table class="table table-bordered table-light">
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th>Item Name</th>
                                    <th class="text-center">Weight</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price (BDT)</th>
                                    <th class="text-end">Amount (BDT)</th>
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
                                            $subTotal += $prod['price']*$products['quantity'];
                                            
                                        ?>
                                            <tr id="body_tr">
                                                <td class="text-center"><?php echo $i; ?></td>
                                                <td><?php echo $prod['name']; ?></td>
                                                <td class="text-center"><?php echo $prod['weight']; ?></td>
                                                <td class="text-center"><?php echo $products['quantity']; ?></td>
                                                <td class="text-end"><?php echo number_format($prod['price'], 2); ?></td>
                                                <td class="text-end"><?php echo number_format($prod['price']*$products['quantity'], 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="4"></td>
                                    <td class="text-end">Sub Total</td>
                                    <td class="text-end"><?php echo $subTotal; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td class="text-end">Delivery Fee</td>
                                    <td class="text-end"><?php echo $delivery; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td class="text-end">Grand total</td>
                                    <td class="text-end"><?php echo $subTotal+$delivery; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex pt-3 pb-5 justify-content-center"><p class="print_text">Print Invoice</p><button class="btn btn-primary text-white" id="printPage"><i class="fa fa-print"></i></button></div>
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
