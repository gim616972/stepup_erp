<?php
session_start();
include "../concat/db_con.php";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if (isset($_POST['useremail']) and isset($_POST['userpassword'])) {
    if (!empty($_POST['useremail']) and !empty($_POST['userpassword'])) {
        $useremail    = test_input($_POST['useremail']);
        $userpassword = test_input($_POST['userpassword']);
        
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = :email OR username = :username");
        $stmt->execute([":email"=>$useremail, ":username"=>$useremail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $f_password = $row['password'];
            if (password_verify($userpassword, $f_password)) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['username'];
                $_SESSION['user_pass'] = $row['password'];
                echo "success";
            } else {
                echo "Wrong Password !";
            }
        } else {
            echo "Wrong Email Or Username!";
        }
        
    } else {
        echo "All fields are required !";
    }
} else {
    echo "All fields are required !";
}
?>