<?php
include "../concat/db_con.php";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// date function
date_default_timezone_set("Asia/Dhaka");
$date = date('M d, Y');
$dateTime = date('M d, Y  g:i A');

if (isset($_GET['uid'])) {
    $uu_id = test_input($_GET['uid']);
    
    // fetch uu_id from datebase
    $stmt = $conn->prepare("SELECT * FROM tbl_wp_config WHERE uu_id = :uu_id");
    $stmt->execute([":uu_id"=>$uu_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result)>0) {
        foreach ($result as $row) {
            $member_id = $row['member_id'];
            $secret = $row['consumer_secret'];
            
            // data management
            $input = file_get_contents('php://input');
            $calculated_signature = base64_encode(hash_hmac('sha256', $input, $secret, true));
            $data  = json_decode(json_encode($input), true);
            if ($data !== null || json_last_error() === JSON_ERROR_NONE) {
                if (isset($_SERVER['HTTP_X_WC_WEBHOOK_TOPIC']) && isset($_SERVER['HTTP_X_WC_WEBHOOK_SIGNATURE'])) {
                    $event     = $_SERVER['HTTP_X_WC_WEBHOOK_TOPIC'];
                    $signature = $_SERVER['HTTP_X_WC_WEBHOOK_SIGNATURE'];
                    if ($signature === $calculated_signature) {
                        
                        // create product
                        if ($event == "product.created") {
                            $json_data = json_decode($data, true);
                            $sku = $json_data['sku'];
                            if (!empty($sku)) {
                                // product data
                                $sku        = $json_data['sku'];
                                $images     = $json_data['images'];
                                foreach ($images as $image) {
                                    $images  = $image['src'];
                                }
                                $name         = $json_data['name'];
                                $type         = $json_data['type'];
                                $categories   = $json_data[0]['categories'];
                                $weight        = !empty($json_data['weight']) ? $json_data['weight'] : 'N/A';
                                $description   = !empty($json_data['description']) ? $json_data['description'] : 'N/A';
                                $short_desc    = !empty($json_data['short_description']) ? $json_data['short_description'] : 'N/A';
                                $price         = !empty($json_data['price']) ? $json_data['price'] : 0;
                                $regular_price = !empty($json_data['regular_price']) ? $json_data['regular_price'] : 0;
                                $sale_price    = !empty($json_data['sale_price']) ? $json_data['sale_price'] : 0;
                                
                                // check the product is already exist
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :sku");
                                $stmt->execute([":sku"=>$sku]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                // if already exist
                                if (count($result)>0) {
                                    $stmt = $conn->prepare("UPDATE tbl_product SET sku = :sku, images = :images, name = :name, type = :type, categories = :categories, weight = :weight, description = :description, short_description = :short_description, price = :price, regular_price = :regular_price, sale_price = :sale_price WHERE sku = :sku");
                                    $result = $stmt->execute([":sku"=>$sku, ":images"=>$images, ":name"=>$name, ":type"=>$type, ":categories"=>$categories, ":weight"=>$weight, ":description"=>$description, ":short_description"=>$short_desc, ":price"=>$price, ":regular_price"=>$regular_price, ":sale_price"=>$sale_price]);
                                    if ($result) {
                                        http_response_code(200);
                                    }
                                    
                                // if not exist
                                } else {
                                    $stmt = $conn->prepare("INSERT INTO tbl_product (member_id, sku, images, name, type, categories, weight, description, short_description, price, regular_price, sale_price) VALUES (:member_id, :sku, :images, :name, :type, :categories, :weight, :description, :short_description, :price, :regular_price, :sale_price)");
                                    $result = $stmt->execute([":member_id"=>$member_id, ":sku"=>$sku, ":images"=>$images, ":name"=>$name, ":type"=>$type, ":categories"=>$categories, ":weight"=>$weight, ":description"=>$description, ":short_description"=>$short_desc, ":price"=>$price, ":regular_price"=>$regular_price, ":sale_price"=>$sale_price]);
                                    if ($result) {
                                        http_response_code(200);
                                    }
                                }
                            }
                            
                        // update product 
                        } else if ($event == "product.updated") {
                            $json_data = json_decode($data, true);
                            $sku = $json_data['sku'];
                            if (!empty($sku)) {
                                // product data
                                $sku        = $json_data['sku'];
                                $images     = $json_data['images'];
                                foreach ($images as $image) {
                                    $images  = $image['src'];
                                }
                                $name         = $json_data['name'];
                                $type         = $json_data['type'];
                                $categories   = '';
                                $all_category = $json_data['categories'];
                                foreach ($all_category as $category) {
                                    $categories .= $category['name'].",";
                                }
                                $weight        = !empty($json_data['weight']) ? $json_data['weight'] : 'N/A';
                                $description   = !empty($json_data['description']) ? $json_data['description'] : 'N/A';
                                $short_desc    = !empty($json_data['short_description']) ? $json_data['short_description'] : 'N/A';
                                $price         = !empty($json_data['price']) ? $json_data['price'] : 0;
                                $regular_price = !empty($json_data['regular_price']) ? $json_data['regular_price'] : 0;
                                $sale_price    = !empty($json_data['sale_price']) ? $json_data['sale_price'] : 0;
                                
                                // check the product is already exist
                                $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE sku = :sku");
                                $stmt->execute([":sku"=>$sku]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                // if already exist
                                if (count($result)>0) {
                                    $stmt = $conn->prepare("UPDATE tbl_product SET sku = :sku, images = :images, name = :name, type = :type, categories = :categories, weight = :weight, description = :description, short_description = :short_description, price = :price, regular_price = :regular_price, sale_price = :sale_price WHERE sku = :sku");
                                    $result = $stmt->execute([":sku"=>$sku, ":images"=>$images, ":name"=>$name, ":type"=>$type, ":categories"=>$categories, ":weight"=>$weight, ":description"=>$description, ":short_description"=>$short_desc, ":price"=>$price, ":regular_price"=>$regular_price, ":sale_price"=>$sale_price]);
                                    if ($result) {
                                        http_response_code(200);
                                    }
                                    
                                // if not exist
                                } else {
                                    $stmt = $conn->prepare("INSERT INTO tbl_product (member_id, sku, images, name, type, categories, weight, description, short_description, price, regular_price, sale_price) VALUES (:member_id, :sku, :images, :name, :type, :categories, :weight, :description, :short_description, :price, :regular_price, :sale_price)");
                                    $result = $stmt->execute([":member_id"=>$member_id, ":sku"=>$sku, ":images"=>$images, ":name"=>$name, ":type"=>$type, ":categories"=>$categories, ":weight"=>$weight, ":description"=>$description, ":short_description"=>$short_desc, ":price"=>$price, ":regular_price"=>$regular_price, ":sale_price"=>$sale_price]);
                                    if ($result) {
                                        http_response_code(200);
                                    }
                                }
                            }
                            
                        // order crerate
                        } else if ($event == "order.created") {
                            $json_data = json_decode($data, true);
                            // create user data
                            $cm_id = "C-".rand(111111,999999);
                            $billing    = $json_data['billing'];
                            $phone      = $billing['phone'];
                            $first_name = $billing['first_name'];
                            $last_name  = $billing['last_name'];
                            $address_1  = $billing['address_1'];
                            $city       = $billing['city'];
                            $state      = $billing['state'];
                            $country    = $billing['country'];
                            $email      = $billing['email'];
                            $source     = "Website";
                            
                            // check if customer exist
                            $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE phone = :phone");
                            $stmt->execute([":phone"=>$phone]);
                            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // if customer exist
                            if (count($res)>0) {
                                $stmt = $conn->prepare("UPDATE tbl_customers SET address_1 = :address_1, city = :city, state = :state, country = :country, last_order = :last_order WHERE phone = :phone");
                                $stmt->execute([":address_1"=>$address_1, ":city"=>$city, ":state"=>$state, ":country"=>$country, ":last_order"=>$date, ":phone"=>$phone]);
                                
                            // if customer not exist
                            } else {
                                $stmt = $conn->prepare("INSERT INTO tbl_customers (member_id, cm_id, phone, first_name, last_name, address_1, city, state, country, email, created_at, last_order, source) VALUES (:member_id, :cm_id, :phone, :first_name, :last_name, :address_1, :city, :state, :country, :email, :created_at, :last_order, :source)");
                                $stmt->execute([":member_id"=>$member_id, ":cm_id"=>$cm_id, ":phone"=>$phone, ":first_name"=>$first_name, ":last_name"=>$last_name, ":address_1"=>$address_1, ":city"=>$city, ":state"=>$state, ":country"=>$country, ":email"=>$email, ":created_at"=>$date, ":last_order"=>$date, ":source"=>$source]);
                            }
                            
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
                            $invoice_id     = "INV-".generateInvoice();
                            $date_create    = new DateTime($json_data['date_created']);
                            $date_created   = $date_create->format('M d, Y  g:i A');
                            $discount_total = $json_data['discount_total'];
                            $shipping_total = $json_data['shipping_total'];
                            $total          = $json_data['total'];
                            $payment_method = $json_data['payment_method'];
                            $customer_note  = $json_data['customer_note'];
                            $order_source   = "Website";
                            
                            // insert order data
                            $stmt = $conn->prepare("INSERT INTO tbl_orders (member_id, phone, invoice_id, date_created, date_entry, discount_total, shipping_total, total, payment_method, customer_note, order_source) VALUES (:member_id, :phone, :invoice_id, :date_created, :date_entry, :discount_total, :shipping_total, :total, :payment_method, :customer_note, :order_source)");
                            $result_order = $stmt->execute([":member_id"=>$member_id, ":phone"=>$phone, ":invoice_id"=>$invoice_id, ":date_created"=>$date_created, ":date_entry"=>$dateTime, ":discount_total"=>$discount_total, ":shipping_total"=>$shipping_total, ":total"=>$total, ":payment_method"=>$payment_method, ":customer_note"=>$customer_note, ":order_source"=>$order_source]);
                            
                            if ($result_order) {
                                // create order item
                                $line_items = $json_data['line_items'];
                                foreach ($line_items as $items) {
                                    $sku      = $items['sku'];
                                    $quantity = $items['quantity'];
                                    $price    = $items['price'];
                                    $subtotal = $items['subtotal'];
                                    $total    = $items['total'];
                                    
                                    // insert order item data
                                    $stmt = $conn->prepare("INSERT INTO tbl_order_items (inv_id, sku, quantity, price, subtotal, total) VALUES ('$invoice_id','$sku','$quantity','$price','$subtotal','$total')");
                                    $result_order = $stmt->execute([":inv_id"=>$invoice_id, ":sku"=>$sku, ":quantity"=>$quantity, ":price"=>$price, ":subtotal"=>$subtotal, ":total"=>$total]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>