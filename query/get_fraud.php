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
                        $apiLey = "bBGVhI8K9tEtX02kN6U6Sr3Tu1a9HOXKemZYQOEPsIR1o3QMdk6ZVoy5ymvW";
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://bdcourier.com/api/courier-check?phone='.$customerNumber,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ' . $apiLey
                            ),
                        ));
                        $response = curl_exec($curl);
                        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
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
                                if ($http_status == 200) {
                                    $resData = json_decode($response, true);
                                    if ($resData['status'] == "success") {
                                        $result = $resData['courierData'];
                                        ?>
                                            <tr>
                                                <td>Pathao</td>
                                                <td><?php echo $result['pathao']['total_parcel']; ?></td>
                                                <td class="table-primary text-dark"class="table-primary text-dark"><?php echo $result['pathao']['success_parcel']; ?></td>
                                                <td class="table-danger text-dark"class="table-danger text-dark"><?php echo $result['pathao']['cancelled_parcel']; ?></td>
                                                <td><?php echo $result['pathao']['success_ratio']; ?> %</td>
                                            </tr>
                                            <tr>
                                                <td>Steadfast</td>
                                                <td><?php echo $result['steadfast']['total_parcel']; ?></td>
                                                <td class="table-primary text-dark"class="table-primary text-dark"><?php echo $result['steadfast']['success_parcel']; ?></td>
                                                <td class="table-danger text-dark"class="table-danger text-dark"><?php echo $result['steadfast']['cancelled_parcel']; ?></td>
                                                <td><?php echo $result['steadfast']['success_ratio']; ?> %</td>
                                            </tr>
                                            <tr>
                                                <td>Redx</td>
                                                <td><?php echo $result['redx']['total_parcel']; ?></td>
                                                <td class="table-primary text-dark"class="table-primary text-dark"><?php echo $result['redx']['success_parcel']; ?></td>
                                                <td class="table-danger text-dark"class="table-danger text-dark"><?php echo $result['redx']['cancelled_parcel']; ?></td>
                                                <td><?php echo $result['redx']['success_ratio']; ?> %</td>
                                            </tr>
                                            <tr>
                                                <td>Paperfly</td>
                                                <td><?php echo $result['paperfly']['total_parcel']; ?></td>
                                                <td class="table-primary text-dark"class="table-primary text-dark"><?php echo $result['paperfly']['success_parcel']; ?></td>
                                                <td class="table-danger text-dark"class="table-danger text-dark"><?php echo $result['paperfly']['cancelled_parcel']; ?></td>
                                                <td><?php echo $result['paperfly']['success_ratio']; ?> %</td>
                                            </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <td>Total</td>
                                    <td><?php echo $result['summary']['total_parcel']; ?></td>
                                    <td class="table-primary text-dark"class="table-primary text-dark"><?php echo $result['summary']['success_parcel']; ?></td>
                                    <td class="table-danger text-dark"class="table-danger text-dark"><?php echo $result['summary']['cancelled_parcel']; ?></td>
                                    <td><?php echo $result['summary']['success_ratio']; ?> %</td>
                                </tr>
                            </table>
                        </div>
                        <div class="progress progress-xl animated-progress custom-progress progress-label h-auto my-3">
                            <div class="progress-bar  bg-primary   p-2" role="progressbar" style="width: <?php echo $result['summary']['success_ratio']; ?>%" aria-valuenow="<?php echo $result['summary']['success_ratio']; ?>" aria-valuemin="0" aria-valuemax="100"><div class="label  bg-primary "><?php echo $result['summary']['success_ratio']; ?>%</div></div>
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