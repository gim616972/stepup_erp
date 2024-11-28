<?php
session_start();
include "../concat/db_con.php";
include "../query/permission_functions.php";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
        if (hasPermission($ac_username, $ac_user, "add_company_settings", $conn)) {
            if (isset($_POST['wp_new']) and isset($_POST['wp_url']) and isset($_POST['cm_key']) and isset($_POST['cm_secret'])) {
                if (!empty($_POST['wp_url']) and !empty($_POST['cm_key']) and !empty($_POST['cm_secret'])) {
                    $store_url       = test_input($_POST['wp_url']);
                    $consumer_key    = test_input($_POST['cm_key']);
                    $consumer_secret = test_input($_POST['cm_secret']);
                    
                    // create uu_id
                    function generateInvoice() {
                        $length = 8;
                        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $charactersLength = strlen($characters);
                        $randomString = '';
                    
                        for ($i = 0; $i < $length; $i++) {
                            $randomString .= $characters[random_int(0, $charactersLength - 1)];
                        }
                        return $randomString;
                    }
                    $uu_id = md5(generateInvoice());
                    
                    // add webhook into wp
                    $endpoint = $store_url . '/wp-json/wc/v3/webhooks/batch';
                    $webhooks_data = [
                        'create' => [
                            [
                                'name' => 'Inventory Product Update Webhook',
                                'topic' => 'product.updated',
                                'secret' => $consumer_secret,
                                'delivery_url' => 'https://uxprime.xyz/integration/woocommerce?uid='.$uu_id
                            ],
                            [
                                'name' => 'Inventory Product Creation Webhook',
                                'topic' => 'product.created',
                                'secret' => $consumer_secret,
                                'delivery_url' => 'https://uxprime.xyz/integration/woocommerce?uid='.$uu_id
                            ],
                            [
                                'name' => 'Inventory Order Creation Webhook',
                                'topic' => 'order.created',
                                'secret' => $consumer_secret,
                                'delivery_url' => 'https://uxprime.xyz/integration/woocommerce?uid='.$uu_id
                            ]
                        ],
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ':' . $consumer_secret);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhooks_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    $result = curl_exec($ch);
                    $error = curl_error($ch);
                    curl_close($ch);
                    if ($error) {
                        echo "Please enter valid details !";
                    } else {
                        $response = json_decode( $result, true);
                        if (isset($response['create'])) {
                            // insert wp_config data into db
                            $stmt = $conn->prepare("INSERT INTO tbl_wp_config (member_id,site_url,consumer_key,consumer_secret,uu_id) VALUES (:member_id, :site_url, :consumer_key, :consumer_secret, :uu_id)");
                            $row = $stmt->execute([":member_id"=>$ac_user, ":site_url"=>$store_url, ":consumer_key"=>$consumer_key, ":consumer_secret"=>$consumer_secret, ":uu_id"=>$uu_id]);
                            if ($row) {
                                echo "Wordpress successfully integrated";
                            } else {
                                echo "Failed Wordpress integration !";
                            }
                        }
                    }
                } else {
                    echo "All fields are required !";
                }
            } else {
                echo "All fields are required !";
            }
        } else {
            echo "You Are Not Allowed !";
        }
    } else {
        echo "Please Login !";
    }
} else {
    echo "Please Login !";
}
?>