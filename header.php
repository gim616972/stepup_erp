<?php
session_start();
include "concat/db_con.php";
include "query/permission_functions.php";
if (!isset($_SESSION['user_id']) and !isset($_SESSION['user_name']) and !isset($_SESSION['user_pass'])) {
    header('Location: login');
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
?>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-layout-style="default" data-bs-theme="light" data-layout-width="fluid" data-layout-position="fixed">
    <head>
        <meta charset="utf-8">
        <title>Dashboard | Quality Check</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="css/app.min.css" rel="stylesheet" type="text/css">
        <link href="css/custom.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </head>
<style>
.profile {
    background: #D28E79;
    width: 40px;
    height: 40px;
    border: 2px solid #ccc;
    border-radius: 50%;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}

</style>
    <body>
        <div id="layout-wrapper">
            <header id="page-topbar" class="head_shadow">
                <div class="layout-width">
                    <div class="navbar-header">
                        <div class="d-flex">
                            <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                            <span class="hamburger-icon open">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                            </button>
                        </div>

                        <div>
                            <button class="profile" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu" style="">
                                <li>
                                    <a class="dropdown-item" href="/profile">
                                        <i class="fa fa-user pe-2"></i>
                                        <span data-key="t-dashboard">Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/settings">
                                        <i class="fa fa-cog pe-2"></i>
                                        <span data-key="t-dashboard">Company Settings</span>
                                    </a>
                                </li>
                                <!--<li>-->
                                <!--    <a class="dropdown-item" href="/payments">-->
                                <!--        <i class="fa fa-credit-card pe-2"></i>-->
                                <!--        <span data-key="t-dashboard">Payments</span>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <li>
                                    <a class="dropdown-item" href="/logout">
                                        <i class="fa fa-sign-out pe-2"></i>
                                        <span data-key="t-dashboard">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <div class="app-menu navbar-menu">
                <div class="navbar-brand-box">
                    <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                        <i class="ri-record-circle-line"></i>
                    </button>
                </div>

                <div id="scrollbar" data-simplebar="" class="h-100">
                    <div class="container-fluid">
                        <div id="two-column-menu"></div>
                        <ul class="navbar-nav" id="navbar-nav" data-simplebar="">
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/">
                                    <i class="fa fa-home" aria-hidden="true"></i> <span data-key="t-dashboard">Dashboard</span>
                                </a>
                            </li>
                            <?php
                            if (hasPermission($ac_username, $ac_user, "view_order", $conn)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/orders?status=requested">
                                    <i class="fa fa-shopping-bag" aria-hidden="true"></i> <span data-key="t-dashboard">Orders</span>
                                </a>
                            </li>
                            <?php
                            }
                            if (hasPermission($ac_username, $ac_user, "view_product", $conn)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/products">
                                    <i class="fa fa-cube" aria-hidden="true"></i> <span data-key="t-dashboard">Products</span>
                                </a>
                            </li>
                            <?php
                            }
                            if (hasPermission($ac_username, $ac_user, "view_customer", $conn)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/customers">
                                    <i class="fa fa-users" aria-hidden="true"></i> <span data-key="t-dashboard">Customers</span>
                                </a>
                            </li>
                            <?php
                            }
                            if (hasPermission($ac_username, $ac_user, "view_courier", $conn)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/courier">
                                    <i class="fa fa-truck" aria-hidden="true"></i> <span data-key="t-dashboard">Courier</span>
                                </a>
                            </li>
                            <?php
                            }
                            if (hasPermission($ac_username, $ac_user, "view_warehouse", $conn)) {
                            ?>
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link menu-link" href="/warehouse">-->
                            <!--        <i class="fa fa-warehouse" aria-hidden="true"></i> <span data-key="t-dashboard">Warehouse</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <?php
                            }
                            if (hasPermission($ac_username, $ac_user, "view_user", $conn)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/user">
                                    <i class="fa fa-lock" aria-hidden="true"></i> <span data-key="t-dashboard">Access</span>
                                </a>
                            </li>
                            <?php } ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="/STEPUP_INV_14_11_2024/logout">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i> <span data-key="t-logout">Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>

                <div class="sidebar-background"></div>
            </div>
            <div class="vertical-overlay"></div>
            <!-- Left Sidebar End -->
            <div class="main-content">
<?php
    } else {
        echo '<script>window.location="logout";</script>';
    }
} else {
    echo '<script>window.location="logout";</script>';
}
?>