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
?>
<link href="css/order_page.css" rel="stylesheet" type="text/css">
    <!--create order modal-->
    <?php
    if (hasPermission($ac_username, $ac_user, "add_order", $conn)) {
    ?>
    <div class="modal fade modal-xl" id="create_order_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
        <div class="modal-dialog pt-md-5">
            <div class="modal-content rounded-0">
                <div class="cog_load h-100 w-100" style="display: none; position: absolute;background: rgb(0, 0, 0, 0.1);z-index: 1;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="fa fa-cog fa-spin" style="font-size: 50px;"></i>
                    </div>
                </div>
                <div class="modal-body p-x-4 px-xl-5">
                    <div class="d-flex justify-content-between my-3">
                        <h4 class="text-primary">Create Order</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="">
                        <form method="POST">
                            <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                            <!--customer data-->
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="cm_phone" class="form-label">Customer Phone No</label>
                                    <input type="number" id="cm_phone" class="form-control" placeholder="01700000000">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="cm_name" class="form-label">Customer Name</label>
                                    <input type="text" id="cm_name" class="form-control" placeholder="John Doe">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="cm_address" class="form-label">Customer Address</label>
                                    <input type="text" id="cm_address" class="form-control" placeholder="Mirpur, Dhaka">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="cm_source" class="form-label">Order Source</label>
                                    <select class="form-control" id="cm_source">
                                        <option value="Other" selected="">Other</option>
                                        <option value="Website">Website</option>
                                        <option value="Offline">Offline</option>
                                        <option value="Messenger">Messenger</option>
                                        <option value="Whatsapp">Whatsapp</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="TikTok">TikTok</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Search product -->
                            <div class="mt-4 mb-3 position-relative">
                                <label for="search" class="form-label text-primary">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="search" placeholder="প্রডাক্ট সার্চ করুন">
                                <div id="pd_result" class="position-absolute bg-success text-white w-100 rounded" style="z-index:1;"></div>
                            </div>
                            <!-- Product table -->
                            <div class="table-responsive shadow rounded border">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr id="heading_tr">
                                            <th class="t_head">Name</th>
                                            <th class="t_head">Qty</th>
                                            <th class="t_head">Price</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pd_table"></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">Sub Total</td>
                                            <td class="text-center" id="total_price">0</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Delivery Charge</td>
                                            <td class="text-center">
                                                <input type="number" class="form-control text-center" id="dl_charge" value="0" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Total</td>
                                            <td class="text-center" id="grand_total">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- Product table -->
                            
                            <div class="d-flex justify-content-end pt-4">
                                <button type="button" id="add_order" class="btn btn-primary text-white rounded-0">Add Order</button>
                            </div>
                        </form>
                    </div>
                    <div class="loading_area text-center" style="display:none;">
                        <i class="fa fa-cog fa-spin"></i>
                        <p class="no_data">Please Wait ...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <!--create order modal-->
    <!--modal-->
    <div class="modal fade modal-lg" id="modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-body">
                    <div class="d-flex justify-content-between px-2">
                        <p class="inv_area">Invoice No <span class="in_no text-dinfo"></span></p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="prod_data"></div>
                    <div class="loading_area text-center">
                        <i class="fa fa-cog fa-spin"></i>
                        <p class="no_data">Please Wait ...</p>
                    </div>
                    <div class="empty_area text-center mt-3" style="display: none;">
                        <i class="fa fa-cube cube_data"></i>
                        <p class="no_data">No Data Found</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->
    <!--action modal-->
        <div class="modal fade" id="action_modal" aria-hidden="true" aria-labelledby="actionmodalLabel" tabindex="-1">
            <div class="modal-dialog mt-5">
                <div class="modal-content rounded-0">
                    <div class="modal-body">
                        <div class="d-flex justify-content-between px-2">
                            <p class="inv_area">Change Status</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="alert alert-warning mt-2 mx-2 err_cinfirm" style="display:none;" role="alert"></div>
                            <div class="px-2">
                                <div id="sl_txt"></div>
                            <?php
                            if (isset($_GET['status']) && !empty($_GET['status'])) {
                                $current_status = $_GET['status'];
                                if ($current_status == "all_order") {
                                ?>
                                
                                <?php
                                } else if ($current_status == "requested") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="1">On Hold</option>
                                        <option class="rounded-0" value="2">Approved</option>
                                        <option class="rounded-0" value="8">Cancelled</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "on_hold") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="2">Approved</option>
                                        <option class="rounded-0" value="8">Cancelled</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "approved") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="1">On Hold</option>
                                        <option class="rounded-0" value="3">Processing</option>
                                        <option class="rounded-0" value="4">Shipped</option>
                                        <option class="rounded-0" value="5">In Transit</option>
                                        <option class="rounded-0" value="6">Delivered</option>
                                        <option class="rounded-0" value="8">Cancelled</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "processing") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="1">On Hold</option>
                                        <option class="rounded-0" value="2">Approved</option>
                                        <option class="rounded-0" value="4">Shipped</option>
                                        <option class="rounded-0" value="5">In Transit</option>
                                        <option class="rounded-0" value="6">Delivered</option>
                                        <option class="rounded-0" value="8">Cancelled</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "shipped") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="1">On Hold</option>
                                        <option class="rounded-0" value="3">Processing</option>
                                        <option class="rounded-0" value="5">In Transit</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "in_transit") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Status</option>
                                        <option class="rounded-0" value="2">Approved</option>
                                        <option class="rounded-0" value="3">Processing</option>
                                        <option class="rounded-0" value="4">Shipped</option>
                                        <option class="rounded-0" value="6">Delivered</option>
                                        <option class="rounded-0" value="7">Flagged</option>
                                        <option class="rounded-0" value="8">Cancelled</option>
                                    </select>
                                    <div class="extand_area" style="display:none;"></div>
                                <?php
                                } else if ($current_status == "delivered") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="7">Flagged</option>
                                    </select>
                                <?php
                                } else if ($current_status == "flagged") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="2">Approved</option>
                                    </select>
                                <?php
                                } else if ($current_status == "cancelled") {
                                ?>
                                    <select id="selected_stat" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="2">Approved</option>
                                    </select>
                                <?php
                                }
                            }
                            ?>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-danger rounded-0 text-white" data-bs-dismiss="modal">Cancel</button>
                            <button id="confirm_status" class="btn btn-primary rounded-0 text-white">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--action modal-->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <!--variant-->
                <div class="col-12 offset-0">
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap align-content-stretch gap-2 py-2">
                                <h4 style="align-self: center; margin: 0px;">Orders</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="btn-group">
                                        <?php
                                        if (hasPermission($ac_username, $ac_user, "edit_order_status", $conn)) {
                                        ?>
                                        <button class="btn fw-bolder rounded-0 action_btn" style="color: #328ea2;" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu dropdown-menu-end rounded-0">
                                            <?php
                                            if (isset($_GET['status']) && !empty($_GET['status'])) {
                                                $current_status = $_GET['status'];
                                                if ($current_status == "all_order") {
                                                ?>
                                                    <li><button class="dropdown-item disabled"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "requested") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "on_hold") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "approved") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "processing") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "shipped") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "in_transit") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "delivered") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "flagged") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                } else if ($current_status == "cancelled") {
                                                ?>
                                                    <li><button class="dropdown-item" id="cng_status"><i class="fa fa-arrows-h pe-2"></i>Change Status</a></button>
                                                    <li><button class="dropdown-item"><i class="fa fa-print pe-2"></i>Print invoice</a></button>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?php } ?>
                                    </div>
                                    <?php
                                    if (hasPermission($ac_username, $ac_user, "add_order", $conn)) {
                                    ?>
                                        <button class="btn btn-primary fw-bolder text-white rounded-0" data-bs-target="#create_order_data" data-bs-toggle="modal">Create Order</button>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                            if (hasPermission($ac_username, $ac_user, "view_order", $conn)) {
                            ?>
                                <div class="d-flex flex-wrap align-content-stretch gap-2 mt-2">
                                    <?php
                                    $all_order  = 0;
                                    $t_all_order  = 0;
                                    $pending    = 0;
                                    $t_pending    = 0;
                                    $on_hold    = 0;
                                    $t_on_hold    = 0;
                                    $approved   = 0;
                                    $t_approved   = 0;
                                    $processing = 0;
                                    $t_processing = 0;
                                    $shipped    = 0;
                                    $t_shipped    = 0;
                                    $in_transit = 0;
                                    $t_in_transit = 0;
                                    $delivered  = 0;
                                    $t_delivered  = 0;
                                    $flagged    = 0;
                                    $t_flagged    = 0;
                                    $cancelled  = 0;
                                    $t_cancelled  = 0;
                                    $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE member_id = :member_id");
                                    $stmt->execute([":member_id"=>$ac_user]);
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($result)>0) {
                                        foreach ($result as $data) {
                                            $all_order++;
                                            $t_all_order += $data['total'];
                                            if ($data['status'] == 0) {
                                                $pending++;
                                                $t_pending += $data['total'];
                                            } else if ($data['status'] == 1) {
                                                $on_hold++;
                                                $t_on_hold += $data['total'];
                                            } else if ($data['status'] == 2) {
                                                $approved++;
                                                $t_approved += $data['total'];
                                            } else if ($data['status'] == 3) {
                                                $processing++;
                                                $t_processing += $data['total'];
                                            } else if ($data['status'] == 4) {
                                                $shipped++;
                                                $t_shipped += $data['total'];
                                            } else if ($data['status'] == 5) {
                                                $in_transit++;
                                                $t_in_transit += $data['total'];
                                            } else if ($data['status'] == 6) {
                                                $delivered++;
                                                $t_delivered += $data['total'];
                                            } else if ($data['status'] == 7) {
                                                $flagged++;
                                                $t_flagged += $data['total'];
                                            } else if ($data['status'] == 8) {
                                                $cancelled++;
                                                $t_cancelled += $data['total'];
                                            }
                                        }
                                    }
                                    ?>
                                    <a href="?status=all_order" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="all_order"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_all_order, 2); ?>">
                                        All Orders <span><?php echo $all_order; ?></span>
                                    </a>
                                    <a href="?status=requested" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="requested"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_pending, 2); ?>">
                                        Pending <span><?php echo $pending; ?></span>
                                    </a>
                                    <a href="?status=on_hold" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="on_hold"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_on_hold, 2); ?>">
                                        On Hold <span><?php echo $on_hold; ?></span>
                                    </a>
                                    <a href="?status=approved" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="approved"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_approved, 2); ?>">
                                        Approved <span><?php echo $approved; ?></span>
                                    </a>
                                    <a href="?status=processing" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="processing"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_processing, 2); ?>">
                                        Processing <span><?php echo $processing; ?></span>
                                    </a>
                                    <a href="?status=shipped" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="shipped"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_shipped, 2); ?>">
                                        Shipped <span><?php echo $shipped; ?></span>
                                    </a>
                                    <a href="?status=in_transit" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="in_transit"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_in_transit, 2); ?>">
                                        In-Transit <span><?php echo $in_transit; ?></span>
                                    </a>
                                    <a href="?status=delivered" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="delivered"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_delivered, 2); ?>">
                                        Delivered <span><?php echo $delivered; ?></span>
                                    </a>
                                    <a href="?status=flagged" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="flagged"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_flagged, 2); ?>">
                                        Flagged <span><?php echo $flagged; ?></span>
                                    </a>
                                    <a href="?status=cancelled" class="<?php if(isset($_GET['status'])&&!empty($_GET['status'])){if($_GET['status']=="cancelled"){echo"ac_order_count";}else{echo"order_count";}}?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BDT <?php echo number_format($t_cancelled, 2); ?>">
                                        Cancelled <span><?php echo $cancelled; ?></span>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                <?php
                if (hasPermission($ac_username, $ac_user, "view_order", $conn)) {
                    if (isset($_GET['status']) && !empty($_GET['status'])) {
                        $current_status = $_GET['status'];
                        if ($current_status == "all_order") {
                        ?>
                            <div id="all_orders"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#all_orders").load( "orders_data/all_orders" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "requested") {
                        ?>
                            <div id="requested"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#requested").load( "orders_data/pending" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "on_hold") {
                        ?>
                            <div id="on_hold"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#on_hold").load( "orders_data/on_hold" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "approved") {
                        ?>
                            <div id="approved"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#approved").load( "orders_data/approved" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "processing") {
                        ?>
                            <div id="processing"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#processing").load( "orders_data/processing" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "shipped") {
                        ?>
                            <div id="shipped"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#shipped").load( "orders_data/shipped" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "in_transit") {
                        ?>
                            <div id="in_transit"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#in_transit").load( "orders_data/in_transit" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "delivered") {
                        ?>
                            <div id="delivered"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#delivered").load( "orders_data/delivered" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "flagged") {
                        ?>
                            <div id="flagged"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#flagged").load( "orders_data/flagged" );
                                });
                            </script>
                        <?php
                        } else if ($current_status == "cancelled") {
                        ?>
                            <div id="cancelled"></div>
                            <script>
                                $(document).ready(function(){
                                    $("#cancelled").load( "orders_data/cancelled" );
                                });
                            </script>
                        <?php
                        } else {
                            echo '<script>window.location="/orders?status=requested";</script>';
                        }
                    } else {
                        echo '<script>window.location="/orders?status=requested";</script>';
                    }
                }
                ?>
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
        
        // view order items
        $(document).on('click', '#order_data', function(){
            var order_id = $(this).attr('order_id');
            $(".in_no").text(order_id);
            $(".prod_data").html('');
            $(".loading_area").show();
            $("#modal_data").modal('show');
            $.ajax({
                method: "POST",
                url: "query/get_product",
                data: {"order_id":order_id},
                success: function(data){
                    if (data == "failed") {
                        $(".loading_area").hide();
                        $(".empty_area").show();
                    } else {
                        $(".loading_area").hide();
                        $(".prod_data").html(data);
                    }
                }
            });
        });
        
        // fraud check
        $(document).on('click', '#fraud_check', function(){
            var order_id = $(this).attr('order_id');
            $(".in_no").text(order_id);
            $(".prod_data").html('');
            $(".loading_area").show();
            $("#modal_data").modal('show');
            $.ajax({
                method: "POST",
                url: "query/get_fraud",
                data: {"order_id":order_id},
                success: function(data){
                    if (data == "failed") {
                        $(".loading_area").hide();
                        $(".empty_area").show();
                    } else {
                        $(".loading_area").hide();
                        $(".prod_data").html(data);
                    }
                }
            });
        });
        
        // print invoice
        $(document).on('click', '#print_inv', function(){
            var order_id = $(this).attr('order_id');
            $(".in_no").text(order_id);
            $(".prod_data").html('');
            $(".loading_area").show();
            $("#modal_data").modal('show');
            $.ajax({
                method: "POST",
                url: "query/print_invoice",
                data: {"order_id":order_id},
                success: function(data){
                    if (data == "failed") {
                        $(".loading_area").hide();
                        $(".empty_area").show();
                    } else {
                        $(".loading_area").hide();
                        $(".prod_data").html(data);
                        $(document).load('prod_data',function(){
                            window.print();
                        });
                    }
                }
            });
        });
        
        // if clicked print button
        $(document).on('click','#printPage',function(){
            window.print();
        });
        
        // copy invoice id
        $(document).on('click', '#copy_inv', function(){
            var order_id = $(this).attr('order_id');
            navigator.clipboard.writeText(order_id);
        });
        
        // copy phone number
        $(document).on('click', '.copy_btn', function(){
            var cp_phone = $(this).attr('cp_phone');
            navigator.clipboard.writeText(cp_phone);
        });
        
        // check and uncheck all
        $(document).on('change', '#checkAll', function() {
            $('input[id="checkInvoice"]').prop('checked', this.checked);
        });
        
        // If any "checkInvoice" checkbox is unchecked, uncheck "checkAll"
        $('input[id="checkInvoice"]').on('change', function() {
            if (!$('input[id="checkInvoice"]').not(':checked').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }
        });
        
        // if action button clicked
        $('#cng_status').on('click', function() {
            // Gather all checked invoices
            let selectedInvoices = [];
            $('input[id="checkInvoice"]:checked').each(function() {
                selectedInvoices.push($(this).val());
            });
            
            if (selectedInvoices.length !== 0) {
                $('#selected_stat').show();
                $('#sl_txt').html('');
                $('#action_modal').modal('show');
            } else {
                $('#selected_stat').hide();
                $('#sl_txt').html('<p class="text-center mb-4">No invoice selected !</p>');
                $('#action_modal').modal('show');
            }
        });
        
        // select calcel
        $('#selected_stat').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue == "1") {
                var extand_data = `<select id="hold_reason" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select On Hold Reason</option>
                                        <option class="rounded-0" value="Customer Unreachable by Phone">Customer Unreachable by Phone</option>
                                        <option class="rounded-0" value="Call Not Received">Call Not Received</option>
                                        <option class="rounded-0" value="Call Scheduled">Call Scheduled</option>
                                        <option class="rounded-0" value="Invalid Phone Number">Invalid Phone Number</option>
                                        <option class="rounded-0" value="Customer Decision Pending">Customer Decision Pending</option>
                                        <option class="rounded-0" value="Pre-Order">Pre-Order</option>
                                        <option class="rounded-0" value="Other">Other</option>
                                    </select>
                                    <div class="hold_ex" style="display:none;"><div>`;
                $('.extand_area').html(extand_data);
                $('.extand_area').show();
                // if other is selected
                $('#hold_reason').change(function() {
                    var onHoldValue = $(this).val();
                    if (onHoldValue == "Other") {
                        var other_c_reason = `<input type="text" id="hold_other_reason" class="form-control mt-3 mb-4 rounded-0" placeholder="Describe The Reason">`;
                        $('.hold_ex').html(other_c_reason);
                        $('.hold_ex').show();
                    } else {
                        $('.hold_ex').html('');
                        $('.hold_ex').hide();
                    }
                });
            }  else if (selectedValue == "5") {
                $.ajax({
                    url: 'query/get_courier',
                    method: 'POST',
                    data: {"get_courier": "get_courier"},
                    success: function(data) {
                        $('.extand_area').html(data);
                        $('.extand_area').show();
                    }
                });
            } else if (selectedValue == "8") {
                var extand_data = `<select id="cancel_reason" class="form-select mt-3 mb-4 rounded-0" aria-label="Large select example">
                                        <option class="rounded-0" value="" selected>Select Cancel Reason</option>
                                        <option class="rounded-0" value="High price">High price</option>
                                        <option class="rounded-0" value="Short time delivery">Short time delivery</option>
                                        <option class="rounded-0" value="Out of zone">Out of zone</option>
                                        <option class="rounded-0" value="Duplicate Order">Duplicate Order</option>
                                        <option class="rounded-0" value="Fake Order">Fake Order</option>
                                        <option class="rounded-0" value="Changed Mind">Changed Mind</option>
                                        <option class="rounded-0" value="Other">Other</option>
                                    </select>
                                    <div class="cancel_ex" style="display:none;"><div>`;
                $('.extand_area').html(extand_data);
                $('.extand_area').show();
                // if other is selected
                $('#cancel_reason').change(function() {
                    var cancelledValue = $(this).val();
                    if (cancelledValue == "Other") {
                        var other_c_reason = `<input type="text" id="other_c_reason" class="form-control mt-3 mb-4 rounded-0" placeholder="Describe The Reason">`;
                        $('.cancel_ex').html(other_c_reason);
                        $('.cancel_ex').show();
                    } else {
                        $('.cancel_ex').html('');
                        $('.cancel_ex').hide();
                    }
                });
            } else {
                $('.extand_area').html('');
                $('.extand_area').hide();
            }
        });
        
        // chage status
        $('#confirm_status').on('click', function() {
            // Gather all checked invoices
            let selectedInvoices = [];
            $('input[id="checkInvoice"]:checked').each(function() {
                selectedInvoices.push($(this).val());
            });
            
            // get selected value
            var selected_stat = $("#selected_stat option:selected").val();
            if (selectedInvoices.length !== 0) {
                if (selected_stat != '') {
                    if (selected_stat == "1") {
                        // order on hold
                        var onHoldValue = $("#hold_reason").val();
                        if (onHoldValue == "Other") {
                            var hold_other_reason = $("#hold_other_reason").val();
                            $.ajax({
                                url: 'query/change_status',
                                method: 'POST',
                                data: {"invoiceID": selectedInvoices, "up_status":selected_stat, "onHoldValue":onHoldValue, "hold_other_reason":hold_other_reason},
                                success: function(data) {
                                    if (data == "success") {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                    } else {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: 'query/change_status',
                                method: 'POST',
                                data: {"invoiceID": selectedInvoices, "up_status":selected_stat, "onHoldValue":onHoldValue},
                                success: function(data) {
                                    if (data == "success") {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                    } else {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                    }
                                }
                            });
                        }
                    } else if (selected_stat == "5") {
                        // order in transit
                        var select_courier = $("#select_courier").val();
                        $.ajax({
                            url: 'query/change_status',
                            method: 'POST',
                            data: {"invoiceID": selectedInvoices, "up_status":selected_stat, "select_courier":select_courier},
                            success: function(data) {
                                if (data == "success") {
                                    $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                } else {
                                    $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                }
                            }
                        });
                    } else if (selected_stat == "8") {
                        // order cancelled
                        var cancelledValue = $("#cancel_reason").val();
                        if (cancelledValue == "Other") {
                            var other_c_reason = $("#other_c_reason").val();
                            $.ajax({
                                url: 'query/change_status',
                                method: 'POST',
                                data: {"invoiceID": selectedInvoices, "up_status":selected_stat, "cancelledValue":cancelledValue, "other_c_reason":other_c_reason},
                                success: function(data) {
                                    if (data == "success") {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                    } else {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: 'query/change_status',
                                method: 'POST',
                                data: {"invoiceID": selectedInvoices, "up_status":selected_stat, "cancelledValue":cancelledValue},
                                success: function(data) {
                                    if (data == "success") {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                    } else {
                                        $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                    }
                                }
                            });
                        }
                    } else {
                        $.ajax({
                            url: 'query/change_status',
                            method: 'POST',
                            data: {"invoiceID": selectedInvoices, "up_status":selected_stat},
                            success: function(data) {
                                if (data == "success") {
                                    $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Status updated successfully !");
                                } else {
                                    $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html(data);
                                }
                            }
                        });
                    }
                } else {
                    $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("Please select status !");
                }
            } else {
                $(".err_cinfirm").fadeIn().delay(2000).fadeOut(2000).html("No invoice selected !");
            }
        });
        
        // search product
        $("#search").on('keyup', function(){
            var sr_value = $(this).val();
            if (sr_value.length > 0) {
                $.ajax({
                    url: "query/find_product",
                    method: "POST",
                    data: { sr_value: sr_value },
                    success: function(data){
                        $("#pd_result").show().html(data);
                    }
                });
            } else {
                $("#pd_result").hide();
            }
        });
        
        // focus
        $("#search").focus(function(){
            if ($(this).val().length > 0) {
                $("#pd_result").show();
            }
        });
        // unfocus
        $(document).click(function(e) {
            if (!$(e.target).closest('#search, #pd_result').length) {
                $("#pd_result").hide();
            }
        });
        
        // add product to table
        $(document).on('click', '#pd_result li', function(){
            var p_sku   = $(this).attr('p_sku');
            var p_name  = $(this).attr('p_name');
            var p_price = parseFloat($(this).attr('p_price'));

            var found = false;

            $('#pd_table tr').each(function() {
                var currentSku = $(this).find('td').eq(0).attr('sku');
                if (currentSku === p_sku) {
                    var currentQty = parseFloat($(this).find('input').eq(0).val());

                    $(this).find('input').eq(0).val(currentQty + 1);
                    found = true;
                    return false; // break the loop
                }
            });

            if (!found) {
                $('#pd_table').append('<tr><td sku="'+ p_sku +'">' + p_name + '</td><td><input type="number" class="form-control w-100 prod_qty" value="1"></td><td><span class="prod_price">' + p_price + '</span></td><td class="text-center"><button class="btn btn-danger text-white clear" type="button"><i class="fa fa-times"></i></button></td></tr>');
            }

            $("#pd_result").hide();
            $("#search").val('');
            calculateTotal();
            
            // grand total
            var dlChagre = Number($('#dl_charge').val());
            var calTotal = calculateGndTotal();
            $('#grand_total').text(calTotal+dlChagre);
        });
            
        // calculate total price
        function calculateTotal() {
            var total = 0;
            $('#pd_table tr').each(function() {
                var price = parseFloat($(this).find('.prod_price').text());
                var quantity = parseFloat($(this).find('.prod_qty').val());
                if (!isNaN(price)) {
                    total += price*quantity;
                }
            });
            $('#total_price').text(total);
        }
        
        // calculate total price
        function calculateGndTotal() {
            var total = 0;
            $('#pd_table tr').each(function() {
                var price = parseFloat($(this).find('.prod_price').text());
                var quantity = parseFloat($(this).find('.prod_qty').val());
                if (!isNaN(price)) {
                    total += price*quantity;
                }
            });
            return total;
        }
        
        // change the product qty
        $('#pd_table').on('keyup', '.prod_qty', function() {
            calculateTotal();
            
            // grand total
            var dlChagre = Number($('#dl_charge').val());
            var calTotal = calculateGndTotal();
            $('#grand_total').text(calTotal+dlChagre);
        });
        
        // remove product
        $(document).on('click', '.clear', function(){
            $(this).closest('tr').remove();
            calculateTotal();
            
            // grand total
            var dlChagre = Number($('#dl_charge').val());
            var calTotal = calculateGndTotal();
            $('#grand_total').text(calTotal+dlChagre);
        });
        
        // grand total
        $('#dl_charge').on('keyup', function() {
            var dlChagre = Number($('#dl_charge').val());
            var calTotal = calculateGndTotal();
            $('#grand_total').text(calTotal+dlChagre);
        });
        
        // add order
         $('#add_order').click(function(){
            var cm_phone   = $('#cm_phone').val();
            var cm_name    = $('#cm_name').val();
            var cm_address = $('#cm_address').val();
            var cm_source  = $('#cm_source').val();
            var dl_charge  = $('#dl_charge').val();
            var products   = [];

            $('#pd_table tr').each(function() {
                var sku   = $(this).find('td').eq(0).attr('sku');
                var qty   = $(this).find('.prod_qty').val();
                products.push({sku:sku, qty:qty});
            });
            
            $(".cog_load").show();
            
            $.ajax({
                url: "query/add_order",
                method: "POST",
                data: {
                    add_order: 'add_order',
                    cm_phone: cm_phone,
                    cm_name: cm_name,
                    cm_address: cm_address,
                    cm_source: cm_source,
                    dl_charge: dl_charge,
                    products: products
                },
                success: function(data){
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
    });
</script>