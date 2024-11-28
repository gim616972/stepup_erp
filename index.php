<?php
include "header.php";
date_default_timezone_set("Asia/Dhaka");
$date = date('M/d/Y');
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
?>
<style>
.dashboard_area {
    background: #f2f8fa;
}
</style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="card border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h3 class="mt-2 mb-0">Dashboard</h3>
                        </div>
                        <div>
                            <form class="my-2" action="index" method="GET">
                                <div class="row ms-1">
                                    <!--from date-->
                                    <div class="col-auto">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <label class="col-form-label fw-bolder">From:</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="date" class="form-control" value="<?php if (isset($_GET['from'])){echo $_GET['from'];} ?>" name="from" required>
                                            </div>
                                        </div>
                                    </div>
                                    <!--select team-->
                                    <div class="col-auto">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <label class="col-form-label fw-bolder">To:</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="date" class="form-control" value="<?php if (isset($_GET['to'])){echo $_GET['to'];} ?>" name="to" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary text-white"><i class="fa fa-filter"></i> Load</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="dropdown mt-2">
                            <button class="btn btn-primary text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Filter</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index?today">Today</a></li>
                                <li><a class="dropdown-item" href="index?weekly">Last 7 days</a></li>
                                <li><a class="dropdown-item" href="index?monthly">Last 30 days</a></li>
                                <li><a class="dropdown-item" href="index?yearly">Last 365 days</a></li>
                                <li><a class="dropdown-item" href="index?all">Lifetime</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $OrderCount = 0;
                        $SalesAmount = 0;
                        $CancelledOrder = 0;
                        $ReturnedOrder = 0;
                        $TotalExpanse = 0;
                        $NetProfit = 0;
                        $TotalPurchase = 0;
                        $CashInhand = 0;
                        if (isset($_GET['from']) && isset($_GET['to'])) {
                            
                        } else if (isset($_GET['all'])) {
                            // order count
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $OrderCount += count($orderData);
                            
                            // slase amount
                            $stmt = $conn->prepare("SELECT SUM(total) AS salse FROM tbl_orders WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $salseData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($salseData as $salseDatas) {
                                $SalesAmount += $salseDatas['salse'];
                            }
                            
                            // cancelled order
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE status = :status && member_id = :member_id");
                            $stmt->execute([":status"=>8, ":member_id"=>$ac_user]);
                            $cancellData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $CancelledOrder += count($cancellData);
                            
                            // returned order
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE status = :status && member_id = :member_id");
                            $stmt->execute([":status"=>7, ":member_id"=>$ac_user]);
                            $returnData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $ReturnedOrder += count($returnData);
                            
                        } else {
                            $inputDate = '2024-10-16';
                            $dateTime = new DateTime($inputDate);
                            $formattedDate = $dateTime->format('M d, Y  g:i A');
                            echo $formattedDate;
                            // order count
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE member_id = :member_id && date_created BETWEEN :date AND :date");
                            $stmt->execute([":member_id"=>$ac_user, ":date"=>$formattedDate]);
                            $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $OrderCount += count($orderData);
                            
                            // slase amount
                            $stmt = $conn->prepare("SELECT SUM(total) AS salse FROM tbl_orders WHERE member_id = :member_id");
                            $stmt->execute([":member_id"=>$ac_user]);
                            $salseData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($salseData as $salseDatas) {
                                $SalesAmount += $salseDatas['salse'];
                            }
                            
                            // cancelled order
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE status = :status && member_id = :member_id");
                            $stmt->execute([":status"=>8, ":member_id"=>$ac_user]);
                            $cancellData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $CancelledOrder += count($cancellData);
                            
                            // returned order
                            $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE status = :status && member_id = :member_id");
                            $stmt->execute([":status"=>7, ":member_id"=>$ac_user]);
                            $returnData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $ReturnedOrder += count($returnData);
                        }
                        ?>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h4>Order Count</h4>
                                <h4><?php echo $OrderCount; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Sales Amount</h5>
                                <h4><?php echo $SalesAmount; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Cancelled Order</h5>
                                <h4><?php echo $CancelledOrder; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Returned Order</h5>
                                <h4><?php echo $ReturnedOrder; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Total Expanse</h5>
                                <h4><?php echo $TotalExpanse; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Net Profit</h5>
                                <h4><?php echo $NetProfit; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Total Purchase</h5>
                                <h4><?php echo $TotalPurchase; ?></h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="dashboard_area rounded shadow-sm py-4 ps-4">
                                <h5>Cash In hand</h5>
                                <h4><?php echo $CashInhand; ?></h4>
                            </div>
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