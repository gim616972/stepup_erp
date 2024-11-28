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

    if (hasPermission($ac_username, $ac_user, "add_user", $conn)) {
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
                <div class="modal-body px-5 pt-5">
                    <div class="d-flex justify-content-between pb-3">
                        <h4 class="">Add User</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Enter your Name">
                        </div>
                        <div class="mb-3">
                            <label for="userName" class="form-label">User Name</label>
                            <input type="text" id="userName" class="form-control" placeholder="Enter your Username">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" placeholder="Enter your email address">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" class="form-control" placeholder="Enter your Password">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_user" class="btn btn-primary text-white rounded-0">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    <?php } ?>
    <!--user promt modal-->
    <div class="modal fade modal-lg" id="promt_modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
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
                        <div id="promt_data"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--user promt modal-->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <!--variant-->
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap align-content-stretch gap-2">
                                <h4 style="align-self: center; margin: 0px;">User</h4>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "add_user", $conn)) {
                                ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-primary text-white rounded-0" data-bs-target="#modal_data" data-bs-toggle="modal">Add User</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--user-->
                <?php
                if (hasPermission($ac_username, $ac_user, "view_user", $conn)) {
                ?>
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result)>0) {
                            ?>
                            <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                <table class="table mb-0">
                                    <tr id="heading_tr">
                                        <th class="t_head">Username</th>
                                        <th class="t_head">Name</th>
                                        <th class="t_head">Email</th>
                                        <th class="t_head">Status</th>
                                        <th>Action</th>
                                    </tr>
                                        <?php
                                        foreach ($result as $userData) {
                                        ?>
                                            <tr id="body_tr">
                                                <td>
                                                    <span class="text-dinfo"><?php echo $userData['username']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-dinfo"><?php echo $userData['name']; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo $userData['email']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($userData['status'] == 0) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" type="checkbox" id="mySwitch">
                                                        </div>
                                                    <?php
                                                    } else if ($userData['status'] == 1) {
                                                    ?>
                                                        <div class="form-check form-switch" style="position: unset;">
                                                            <input class="form-check-input" type="checkbox" id="UserStatus" checked>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <button id="editUser" uid="<?php echo $userData['username']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit User"><i class="fas fa-edit"></i></button>
                                                    <button id="editPermission" uid="<?php echo $userData['username']; ?>" class="btn btn-success text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Permission"><i class="fas fa-lock"></i></button>
                                                    <button id="deleteUser" uid="<?php echo $userData['username']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete User"><i class="fas fa-trash"></i></button>
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
        $("#add_user").click(function(){
            var name     = $("#name").val();
            var userName = $("#userName").val();
            var email    = $("#email").val();
            var password = $("#password").val();
            $(".cog_load").show();
            $.ajax({
                url: 'query/add_user',
                method: 'POST',
                data: {"add_user":"add_user", "name":name, "userName":userName, "email":email, "password":password},
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        
        // edit user promt
        $(document).on('click', '#editUser', function(){
            var uid = $(this).attr('uid');
            $(".action_title").html('Edit User');
            $("#promt_data").html("");
            $("#promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/get_user',
                method: 'POST',
                data: {"edit_user_promt":"edit_user_promt", "uid":uid},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $("#promt_data").html(data);
                }
            });
        });
        // edit user data (letter)
        $(document).on('click', '#editUserData', function(){
            var user_id       = $("#user_id").val();
            var edit_name     = $("#edit_name").val();
            var edit_email    = $("#edit_email").val();
            var edit_password = $("#edit_password").val();
            
            $("#promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/add_user',
                method: 'POST',
                data: {"edit_user":"edit_user", "user_id":user_id, "edit_name":edit_name, "edit_email":edit_email, "edit_password":edit_password},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $(".promt_err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        
        // edit permission promt
        $(document).on('click', '#editPermission', function(){
            var uid = $(this).attr('uid');
            $(".action_title").html('Edit Admin Role');
            $("#promt_data").html("");
            $("#promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/get_user',
                method: 'POST',
                data: {"edit_permission_promt":"edit_permission_promt", "uid":uid},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $("#promt_data").html(data);
                }
            });
        });
        // edit permission data (letter)
        $(document).on('click', '#editPermissionData', function(){
            var formData = $('#serializeFormData').serialize();
            $("#promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/add_user',
                method: 'POST',
                data: formData + "&edit_permission=edit_permission",
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $(".promt_err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        
        // delete user
        $(document).on('click', '#deleteUser', function(){
            var uid = $(this).attr('uid');
            $.ajax({
                url: 'query/add_user',
                method: 'POST',
                data: {"delete_user":"delete_user", "uid":uid},
                success: function(data) {
                    if (data == "success") {
                        alert ("User deleted successfully !");
                    } else {
                        alert(data);
                    }
                }
            });
        });
    });
</script>