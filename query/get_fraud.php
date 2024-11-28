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
        
        if (isset($_POST['order_id']) AND !empty($_POST['order_id'])) {
            if (hasPermission($ac_username, $ac_user, "view_product", $conn)) {
                
                $total_delivered  = 0;
                $total_succrssful = 0;
                $total_cancelled  = 0;
                // customerNumber
                
                $order_id  = test_input($_POST['order_id']);
                $stmt = $conn->prepare("SELECT * FROM tbl_orders WHERE invoice_id = :invoice_id && member_id = :member_id");
                $stmt->execute([":invoice_id"=>$order_id, ":member_id"=>$ac_user]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)>0) {
                    foreach ($result as $order) {
                        $customerNumber = $order['phone'];
                        
                        // Pathao API
                        $url = 'https://merchant.pathao.com/api/v1/user/success';
                        $cm_data = array('phone' => $customerNumber);
                        $jsonData = json_encode($cm_data);
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        $headers = array(
                            'Content-Type: application/json',
                            'Accept: application/json, text/plain, */*',
                            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiZTliNDkwYWU2NzNkYzg5ZGU4OWNiYTQ3MDhiZjQyNzFkYTNhZjdiZmUxYjA5ZmEzYTEyYjk2YzA5MWRkYjViM2U5ODkyMzkxODU2MzI3MWYiLCJpYXQiOjE3MjcxNDkzMDIuMTc5NDk2LCJuYmYiOjE3MjcxNDkzMDIuMTc5NDk5LCJleHAiOjE3MzQ5MjUzMDIuMTYyNDE1LCJzdWIiOiIyMTc4NzkiLCJzY29wZXMiOltdfQ.aNl3cqOoa4J7q_eKVpAk2S2hmAO3M6k0R4cdkz38RaPlKoy-5lR_fXwB52tVK2f8iOryg0NB6HPdB5HyChjg3Z7BIY7p7qe1sman--_g_pqMaSd9aYJXHEmKC0VRdO6jd8RQfwhoYZ7qe3LanMc1_aUy1xRM_xxrwDxJKEVmKIqh-pFoeLXFigpagdQOyrW__nx6Iki_lPBJ5850ILml2XXqcl9Rfp7qPJmQ4mWx-6_WS9l_dbar24PA9Y3nKenz75LZoJUN4RMZQrWjbanJQirP3o9EnOGBKOQfoo8tcPR8Ql2apOSOpnpLHoGDMCjEDcfZPhwDSXlqdvb-l_LOuIi-2Sz3qAVj6x9Ha0gCqqRumrbJWoYY-udqy0I5ZQSs9jEydp1mY_ft36reb0JeamB9J0ELi4-6-pLvesQKKCQY7e9JGKtudZ0XNO2X844rFbG7VXdQAB4yqDVxegqepbOL7W_6UkS7XJSmc7xUOsNxwXiC0UvmCQUvlms6OTJLeQnoB7a3ppy5-y7aoAv7ZU-uGZtTwCrinybsz4hxkJ_KunYfrkQldQdjRunyHrsKM7OQDVWGTyqX9Y3YeUWR1NBCpFoL2l7m7R0uWVQNw4UjN6vPYxlgXX2Ua156PnbVC6FiwEZbQ2CBkAM03q2-rN5lCb0wGccasDbBdFkzimM',
                            'Origin: htBearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMTU3NmU0MGM3OTBhNDBmYTRjNTRjYmViMWE1MzI4NTI1OTIwYWJiMzRkY2FlMTZiNDc4MWQzMjVkNzM0NGUxMmVlNzEzNWY2NDIxMWY4NzciLCJpYXQiOjE3MTgyNzA0NDMuNzc2NjM1LCJuYmYiOjE3MTgyNzA0NDMuNzc2NjM5LCJleHAiOjE3MjYwNDY0NDMuNjgwNiwic3ViIjoiMjE3ODc5Iiwic2NvcGVzIjpbXX0.Z_8z7pRzkxWped8JgLZrpZMTfCnZO_3EGUIJpdmPlh0gUTG0CMb59k0AVnYTyQZ0gtfVpWUUUG-cLc1Xd620qjNjo2-1GGprkfDPd4qg7TZGwERxjkyUVbaOQfZZGJnjtcHOcc2j1airPvgJ-FfJGTEPxpybscC5_exnfvubvKYYH-0jtq_t0Znaxr9-VglXZb2639F78LfTX6rmBU830ZsVRAUpe5mqz7M8tW6zzx47jg6kq5_bGXyLvgNR_mRyJT-nPhTMAA1Oim_Kr06FzI9L_dpCufIWJ27NiilPDu8YY97pc_VfpAGPPs5U7T42IvwPzOV129IdpjXoWKP_aQ5HraDVj4Vf1gTLR2HW1LXeon18ARjWvIYfOpMYWRrM4ti3exn_1qJS41Fhu2IoPbtbPqG6CMVEE5GVlt8OqLn0SKHr1WXhl0NosXPtjzh_RmUF5GiFSBhSrkfWCoL6JkvCbsraSFpQI8lg-MAEIDM7_oHUVRcLjeCRPgzzks-UeIDZnJfOeAtSL65xIIsZUoz4NdHghYwTAF_umrtis326IfRtqswJMJHgTGoyBpWU-Qzr1O3x0autUshujpnxc6MsNygVcUZXeA7eyk21mOm32aD9PuxfuyZJwJ15ms9T9XGpbSXz0kARTd6_pwDm4KWcePxMaY8ZnF0q0osdjbktps://merchant.pathao.com',
                            'Referer: https://merchant.pathao.com/courier/dashboard',
                            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                        );
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        $http_status_pathao = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        
                        // Redex API
                        $URL = "https://redx.com.bd/api/redx_se/admin/parcel/customer-success-return-rate?phoneNumber=88".$customerNumber; // View public msg
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $URL);
                        curl_setopt($curl, CURLOPT_HEADER, 0);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                        $result = curl_exec($curl);
                        $http_status_redex = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);
                        ?>
                        <table class="table text-center table-striped table-bordered mb-3">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td><?php echo $customerNumber ?></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="table-responsive">
                            <table class="table text-center table-striped table-bordered mb-0">
                                <tr>
                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th class="table-primary text-dark">Delivered</th>
                                    <th class="table-danger text-dark">Returned</th>
                                    <th>Success Ratio</th>
                                </tr>
                                <?php
                                // pathao
                                if ($http_status_pathao == 200) {
                                    $pathao_res = json_decode($response, true);
                                    if ($pathao_res['type'] == "success") {
                                        if (isset($pathao_res['data']['customer'])) {
                                            $total_delivered+=$pathao_res['data']['customer']['total_delivery'];
                                            $total_succrssful+=$pathao_res['data']['customer']['successful_delivery'];
                                            $total_cancelled+=$pathao_res['data']['customer']['total_delivery']-$pathao_res['data']['customer']['successful_delivery'];
                                        }
                                    ?>
                                    <tr>
                                        <td>Pathao</td>
                                        <td><?php echo $total_delivered; ?></td>
                                        <td class="table-primary text-dark"><?php echo $total_succrssful; ?></td>
                                        <td class="table-danger text-dark"><?php echo $total_cancelled; ?></td>
                                        <td><?php echo $pathao_res['data']['success_rate']; ?> %</td>
                                    </tr>
                                    <?php
                                    }
                                }
                                
                                // redex
                                if ($http_status_redex == 200) {
                                    $redex_res = json_decode($result, true);
                                    if ($redex_res['code'] == 200) {
                                        $total_delivered+=$redex_res['data']['totalParcels'];
                                        $total_succrssful+=$redex_res['data']['deliveredParcels'];
                                        $total_cancelled+=$redex_res['data']['returnPercentage'];
                                        if ($redex_res['data']['totalParcels'] != 0) {
                                            $success_rate = (100/$redex_res['data']['totalParcels'])*$redex_res['data']['deliveredParcels'];
                                        } else {
                                            $success_rate = 0;
                                        }
                                    ?>
                                        <tr>
                                            <td>Redx</td>
                                            <td><?php echo $redex_res['data']['totalParcels']; ?></td>
                                            <td class="table-primary text-dark"><?php echo $redex_res['data']['deliveredParcels']; ?></td>
                                            <td class="table-danger text-dark"><?php echo $redex_res['data']['returnPercentage']; ?></td>
                                            <td><?php echo $success_rate; ?> %</td>
                                        </tr>
                                    <?php
                                    }
                                }
                                
                                // total success rate
                                if ($total_delivered != 0) {
                                    $total_success_rate = round((100/$total_delivered)*$total_succrssful);
                                } else {
                                    $total_success_rate = 0;
                                }
                                ?>
                                <tr>
                                    <td>Total</td>
                                    <td><?php echo $total_delivered; ?></td>
                                    <td class="table-primary text-dark"><?php echo $total_succrssful; ?></td>
                                    <td class="table-danger text-dark"><?php echo $total_cancelled; ?></td>
                                    <td><?php echo $total_success_rate; ?> %</td>
                                </tr>
                            </table>
                        </div>
                        <div class="progress progress-xl animated-progress custom-progress progress-label h-auto my-3">
                            <div class="progress-bar  bg-primary   p-2" role="progressbar" style="width: <?php echo $total_success_rate; ?>%" aria-valuenow="<?php echo $total_success_rate; ?>" aria-valuemin="0" aria-valuemax="100"><div class="label  bg-primary "><?php echo $total_success_rate; ?>%</div></div>
                        </div>
                    <?php
                    }
                } else {
                    echo "failed";
                }
            } else {
                echo "failed";
            }
        } else {
            echo "failed";
        }
    } else {
        echo "failed";
    }
} else {
    echo "failed";
}
?>