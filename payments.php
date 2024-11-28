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
?>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8 offset-0 offset-lg-2">
                    <div class="card border-0">
                        <div class="card-body">
                            
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