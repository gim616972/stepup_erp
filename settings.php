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
        
    if (hasPermission($ac_username, $ac_user, "add_company_settings", $conn)) {
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
                        <h4 class="">Add Another Integration</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-3">
                            <label for="wp_url" class="form-label">Wordpress URL</label>
                            <input type="text" id="wp_url" class="form-control" placeholder="Wordpress URL (https://www.example.com)">
                        </div>
                        <div class="mb-3">
                            <label for="cm_key" class="form-label">Consumer Key</label>
                            <input type="text" id="cm_key" class="form-control" placeholder="Consumer Key (ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)">
                        </div>
                        <div class="mb-3">
                            <label for="cm_secret" class="form-label">Consumer Secret</label>
                            <input type="text" id="cm_secret" class="form-control" placeholder="Consumer Secret (cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_wp_config" class="btn btn-primary text-white rounded-0">Add Integration</button>
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
                <div class="col-12 col-lg-8 offset-0 offset-lg-2">
                    <div class="card border-0">
                        <div class="card-body p-xl-5">
                            <div class="d-flex justify-content-between flex-wrap my-3">
                                <h3>Integration</h3>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "add_company_settings", $conn)) {
                                ?>
                                    <button class="btn btn-primary rounded-0 text-white" data-bs-target="#modal_data" data-bs-toggle="modal">Add Another Integration</button>
                                <?php } ?>
                            </div>
                            <?php
                            if (hasPermission($ac_username, $ac_user, "view_company_settings", $conn)) {
                                $stmt = $conn->prepare("SELECT * FROM tbl_wp_config WHERE member_id = :member_id");
                                $stmt->execute([":member_id"=>$ac_user]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result)>0) {
                                    foreach ($result as $row) {
                                    ?>
                                    <form method="POST" id="wp-config">
                                        <div class="alert alert-warning err" style="display:none;" role="alert"></div>
                                        <input type="hidden" id="up_uu_id" value="<?php echo $row['uu_id']; ?>">
                                        <div class="mb-3">
                                            <label for="up_wp_url" class="form-label">Wordpress URL</label>
                                            <input type="text" id="up_wp_url" class="form-control" value="<?php echo $row['site_url']; ?>" placeholder="Wordpress URL (https://www.example.com)">
                                        </div>
                                        <div class="mb-3">
                                            <label for="up_cm_key" class="form-label">Consumer Key</label>
                                            <input type="text" id="up_cm_key" class="form-control" value="<?php echo $row['consumer_key']; ?>" placeholder="Consumer Key (ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)">
                                        </div>
                                        <div class="mb-3">
                                            <label for="up_cm_secret" class="form-label">Consumer Secret</label>
                                            <input type="text" id="up_cm_secret" class="form-control" value="<?php echo $row['consumer_secret']; ?>" placeholder="Consumer Secret (cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <?php
                                            if (hasPermission($ac_username, $ac_user, "remove_company_settings", $conn)) {
                                                echo '<button type="button" id="remove_wp_config" class="btn btn-danger text-white rounded-0">Remove</button>';
                                            }
                                            if (hasPermission($ac_username, $ac_user, "update_company_settings", $conn)) {
                                                echo '<button type="button" id="update_wp_config" class="btn btn-primary text-white rounded-0">Update</button>';
                                            }
                                            ?>
                                        </div>
                                    </form>
                                    <?php
                                    }
                                } else {
                                ?>
                                    <div class="d-flex justify-content-center text-center pt-4 pb-5">
                                        <div class="d-grid border border-light-subtle p-5 rounded">
                                            <i class="fa fa-cube" style="font-size: 50px;"></i>
                                            <span style="font-weight: 600;font-size: 30px;">No Data Found!</span>
                                            <p class="mb-0">No data to display currently.</p>
                                        </div>
                                    </div>
                                <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
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
        $("#add_wp_config").click(function(){
            var wp_url    = $("#wp_url").val();
            var cm_key    = $("#cm_key").val();
            var cm_secret = $("#cm_secret").val();
            $(".cog_load").show();
            $.ajax({
                url: 'integration/wp_config',
                method: 'POST',
                data: {"wp_new":"wp_new", "wp_url":wp_url, "cm_key":cm_key, "cm_secret":cm_secret},
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        
        // update wp config
        $("#update_wp_config").click(function(){
            var up_uu_id     = $("#up_uu_id").val();
            var up_wp_url    = $("#up_wp_url").val();
            var up_cm_key    = $("#up_cm_key").val();
            var up_cm_secret = $("#up_cm_secret").val();
            $.ajax({
                url: 'query/update_wpconfig',
                method: 'POST',
                data: {"wp_update":"wp_update", "up_uu_id":up_uu_id, "up_wp_url":up_wp_url, "up_cm_key":up_cm_key, "up_cm_secret":up_cm_secret},
                success: function(data) {
                    $(".err").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        
        // remove wp config
        $("#remove_wp_config").click(function(){
            var up_uu_id = $("#up_uu_id").val();
            $.ajax({
                url: 'query/update_wpconfig',
                method: 'POST',
                data: {"wp_remove":"wp_remove", "up_uu_id":up_uu_id},
                success: function(data) {
                    if (data == "success") {
                        $("#wp-config").html(`<div class="d-flex justify-content-center text-center pt-4 pb-5">
                                                <div class="d-grid border border-light-subtle p-5 rounded">
                                                    <i class="fa fa-cube" style="font-size: 50px;"></i>
                                                    <span style="font-weight: 600;font-size: 30px;">No Data Found!</span>
                                                    <p class="mb-0">No data to display currently.</p>
                                                </div>
                                            </div>`);
                    } else {
                        $(".err").fadeIn().delay(2000).fadeOut(2000).html(data);
                    }
                }
            });
        });
        
    });
</script>