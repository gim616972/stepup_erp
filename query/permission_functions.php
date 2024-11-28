<?php
function hasPermission($username = null, $member_id = null, $permissionName = null, $conn = null) {
    // if all the fields are not fill
    if (is_null($username) || is_null($member_id) || is_null($permissionName) || is_null($conn)) {
        return false;
    }
    // select permission from db
    $stmt = $conn->prepare("SELECT * FROM tbl_user_permission WHERE member_id = ? && username = ? && permission = ?");
    $stmt->execute([$username, $member_id, $permissionName]);
    $users_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($users_data) {
        return true;
    } else {
        return false;
    }
}
?>
