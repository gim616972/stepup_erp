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
        
    if (hasPermission($ac_username, $ac_user, "add_warehouse", $conn)) {
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
                        <h4 class="">Add Warehouse</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-3">
                            <label for="w_name" class="form-label">Warehouse Name</label>
                            <input type="text" id="w_name" class="form-control" placeholder="Enter your warehouse name">
                        </div>
                        <div class="mb-3">
                            <label for="w_address" class="form-label">Warehouse Address</label>
                            <input type="text" id="w_address" class="form-control" placeholder="Enter your warehouse address">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_warehouse" class="btn btn-primary text-white rounded-0">Add Warehouse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->
    <?php } ?>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <!--variant-->
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap align-content-stretch gap-2">
                                <h4 style="align-self: center; margin: 0px;">Warehouse</h4>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "add_warehouse", $conn)) {
                                ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-primary text-white rounded-0" data-bs-target="#modal_data" data-bs-toggle="modal">Add Warehouse</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--warehouse-->
                <?php
                if (hasPermission($ac_username, $ac_user, "view_warehouse", $conn)) {
                ?>
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM tbl_warehouse WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result)>0) {
                            ?>
                            <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                <table class="table mb-0">
                                    <tr id="heading_tr">
                                        <th class="t_head">ID</th>
                                        <th class="t_head">Warehouse Name</th>
                                        <th class="t_head">Warehouse Address</th>
                                        <th class="t_head">On-Board Date</th>
                                        <th class="t_head">Status</th>
                                        <th>Action</th>
                                    </tr>
                                        <?php
                                        foreach ($result as $warehouse) {
                                        ?>
                                            <tr id="body_tr">
                                                <td>
                                                    <span class="text-dinfo"><?php echo $warehouse['w_id']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-dinfo"><?php echo $warehouse['warehouse_name']; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo $warehouse['warehouse_address']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $warehouse['date']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($warehouse['status'] == 0) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" type="checkbox" id="mySwitch">
                                                        </div>
                                                    <?php
                                                    } else if ($warehouse['status'] == 1) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" type="checkbox" id="mySwitch" checked>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (hasPermission($ac_username, $ac_user, "edit_warehouse", $conn)) {
                                                    ?>
                                                        <button id="editWarehouse" uid="<?php echo $warehouse['w_id']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Warehouse"><i class="fas fa-edit"></i></button>
                                                    <?php
                                                    }
                                                    if (hasPermission($ac_username, $ac_user, "delete_warehouse", $conn)) {
                                                    ?>
                                                        <button id="deleteWarehouse" uid="<?php echo $warehouse['w_id']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Warehouse"><i class="fas fa-trash"></i></button>
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
        $("#add_warehouse").click(function(){
            var w_name    = $("#w_name").val();
            var w_address = $("#w_address").val();
            $(".cog_load").show();
            $.ajax({
                url: 'query/add_warehouse',
                method: 'POST',
                data: {"add_warehouse":"add_warehouse", "w_name":w_name, "w_address":w_address},
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
    });
</script>