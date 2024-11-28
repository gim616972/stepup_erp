<?php
include "header.php";
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
    
    if (hasPermission($ac_username, $ac_user, "add_customer", $conn)) {
?>
    <!--modal start-->
    <div class="modal fade modal-lg" id="modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="cog_load h-100 w-100" style="display: none; position: absolute;background: rgb(0, 0, 0, 0.1);z-index: 1;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="fa fa-cog fa-spin" style="font-size: 50px;"></i>
                    </div>
                </div>
                <div class="modal-body px-4 pt-4">
                    <div class="d-flex justify-content-between pb-2">
                        <h4 class="">Add Customer</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <f  orm method="POST" class="mb-1">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-2">
                            <label for="cm_phone" class="form-label">Phone Number</label>
                            <input type="number" id="cm_phone" class="form-control" placeholder="01700000000">
                        </div>
                        <div class="mb-2">
                            <label for="cm_name" class="form-label">Customer Name</label>
                            <input type="text" id="cm_name" class="form-control" placeholder="John Doe">
                        </div>
                        <div class="mb-2">
                            <label for="cm_address" class="form-label">Customer Address</label>
                            <input type="text" id="cm_address" class="form-control" placeholder="Mirpur, Dhaka">
                        </div>
                        <div class="mb-2">
                            <label for="cm_email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
                            <input type="email" id="cm_email" class="form-control" placeholder="mail@example.com">
                        </div>
                        <div class="mb-2">
                            <label for="cm_source" class="form-label">Customer source</label>
                            <select class="form-control" id="cm_source">
                                <option value="Other" selected>Other</option>
                                <option value="Website">Website</option>
                                <option value="Offline">Offline</option>
                                <option value="Messenger">Messenger</option>
                                <option value="Whatsapp">Whatsapp</option>
                                <option value="Instagram">Instagram</option>
                                <option value="TikTok">TikTok</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_customer" class="btn btn-primary text-white rounded-0">Add Customer</button>
                        </div>
                    </f>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    <?php } ?>
    <!--user promt modal start-->
    <div class="modal fade modal-lg" id="customer_promt_modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="promt_cog_load h-100 w-100" style="display: none; position: absolute;background: rgb(0, 0, 0, 0.1);z-index: 1;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="fa fa-cog fa-spin" style="font-size: 50px;"></i>
                    </div>
                </div>
                <div class="modal-body px-5 pt-5">
                    <div class="d-flex justify-content-between pb-3">
                        <h4 class="action_title"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" id="serializeFormData">
                        <div class="alert alert-warning promt_err_modal" style="display:none;" role="alert"></div>
                        <div id="customer_promt_data"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--user promt modal end-->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <!--variant-->
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap align-content-stretch gap-2">
                                <h4 style="align-self: center; margin: 0px;">Customers</h4>
                                <div class="d-flex">
                                    <?php
                                    if (hasPermission($ac_username, $ac_user, "view_customer", $conn)) {
                                    ?>
                                        <div class="prod_count">
                                            <div class="text-secondary">
                                                <span class="pe-2">Total Customers</span><i class="fa fa-info-circle"></i>
                                            </div>
                                            <h5 class="pt-1">6</h5>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "add_customer", $conn)) {
                                ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-primary text-white rounded-0" data-bs-target="#modal_data" data-bs-toggle="modal">Add Customer</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--customer-->
                <?php
                if (hasPermission($ac_username, $ac_user, "view_customer", $conn)) {
                ?>
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result)>0) {
                            ?>
                            <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                <table class="table mb-0">
                                    <tr id="heading_tr">
                                        <th class="t_head">ID</th>
                                        <th class="t_head">Contact</th>
                                        <th class="t_head">Location</th>
                                        <th class="t_head">Orders</th>
                                        <th class="t_head">Created At</th>
                                        <th class="t_head">Last Order Date</th>
                                        <th>Action</th>
                                    </tr>
                                        <?php
                                        foreach ($result as $customer) {
                                        ?>
                                            <tr id="body_tr">
                                                <td>
                                                    <div class="d-grid">
                                                        <p class="text-dinfo"><?php echo $customer['cm_id']; ?></p>
                                                        <div>
                                                            <span class="px-2 py-1 bg-light text-warning border"><?php echo $customer['source']; ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-dinfo"><?php echo $customer['first_name']." ".$customer['last_name']."<br>".$customer['phone']."<br>".$customer['email']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-dinfo"><?php echo $customer['address_1']; ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $phone = $customer['phone'];
                                                    $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE member_id = :member_id AND phone = :phone");
                                                    $stmt->execute([":phone"=>$phone, "member_id"=>$ac_user]);
                                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    echo count($result);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo $customer['created_at']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($customer['last_order'] == "00-00-0000") {
                                                        echo "<span class='text-muted'>No orders yet</span>";
                                                    } else {
                                                        echo $customer['last_order'];
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (hasPermission($ac_username, $ac_user, "edit_customer", $conn)) {
                                                    ?>
                                                        <button id="editCustomer" uid="<?php echo $customer['cm_id']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Customer"><i class="fas fa-edit"></i></button>
                                                    <?php
                                                    }
                                                    if (hasPermission($ac_username, $ac_user, "delete_customer", $conn)) {
                                                    ?>
                                                        <button id="deleteCustomer" uid="<?php echo $customer['cm_id']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Customer"><i class="fas fa-trash"></i></button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
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
                <?php } ?>
            </div>
        </div>
    </div>
<?php
    } else {
        echo '<script>window.location="logout";</script>';
    }
} else {
    echo '<script>window.location="logout";</script>';
}
include "footer.php";
?>
<script>
    $(document).ready(function(){
        $("#add_customer").click(function(){
            var cm_phone   = $("#cm_phone").val();
            var cm_name    = $("#cm_name").val();
            var cm_address = $("#cm_address").val();
            var cm_email   = $("#cm_email").val();
            var cm_source  = $("#cm_source").val();
            $(".cog_load").show();
            $.ajax({
                url: 'query/add_customer',
                method: 'POST',
                data: {"add_customer":"add_customer", "cm_phone":cm_phone, "cm_name":cm_name, "cm_address":cm_address, "cm_email":cm_email, "cm_source":cm_source},
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });

        // === edit Customer data start =====

        //show data
        $(document).on('click', '#editCustomer', function(){
            var uid = $(this).attr('uid');
            $(".action_title").html('Edit Customer');
            $("#customer_promt_data").html("");
            $("#customer_promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/get_customer',
                method: 'POST',
                data: {"edit_customer_promt":"edit_customer_promt", "uid":uid},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $("#customer_promt_data").html(data);
                }
            });
        });

        //save changes
        $(document).on('click', "#edit_customer_Data", function(){
            const edit_id       = $("#user_id").val();
            const edit_name     = $("#edit_name").val();
            const edit_phone    = $("#edit_phone").val();
            const edit_email    = $("#edit_email").val();
            const edit_address  = $("#edit_address").val();

            $.ajax({
                url: "query/get_customer",
                method: 'POST',
                data: {"edit_customer_changes": "edit_customer_changes", "edit_id": edit_id, "edit_name": edit_name, "edit_phone": edit_phone, "edit_email": edit_email, "edit_address": edit_address},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $(".promt_err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });

        // ==== edit Customer data end ====

        //delete customer data
        $(document).on('click', "#deleteCustomer", function(){
            const uid = $(this).attr('uid');
            $.ajax({
                url: 'query/get_customer',
                method: 'POST',
                data: {"delete_customer":"delete_customer", "uid":uid},
                success: function(data) {
                    if (data === "success") {
                        alert ("User deleted successfully !");
                    } else {
                        alert(data);
                    }
                }
            });
        });
    });
</script>