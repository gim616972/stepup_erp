<?php
session_start();
include "../concat/db_con.php";
include "permission_functions.php";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
date_default_timezone_set("Asia/Dhaka");
$date = date('M d, Y');
$dateTime = date('M d, Y  g:i A');

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
        
        // order data management
        if (isset($_POST['add_order']) && !empty($_POST['cm_phone']) && !empty($_POST['cm_name']) && !empty($_POST['cm_address']) && !empty($_POST['cm_source']) && !empty($_POST['products'])) {
            if (hasPermission($ac_username, $ac_user, "add_order", $conn)) {
                
                $cm_id      = "C-".rand(111111,999999);
                $cm_phone   = test_input($_POST['cm_phone']);
                $cm_name    = test_input($_POST['cm_name']);
                $cm_address = test_input($_POST['cm_address']);
                $cm_source  = test_input($_POST['cm_source']);
                $country    = "BD";
                $lastName   = "";
                $cm_email   = "";
                $discount   = 0;
                $shipping   = isset($_POST['dl_charge']) ? test_input($_POST['dl_charge']) : 0;
                $products   = $_POST['products'];
                
                // check if user already exist
                $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE phone = :phone && member_id = :member_id");
                $stmt->execute([":phone"=>$cm_phone, ":member_id"=>$ac_user]);
                $get_user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($get_user) {
                    $stmt = $conn->prepare("UPDATE tbl_customers SET address_1 = :address_1, country = :country, last_order = :last_order WHERE phone = :phone && member_id = :member_id");
                    $update_user = $stmt->execute([":address_1"=>$cm_address, ":country"=>$country, ":last_order"=>$date, ":phone"=>$cm_phone, ":member_id"=>$ac_user]);
                    
                    // create order
                    function generateInvoice() {
                        $length = 7;
                        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $charactersLength = strlen($characters);
                        $randomString = '';
                    
                        for ($i = 0; $i < $length; $i++) {
                            $randomString .= $characters[random_int(0, $charactersLength - 1)];
                        }
                        return $randomString;
                    }
                    $invoice_id = "INV-".generateInvoice();
                    
                    // get product details
                    $get_total = 0;
                    foreach ($products as $product) {
                        $get_sku = $product['sku'];
                        $get_qty = $product['qty'];
                        
                        $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :sku && member_id = :member_id");
                        $stmt->execute([":sku"=>$get_sku, ":member_id"=>$ac_user]);
                        $items = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($items) {
                            $price     = $items['price'];
                            $subtotal  = $items['price']*$get_qty;
                            $get_total += $subtotal;
                            $total     = $subtotal;
                            
                            // insert into tbl_order_items
                            $stmt = $conn->prepare("INSERT INTO tbl_order_items (inv_id, sku, quantity, price, subtotal, total) VALUES (:inv_id, :sku, :quantity, :price, :subtotal, :total)");
                            $stmt->execute([":inv_id"=>$invoice_id, ":sku"=>$get_sku, ":quantity"=>$get_qty, ":price"=>$price, ":subtotal"=>$subtotal, ":total"=>$total]);
                        }
                    }
                    
                    // insert into tbl_order
                    $total = $get_total+$shipping;
                    $stmt = $conn->prepare("INSERT INTO tbl_orders (member_id, phone, invoice_id, date_created, date_entry, discount_total, shipping_total, total, order_source) VALUES (:member_id, :phone, :invoice_id, :date_created, :date_entry, :discount_total, :shipping_total, :total, :order_source)");
                    $ons_order = $stmt->execute([":member_id"=>$ac_user, ":phone"=>$cm_phone, ":invoice_id"=>$invoice_id, "date_created"=>$dateTime, "date_entry"=>$dateTime, "discount_total"=>$discount, "shipping_total"=>$shipping, "total"=>$total, "order_source"=>$cm_source]);
                    if ($ons_order) {
                        echo "Order Created Successfully !";
                    } else {
                        echo "Failed To Create Order !";
                    }
                } else {
                    $stmt = $conn->prepare("INSERT INTO tbl_customers (member_id, cm_id, phone, first_name, last_name, address_1, country, email, created_at, last_order, source) VALUES (:member_id, :cm_id, :phone, :first_name, :last_name, :address_1, :country, :email, :created_at, :last_order, :source)");
                    $add_user = $stmt->execute([":member_id"=>$ac_user, ":cm_id"=>$cm_id, ":phone"=>$cm_phone, ":first_name"=>$cm_name, ":last_name"=>$lastName, ":address_1"=>$cm_address, ":country"=>$country, ":email"=>$cm_email, ":created_at"=>$date, ":last_order"=>$date, ":source"=>$cm_source]);
                    if ($add_user) {
                        // create order
                        function generateInvoice() {
                            $length = 7;
                            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $charactersLength = strlen($characters);
                            $randomString = '';
                        
                            for ($i = 0; $i < $length; $i++) {
                                $randomString .= $characters[random_int(0, $charactersLength - 1)];
                            }
                            return $randomString;
                        }
                        $invoice_id = "INV-".generateInvoice();
                        
                        // get product details
                        $get_total = 0;
                        foreach ($products as $product) {
                            $get_sku = $product['sku'];
                            $get_qty = $product['qty'];
                            
                            $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :sku && member_id = :member_id");
                            $stmt->execute([":sku"=>$get_sku, ":member_id"=>$ac_user]);
                            $items = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($items) {
                                $price     = $items['price'];
                                $subtotal  = $items['price']*$get_qty;
                                $get_total += $subtotal;
                                $total     = $subtotal;
                                
                                // insert into tbl_order_items
                                $stmt1 = $conn->prepare("INSERT INTO tbl_order_items (inv_id, sku, quantity, price, subtotal, total) VALUES (:inv_id, :sku, :quantity, :price, :subtotal, :total)");
                                $stmt1->execute([":inv_id"=>$invoice_id, ":sku"=>$get_sku, ":quantity"=>$get_qty, ":price"=>$price, ":subtotal"=>$subtotal, ":total"=>$total]);
                            }
                        }
                        
                        // insert into tbl_order
                        $total = $get_total+$shipping;
                        $stmt = $conn->prepare("INSERT INTO tbl_orders (member_id, phone, invoice_id, date_created, date_entry, discount_total, shipping_total, total, order_source) VALUES (:member_id, :phone, :invoice_id, :date_created, :date_entry, :discount_total, :shipping_total, :total, :order_source)");
                        $ons_order = $stmt->execute([":member_id"=>$ac_user, ":phone"=>$cm_phone, ":invoice_id"=>$invoice_id, "date_created"=>$dateTime, "date_entry"=>$dateTime, "discount_total"=>$discount, "shipping_total"=>$shipping, "total"=>$total, "order_source"=>$cm_source]);
                        if ($ons_order) {
                            echo "Order Created Successfully !";
                        } else {
                            echo "Failed To Create Order !";
                        }
                    }
                }
            }
        } else {
            echo "All fields are required !";
        }
    } else {
        echo "Please Login !";
    }
} else {
    echo "Please Login !";
}
?>