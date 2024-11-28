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

    if (hasPermission($ac_username, $ac_user, "add_courier", $conn)) {
    ?>
    <!--modal-->
    <div class="modal fade modal-lg" id="modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="cog_load h-100 w-100" style="display: none; position: absolute;background: rgb(0, 0, 0, 0.1);z-index: 1;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="fa fa-cog fa-spin" style="font-size: 50px;"></i>
                    </div>
                </div>
                <div class="modal-body px-5 pt-5">
                    <div class="d-flex justify-content-between pb-3">
                        <h4 class="text-primary">Add Delivery Partners</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-3">
                            <label for="courier" class="form-label">Courier Name</label>
                            <select class="form-control" id="courier">
                                <option value="" selected>Select Delivery Partners</option>
                                <option value="Steadfast">Steadfast</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="api_key" class="form-label">Api Key</label>
                            <input type="text" id="api_key" class="form-control" placeholder="Api Key ( bpvgojpmxlrvvfbokt2u4wj3uyhw9ltr )">
                        </div>
                        <div class="mb-3">
                            <label for="secret_key" class="form-label">Secret Key</label>
                            <input type="text" id="secret_key" class="form-control" placeholder="Secret Key ( to8s55l7o5hfovgo8tgk8goh )">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_courier" class="btn btn-primary text-white rounded-0">Add Courier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->
    <?php } ?>
    <!--user promt modal start-->
    <div class="modal fade modal-lg" id="curier_promt_modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
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
                        <div id="courier_promt_data"></div>
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
                                <h4 style="align-self: center; margin: 0px;">Delivery Partners</h4>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "add_courier", $conn)) {
                                ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-primary text-white rounded-0" data-bs-target="#modal_data" data-bs-toggle="modal">Add Courier</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--curier-->
                <?php
                if (hasPermission($ac_username, $ac_user, "view_courier", $conn)) {
                ?>
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM tbl_courier WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result)>0) {
                            ?>
                            <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                <table class="table mb-0">
                                    <tr id="heading_tr">
                                        <th class="t_head">ID</th>
                                        <th class="t_head">Name</th>
                                        <th class="t_head">Api-Key</th>
                                        <th class="t_head">Secret-Key</th>
                                        <th class="t_head">On-Board Date</th>
                                        <th class="t_head">Status</th>
                                        <th>Action</th>
                                    </tr>
                                        <?php
                                        foreach ($result as $courier) {
                                        ?>
                                            <tr id="body_tr">
                                                <td>
                                                    <span class="text-dinfo"><?php echo $courier['c_id']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-dinfo"><?php echo $courier['courier_name']; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo $courier['api_key']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $courier['secret_key']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $courier['date']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($courier['status'] == 0) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" uid="<?php echo $courier['c_id']; ?>" type="checkbox" id="mySwitch">
                                                        </div>
                                                    <?php
                                                    } else if ($courier['status'] == 1) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" uid="<?php echo $courier['c_id']; ?>" type="checkbox" id="mySwitch" checked>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (hasPermission($ac_username, $ac_user, "edit_courier", $conn)) {
                                                    ?>
                                                        <button id="editCourier" uid="<?php echo $courier['c_id']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Courier"><i class="fas fa-edit"></i></button>
                                                    <?php
                                                    }
                                                    if (hasPermission($ac_username, $ac_user, "delete_courier", $conn)) {
                                                    ?>
                                                        <button id="deleteCourier" uid="<?php echo $courier['c_id']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Courier"><i class="fas fa-trash"></i></button>
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
        $("#add_courier").click(function(){
            var courier    = $("#courier").val();
            var api_key    = $("#api_key").val();
            var secret_key = $("#secret_key").val();
            $(".cog_load").show();
            $.ajax({
                url: 'query/add_courier',
                method: 'POST',
                data: {"add_courier":"add_courier", "courier":courier, "api_key":api_key, "secret_key":secret_key},
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });

        //==== edit curiar data start ====

        //show curiar data
        $(document).on('click', '#editCourier', function(){
            var uid = $(this).attr('uid');
            
            $(".action_title").html('Edit Courier');
            $("#courier_promt_data").html("");
            $("#curier_promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/get_courier',
                method: 'POST',
                data: {"edit_curier_promt":"edit_curier_promt", "uid":uid},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $("#courier_promt_data").html(data);
                }
            });
        });
            //save changes
            $(document).on('click', "#edit_courier_Data", function(){
            const edit_id = $("#courier_id").val();
            const edit_name       = $("#edit_name").val();
            const edit_api_key    = $("#edit_api_key").val();
            const edit_secret_key = $("#edit_secret_key").val();
            
            $.ajax({
                url: "query/get_courier",
                method: 'POST',
                data: {"edit_courier_changes": "edit_courier_changes","edit_id":edit_id, "edit_name": edit_name, "edit_api_key": edit_api_key, "edit_secret_key": edit_secret_key},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $(".promt_err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        //==== edit curiar data end ====

        //delete customer data
        $(document).on('click', "#deleteCourier", function(){
            const uid = $(this).attr('uid');
            $.ajax({
                url: 'query/get_courier',
                method: 'POST',
                data: {"delete_courier":"delete_courier", "uid":uid},
                success: function(data) {
                    if (data === "success") {
                        alert ("User deleted successfully !");
                    } else {
                        alert(data);
                    }
                }
            });
        });

        //change status
        $(document).on('click', "#mySwitch", function(){
            const uid = $(this).attr('uid');
            $.ajax({
                url: 'query/get_courier',
                method: 'POST',
                data: {"change_status":"change_status", "uid":uid},
                success: function(data) {
                    // if (data === "success") {
                    //     alert ("status chenged successfully !");
                    // } else {
                    //     alert("status chenged failed");
                    // }
                }
            });
        });
    });
</script>