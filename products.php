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

    if (hasPermission($ac_username, $ac_user, "add_product", $conn)) {
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
                        <h4 class="">Add Product</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="alert alert-warning err_modal" style="display:none;" role="alert"></div>
                        <div class="mb-3">
                            <label for="pd_sku" class="form-label">Product SKU</label>
                            <input type="text" id="pd_sku" class="form-control" placeholder="Enter your product SKU">
                        </div>
                        <div class="mb-3">
                            <label for="pd_image" class="form-label">Product Image</label>
                            <input class="form-control" type="file" id="pd_image">
                        </div>
                        <div class="mb-3">
                            <label for="pd_name" class="form-label">Product Name</label>
                            <input type="text" id="pd_name" class="form-control" placeholder="Enter your product name">
                        </div>
                        <div class="mb-3">
                            <label for="pd_type" class="form-label">Product Type</label>
                            <select class="form-select" id="pd_type">
                                <option value="" selected>Select Product Type</option>
                                <option value="simple">Simple</option>
                                <option value="variable">Variable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pd_category" class="form-label">Product Category</label>
                            <select class="form-select" id="pd_category">
                                <option value="" selected>Select Product Category</option>
                                <option value="simple">Simple</option>
                                <option value="variable">Variable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pd_weight" class="form-label">Product Weight</label>
                            <input type="number" id="pd_weight" class="form-control" placeholder="Enter your product weight">
                        </div>
                        <div class="mb-3">
                            <label for="pd_summary" class="form-label">Product Summary</label>
                            <textarea class="form-control" placeholder="Enter Product Summary" id="pd_summary"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="pd_mrp" class="form-label">Product MPR</label>
                            <input type="number" id="pd_mrp" class="form-control" placeholder="Enter your product MPR">
                        </div>
                        <div class="mb-3">
                            <label for="buy_price" class="form-label">Purchase Price</label>
                            <input type="number" id="buy_price" class="form-control" placeholder="Enter your purchase price">
                        </div>
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button" id="add_product" class="btn btn-primary text-white rounded-0">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->
    <?php } ?>
    <!--user promt modal start-->
    <div class="modal fade modal-lg" id="product_promt_modal_data" aria-hidden="true" aria-labelledby="modalLabel" tabindex="-1">
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
                        <div id="product_promt_data"></div>
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
                            <div class="d-flex flex-wrap align-content-stretch gap-2 justify-content-between">
                                <h4 style="align-self: center; margin: 0px;">Products &amp; Pricing</h4>
                                <?php
                                if (hasPermission($ac_username, $ac_user, "view_product", $conn)) {
                                    
                                    // fetch product data
                                    $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE member_id = :member_id");
                                    $stmt->execute([":member_id"=>$ac_user]);
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                        <div class="d-flex flex-wrap align-content-stretch gap-2">
                                            <a href="products" class="prod_count">
                                                <div class="text-secondary d-flex align-items-center justify-content-between">
                                                    <span>Total</span><i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Product"></i>
                                                </div>
                                                <h5 class="pt-1"><?php echo count($result); ?></h5>
                                            </a>
                                            <a href="?error" class="prod_count">
                                                <div class="text-secondary d-flex align-items-center justify-content-between">
                                                    <span>Error</span><i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Please add purchase price"></i>
                                                </div>
                                                <h5 class="pt-1 text-danger">
                                                    <?php
                                                    if (count($result)>0) {
                                                        $err_in_pd = 0;
                                                        foreach ($result as $pddata) {
                                                            if ($pddata['purchase_price'] == 0) {
                                                                $err_in_pd++;
                                                            }
                                                        }
                                                        echo $err_in_pd;
                                                    } else {
                                                        echo 0;
                                                    }
                                                    ?>
                                                </h5>
                                            </a>
                                            <a href="?active" class="prod_count">
                                                <div class="text-secondary d-flex align-items-center justify-content-between">
                                                    <span>Active</span><i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Active Product"></i>
                                                </div>
                                                <h5 class="pt-1 text-success">
                                                    <?php
                                                    if (count($result)>0) {
                                                        $active_in_pd = 0;
                                                        foreach ($result as $pddata) {
                                                            if ($pddata['status'] == 1) {
                                                                $active_in_pd++;
                                                            }
                                                        }
                                                        echo $active_in_pd;
                                                    } else {
                                                        echo 0;
                                                    }
                                                    ?>
                                                </h5>
                                            </a>
                                            <a href="?inactive" class="prod_count">
                                                <div class="text-secondary d-flex align-items-center justify-content-between">
                                                    <span>Inactive</span><i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Total Inactive Product"></i>
                                                </div>
                                                <h5 class="pt-1 text-info">
                                                    <?php
                                                    if (count($result)>0) {
                                                        $in_active_in_pd = 0;
                                                        foreach ($result as $pddata) {
                                                            if ($pddata['status'] == 0) {
                                                                $in_active_in_pd++;
                                                            }
                                                        }
                                                        echo $in_active_in_pd;
                                                    } else {
                                                        echo 0;
                                                    }
                                                    ?>
                                                </h5>
                                            </a>
                                        </div>
                                    <?php
                                }
                                if (hasPermission($ac_username, $ac_user, "add_product", $conn)) {
                                ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-primary text-white rounded-0" data-bs-target="#modal_data" data-bs-toggle="modal">Add Product</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--products-->
                <?php
                if (hasPermission($ac_username, $ac_user, "view_product", $conn)) {
                    if (isset($_GET['error'])) {
                    ?>
                    <div class="col-12 offset-0">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE member_id = :member_id && purchase_price = :purchase_price");
                                $stmt->execute([":member_id"=>$ac_user, ":purchase_price"=>0]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result)>0) {
                                ?>
                                <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                    <table class="table mb-0">
                                        <tr id="heading_tr">
                                            <th class="t_head">SKU</th>
                                            <th class="t_head">Image</th>
                                            <th class="t_head">Name</th>
                                            <th class="t_head">Category</th>
                                            <th class="t_head">Weight</th>
                                            <th class="t_head">MPR</th>
                                            <th class="t_head">Status</th>
                                            <th>Action</th>
                                        </tr>
                                            <?php
                                            foreach ($result as $product) {
                                            ?>
                                                <tr id="body_tr">
                                                    <td>
                                                        <span class="text-dinfo"><?php echo $product['sku']; ?></span>
                                                        <?php
                                                        if ($product['purchase_price'] == 0) {
                                                            echo '<i class="fa fa-info-circle ms-2 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Please add purchase price"></i>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <img class="prod_image" src="<?php echo $product['images']; ?>">
                                                    </td>
                                                    <td>
                                                        <a class="text-primary" href="#" style="position: unset;"><?php echo $product['name']; ?></a>
                                                        <div class="mt-2">
                                                            <span class="simple">
                                                                <?php
                                                                if ($product['type'] == "simple") {
                                                                    echo "Simple Product";
                                                                } else if ($product['type'] == "variable") {
                                                                    echo "Variable Product";
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $product['categories']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['weight'] == "N/A") {
                                                            echo "N/A";
                                                        } else {
                                                            echo "<span class='border p-2'>Weight: ".$product['weight']." KG</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo "BDT ".number_format($product['price'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['status'] == 0) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>">
                                                            </div>
                                                        <?php
                                                        } else if ($product['status'] == 1) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>" checked>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (hasPermission($ac_username, $ac_user, "edit_product", $conn)) {
                                                        ?>
                                                            <button id="editProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Product"><i class="fas fa-edit"></i></button>
                                                        <?php
                                                        }
                                                        if (hasPermission($ac_username, $ac_user, "delete_product", $conn)) {
                                                        ?>
                                                            <button id="deleteProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Product"><i class="fas fa-trash"></i></button>
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
                    <?php
                    } else if (isset($_GET['active'])) {
                    ?>
                    <div class="col-12 offset-0">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE member_id = :member_id && status = :status");
                                $stmt->execute([":member_id"=>$ac_user, ":status"=>1]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result)>0) {
                                ?>
                                <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                    <table class="table mb-0">
                                        <tr id="heading_tr">
                                            <th class="t_head">SKU</th>
                                            <th class="t_head">Image</th>
                                            <th class="t_head">Name</th>
                                            <th class="t_head">Category</th>
                                            <th class="t_head">Weight</th>
                                            <th class="t_head">MPR</th>
                                            <th class="t_head">Status</th>
                                            <th>Action</th>
                                        </tr>
                                            <?php
                                            foreach ($result as $product) {
                                            ?>
                                                <tr id="body_tr">
                                                    <td>
                                                        <span class="text-dinfo"><?php echo $product['sku']; ?></span>
                                                        <?php
                                                        if ($product['purchase_price'] == 0) {
                                                            echo '<i class="fa fa-info-circle ms-2 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Please add purchase price"></i>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <img class="prod_image" src="<?php echo $product['images']; ?>">
                                                    </td>
                                                    <td>
                                                        <a class="text-primary" href="#" style="position: unset;"><?php echo $product['name']; ?></a>
                                                        <div class="mt-2">
                                                            <span class="simple">
                                                                <?php
                                                                if ($product['type'] == "simple") {
                                                                    echo "Simple Product";
                                                                } else if ($product['type'] == "variable") {
                                                                    echo "Variable Product";
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $product['categories']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['weight'] == "N/A") {
                                                            echo "N/A";
                                                        } else {
                                                            echo "<span class='border p-2'>Weight: ".$product['weight']." KG</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo "BDT ".number_format($product['price'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['status'] == 0) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>">
                                                            </div>
                                                        <?php
                                                        } else if ($product['status'] == 1) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>" checked>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (hasPermission($ac_username, $ac_user, "edit_product", $conn)) {
                                                        ?>
                                                            <button id="editProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Product"><i class="fas fa-edit"></i></button>
                                                        <?php
                                                        }
                                                        if (hasPermission($ac_username, $ac_user, "delete_product", $conn)) {
                                                        ?>
                                                            <button id="deleteProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Product"><i class="fas fa-trash"></i></button>
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
                    <?php
                    } else if (isset($_GET['inactive'])) {
                    ?>
                    <div class="col-12 offset-0">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE member_id = :member_id && status = :status");
                                $stmt->execute([":member_id"=>$ac_user, ":status"=>0]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result)>0) {
                                ?>
                                <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                    <table class="table mb-0">
                                        <tr id="heading_tr">
                                            <th class="t_head">SKU</th>
                                            <th class="t_head">Image</th>
                                            <th class="t_head">Name</th>
                                            <th class="t_head">Category</th>
                                            <th class="t_head">Weight</th>
                                            <th class="t_head">MPR</th>
                                            <th class="t_head">Status</th>
                                            <th>Action</th>
                                        </tr>
                                            <?php
                                            foreach ($result as $product) {
                                            ?>
                                                <tr id="body_tr">
                                                    <td>
                                                        <span class="text-dinfo"><?php echo $product['sku']; ?></span>
                                                        <?php
                                                        if ($product['purchase_price'] == 0) {
                                                            echo '<i class="fa fa-info-circle ms-2 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Please add purchase price"></i>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <img class="prod_image" src="<?php echo $product['images']; ?>">
                                                    </td>
                                                    <td>
                                                        <a class="text-primary" href="#" style="position: unset;"><?php echo $product['name']; ?></a>
                                                        <div class="mt-2">
                                                            <span class="simple">
                                                                <?php
                                                                if ($product['type'] == "simple") {
                                                                    echo "Simple Product";
                                                                } else if ($product['type'] == "variable") {
                                                                    echo "Variable Product";
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $product['categories']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['weight'] == "N/A") {
                                                            echo "N/A";
                                                        } else {
                                                            echo "<span class='border p-2'>Weight: ".$product['weight']." KG</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo "BDT ".number_format($product['price'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['status'] == 0) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>">
                                                            </div>
                                                        <?php
                                                        } else if ($product['status'] == 1) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>" checked>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (hasPermission($ac_username, $ac_user, "edit_product", $conn)) {
                                                        ?>
                                                            <button id="editProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Product"><i class="fas fa-edit"></i></button>
                                                        <?php
                                                        }
                                                        if (hasPermission($ac_username, $ac_user, "delete_product", $conn)) {
                                                        ?>
                                                            <button id="deleteProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Product"><i class="fas fa-trash"></i></button>
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
                    <?php
                    } else {
                    ?>
                    <div class="col-12 offset-0">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE member_id = :member_id");
                                $stmt->execute([":member_id"=>$ac_user]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result)>0) {
                                ?>
                                <div class="table-responsive shadow rounded" style="max-height: 60vh;">
                                    <table class="table mb-0">
                                        <tr id="heading_tr">
                                            <th class="t_head">SKU</th>
                                            <th class="t_head">Image</th>
                                            <th class="t_head">Name</th>
                                            <th class="t_head">Category</th>
                                            <th class="t_head">Weight</th>
                                            <th class="t_head">MPR</th>
                                            <th class="t_head">Status</th>
                                            <th>Action</th>
                                        </tr>
                                            <?php
                                            foreach ($result as $product) {
                                            ?>
                                                <tr id="body_tr">
                                                    <td>
                                                        <span class="text-dinfo"><?php echo $product['sku']; ?></span>
                                                        <?php
                                                        if ($product['purchase_price'] == 0) {
                                                            echo '<i class="fa fa-info-circle ms-2 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Please add purchase price"></i>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <img class="prod_image" src="<?php echo $product['images']; ?>">
                                                    </td>
                                                    <td>
                                                        <a class="text-primary" href="#" style="position: unset;"><?php echo $product['name']; ?></a>
                                                        <div class="mt-2">
                                                            <span class="simple">
                                                                <?php
                                                                if ($product['type'] == "simple") {
                                                                    echo "Simple Product";
                                                                } else if ($product['type'] == "variable") {
                                                                    echo "Variable Product";
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $product['categories']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['weight'] == "N/A") {
                                                            echo "N/A";
                                                        } else {
                                                            echo "<span class='border p-2'>Weight: ".$product['weight']." KG</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo "BDT ".number_format($product['price'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        if ($product['status'] == 0) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>">
                                                            </div>
                                                        <?php
                                                        } else if ($product['status'] == 1) {
                                                        ?>
                                                            <div class="form-check form-switch" style="position: unset;">
                                                                <input class="form-check-input" type="checkbox" id="mySwitch" uid="<?php echo $product['sku']; ?>" checked>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (hasPermission($ac_username, $ac_user, "edit_product", $conn)) {
                                                        ?>
                                                            <button id="editProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-info text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Product"><i class="fas fa-edit"></i></button>
                                                        <?php
                                                        }
                                                        if (hasPermission($ac_username, $ac_user, "delete_product", $conn)) {
                                                        ?>
                                                            <button id="deleteProduct" uid="<?php echo $product['sku']; ?>" class="btn btn-danger text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Product"><i class="fas fa-trash"></i></button>
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
                    <?php
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
    $(document).ready(function() {
        $('#mySwitch').on('change', function() {
            var isChecked = $(this).prop('checked');
            var confirmMessage = isChecked ? "Are you sure you want to turn this on?" : "Are you sure you want to turn this off?";
            
            var isConfirmed = confirm(confirmMessage);
            
            if (!isConfirmed) {
                $(this).prop('checked', !isChecked);  // Revert the switch state
            }
        });
        
        // add product
        $("#add_product").click(function(e){
            e.preventDefault();
            var pd_sku      = $("#pd_sku").val();
            var pd_name     = $("#pd_name").val();
            var pd_type     = $("#pd_type").val();
            var pd_category = $("#pd_category").val();
            var pd_weight   = $("#pd_weight").val();
            var pd_summary  = $("#pd_summary").val();
            var pd_mrp      = $("#pd_mrp").val();
            var buy_price   = $("#buy_price").val();
            var pd_image    = $('#pd_image')[0].files[0];
            
            var formData = new FormData();
            formData.append('add_product', 'add_product');
            formData.append('pd_sku', pd_sku);
            formData.append('pd_image', pd_image);
            formData.append('pd_name', pd_name);
            formData.append('pd_type', pd_type);
            formData.append('pd_category', pd_category);
            formData.append('pd_weight', pd_weight);
            formData.append('pd_summary', pd_summary);
            formData.append('pd_mrp', pd_mrp);
            formData.append('buy_price', buy_price);
    
            $(".cog_load").show();
            $.ajax({
                method: 'POST',
                url: 'query/add_product',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".cog_load").hide();
                    $(".err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });

         //==== edit curiar data start ====

        //show curiar data
        $(document).on('click', '#editProduct', function(){
            var uid = $(this).attr('uid');
            console.log(uid);
            
            $(".action_title").html('Edit Product');
            $("#product_promt_data").html("");
            $("#product_promt_modal_data").modal('show');
            $(".promt_cog_load").show();
            $.ajax({
                url: 'query/edit_product',
                method: 'POST',
                data: {"edit_product_promt":"edit_product_promt", "uid":uid},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $("#product_promt_data").html(data);
                }
            });
        });

        //save changes
        $(document).on('click', "#edit_product_Data", function(){
            const edit_courier_id = $("#edit_courier_id").val();
            const edit_images       = $("#edit_images").val();
            const edit_name    = $("#edit_name").val();
            const edit_categories = $("#edit_categories").val();
            const edit_weight = $("#edit_weight").val();
            const edit_price = $("#edit_price").val();
            
            $.ajax({
                url: "query/edit_product",
                method: 'POST',
                data: {"edit_product_changes": "edit_product_changes","edit_courier_id": edit_courier_id, "edit_images": edit_images, "edit_name": edit_name,
                     "edit_categories": edit_categories, "edit_weight": edit_weight, "edit_price": edit_price},
                success: function(data) {
                    $(".promt_cog_load").hide();
                    $(".promt_err_modal").fadeIn().delay(2000).fadeOut(2000).html(data);
                }
            });
        });
        //==== edit curiar data end ====

        //delete customer data
        $(document).on('click', "#deleteProduct", function(){
            const uid = $(this).attr('uid');
            $.ajax({
                url: 'query/edit_product',
                method: 'POST',
                data: {"delete_product":"delete_product", "uid":uid},
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
            console.log(uid);
            
            $.ajax({
                url: 'query/edit_product',
                method: 'POST',
                data: {"change_product_status":"change_product_status", "uid":uid},
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