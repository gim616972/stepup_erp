<?php
session_start();
include "../concat/db_con.php";
include "../query/permission_functions.php";
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
        if (hasPermission($ac_username, $ac_user, "view_order", $conn)) {
        ?>
            <!--requested orders-->
            <div class="col-12 offset-0">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE member_id = :member_id && status = :status ORDER BY id DESC");
                        $stmt->execute([":member_id"=>$ac_user, ":status"=>3]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($result)>0) {
                        ?>
                        <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                            <table class="table mb-0">
                                <tr id="heading_tr">
                                    <th class="check_box text-center"><input class="form-check-input ms-2" type="checkbox" value="" id="checkAll"></th>
                                    <th class="t_head">Invoice No</th>
                                    <th class="t_head">Date</th>
                                    <th class="t_head">Customer</th>
                                    <th class="t_head">Payments Info</th>
                                    <th>Delivery Fee</th>
                                </tr>
                                    <?php
                                    foreach ($result as $orders) {
                                    ?>
                                        <tr id="body_tr">
                                            <td class="text-center"><input class="form-check-input ms-2" type="checkbox" value="<?php echo $orders['invoice_id']; ?>" id="checkInvoice"></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="info_area">
                                                        <i id="order_data" role="button" order_id="<?php echo $orders['invoice_id']; ?>" class="fa fa-info-circle pe-2 text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Order Items"></i>
                                                        <i id="copy_inv" role="button" order_id="<?php echo $orders['invoice_id']; ?>" class="fa fa-copy pe-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy"></i>
                                                        <i id="print_inv" role="button" order_id="<?php echo $orders['invoice_id']; ?>" class="fa fa-print pe-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Print Invoice"></i>
                                                        <i id="fraud_check" role="button" order_id="<?php echo $orders['invoice_id']; ?>" class="fa fa-user-secret" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Fraud Check"></i>
                                                    </div>
                                                    <span class="text-dinfo small_text py-2"><?php echo $orders['invoice_id']; ?></span>
                                                    <div class="mt-2">
                                                        <span class="px-3 py-1 bg-light text-warning border"><?php echo $orders['order_source']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-grid small_text">
                                                    <p class="mb-1"><span style="font-weight: 500;">Shipping </span><?php echo $orders['date_created']; ?></p>
                                                    <p class="mb-1"><span style="font-weight: 500;">Created </span><?php echo $orders['date_entry']; ?></p>
                                                    <p class="mb-1"><span style="font-weight: 500;">Processing </span><?php echo $orders['date_action']; ?></p>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $phone = $orders['phone'];
                                                $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE member_id = :member_id AND phone = :phone");
                                                $stmt->execute([":phone"=>$phone, ":member_id"=>$ac_user]);
                                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                if (count($result)>0) {
                                                    foreach ($result as $customer) {
                                                    ?>
                                                        <p class="text-dinfo mb-2"><?php echo $customer['first_name']." ".$customer['last_name']?></p>
                                                        <div class="pb-2">
                                                            <span class="phone_number"><?php echo $customer['phone']; ?></span><span class="ps-2"><i role="button" class="fa fa-copy copy_btn" cp_phone="<?php echo $customer['phone']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy"></i></span>
                                                        </div>
                                                        <p class="mb-0 small_text"><?php echo $customer['address_1'].", ".$customer['country']; ?></p>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="d-grid small_text">
                                                    <p class="mb-1"><span style="font-weight: 500;">Sales Amount:</span><?php echo " BDT ".number_format($orders['total'], 2); ?></p>
                                                    <p class="mb-1"><span style="font-weight: 500;">Paid Amount:</span><?php echo " BDT ".number_format($orders['shipping_total'], 2); ?></p>
                                                    <p class="mb-1"><span style="font-weight: 500;">Due Amount:</span><?php echo " BDT ".number_format($orders['shipping_total'], 2); ?></p></div>
                                            </td>
                                            <td>
                                                <span class="text-dinfo"><?php echo "BDT ".number_format($orders['shipping_total'], 2); ?></span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                            </table>
                        </div>
                        <?php
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
                        ?>
                    </div>
                </div>
            </div>
            <!--requested orders-->
        <?php
        }
    } else {
        echo "Please Login !";
    }
} else {
    echo "Please Login !";
}
?>