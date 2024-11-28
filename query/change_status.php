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
        
        if (isset($_POST['invoiceID']) && !empty($_POST['invoiceID']) && !empty($_POST['up_status'])) {
            if (hasPermission($ac_username, $ac_user, "edit_order_status", $conn)) {
                
                $invoiceID = $_POST['invoiceID'];
                $up_status = test_input($_POST['up_status']);
                if ($up_status == 1) {
                    // order on hold
                    if (isset($_POST['onHoldValue']) && !empty($_POST['onHoldValue'])) {
                        $onHoldValue = test_input($_POST['onHoldValue']);
                        if ($onHoldValue == "Other") {
                            if (isset($_POST['hold_other_reason']) && !empty($_POST['hold_other_reason'])) {
                                $hold_other = test_input($_POST['hold_other_reason']);
                                foreach ($invoiceID as $invoice) {
                                    $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, additional = :additional, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                                    $status = $stmt->execute([":status"=>$up_status, ":additional"=>$hold_other, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                                }
                                echo "success";
                            } else {
                                echo "All fields are required !";
                            }
                        } else {
                            foreach ($invoiceID as $invoice) {
                                $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, additional = :additional, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                                $status = $stmt->execute([":status"=>$up_status, ":additional"=>$onHoldValue, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                            }
                            echo "success";
                        }
                    } else {
                        echo "All fields are required !";
                    }
                } else if ($up_status == 5) {
                    // order in transit
                    if (isset($_POST['select_courier']) && !empty($_POST['select_courier'])) {
                        $courier_id = test_input($_POST['select_courier']);
                        // get courier data
                        $stmt = $conn->prepare("SELECT * FROM tbl_courier WHERE status = :status && c_id = :c_id && member_id = :member_id");
                        $stmt->execute([":status" => 1, ":c_id"=>$courier_id, ":member_id"=>$ac_user]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result) {
                            // Steadfast
                            $courier = $result['courier_name'];
                            if ($courier == "Steadfast") {
                                $url       = "https://portal.packzy.com/api/v1/create_order";
                                $apiKey    = $result['api_key'];
                                $secretKey = $result['secret_key'];
                                $headers = [
                                    "Api-Key: $apiKey",
                                    "Secret-Key: $secretKey",
                                    "Content-Type: application/json"
                                ];
                                // get order data
                                foreach ($invoiceID as $invoice) {
                                    $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE invoice_id = :invoice_id && member_id = :member_id");
                                    $stmt->execute([":invoice_id" => $invoice, ":member_id"=>$ac_user]);
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($results)>0) {
                                        foreach ($results as $row) {
                                            // get customer
                                            $cm_phone = $row['phone'];
                                            $stmt = $conn->prepare("SELECT * FROM tbl_customers WHERE phone = :phone && member_id = :member_id");
                                            $stmt->execute([":phone" => $cm_phone, ":member_id"=>$ac_user]);
                                            $result_cm = $stmt->fetch(PDO::FETCH_ASSOC);
                                            if ($result_cm) {
                                                // product data
                                                $data = [
                                                    "invoice" => $row['invoice_id'],
                                                    "recipient_name" => $result_cm['first_name'].$result_cm['last_name'],
                                                    "recipient_phone" => $row['phone'],
                                                    "recipient_address" => $result_cm['address_1'],
                                                    "cod_amount" => $row['total'],
                                                    "note" => $row['customer_note']
                                                ];
                                                // curl start
                                                $ch = curl_init($url);
                                                curl_setopt($ch, CURLOPT_POST, true);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                                                $result = curl_exec($ch);
                                                $error  = curl_error($ch);
                                                curl_close($ch);
                                                if ($error) {
                                                    echo "Failed to create order !";
                                                } else {
                                                    $response = json_decode($result, true);
                                                    if ($response['status'] == "200") {
                                                        $dl_consignment   = $response['consignment']['consignment_id'];
                                                        $dl_tracking_code = $response['consignment']['tracking_code'];
                                                        // update the order table
                                                        $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, dl_partner = :dl_partner, dl_consignment = :dl_consignment, dl_tracking_code = :dl_tracking_code, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                                                        $status = $stmt->execute([":status"=>$up_status, ":dl_partner"=>$courier, ":dl_consignment"=>$dl_consignment, ":dl_tracking_code"=>$dl_tracking_code, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                                                        echo "success";
                                                    } else {
                                                        echo "Failed to create order !";
                                                    }
                                                }
                                            } else {
                                                echo "Customer not valid !";
                                            }
                                        }
                                    } else {
                                        echo "No order data found !";
                                    }
                                }
                            } else {
                                echo "Please secect a valid courier !";
                            }
                        } else {
                            echo "Please secect a valid courier !";
                        }
                    } else {
                        echo "All fields are required !";
                    }
                } else if ($up_status == 8) {
                    // order cancelled
                    if (isset($_POST['cancelledValue']) && !empty($_POST['cancelledValue'])) {
                        $cancelledValue = test_input($_POST['cancelledValue']);
                        if ($cancelledValue == "Other") {
                            if (isset($_POST['other_c_reason']) && !empty($_POST['other_c_reason'])) {
                                $cancel_other = test_input($_POST['other_c_reason']);
                                foreach ($invoiceID as $invoice) {
                                    $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, additional = :additional, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                                    $status = $stmt->execute([":status"=>$up_status, ":additional"=>$cancel_other, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                                }
                                echo "success";
                            } else {
                                echo "All fields are required !";
                            }
                        } else {
                            foreach ($invoiceID as $invoice) {
                                $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, additional = :additional, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                                $stmt->execute([":status"=>$up_status, ":additional"=>$cancelledValue, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                            }
                            echo "success";
                        }
                    } else {
                        echo "All fields are required !";
                    }
                } else {
                    foreach ($invoiceID as $invoice) {
                        $stmt = $conn->prepare("UPDATE tbl_orders SET status = :status, date_action = :date_action WHERE invoice_id = :invoice_id && member_id = :member_id");
                        $status = $stmt->execute([":status"=>$up_status, ":date_action"=>$dateTime, ":invoice_id"=>$invoice, ":member_id"=>$ac_user]);
                    }
                    echo "success";
                }
            } else {
                echo "You Are Not Allowed !";
            }
        } else {
            echo "Please select status !";
        }
    } else {
        echo "Please Login !";
    }
} else {
    echo "Please Login !";
}
?>