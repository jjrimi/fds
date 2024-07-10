<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['user_id'])) {
    echo '<script type="text/javascript">
            alert("세션 정보가 없습니다.");
            window.location.href = "../index.php";
          </script>';
    exit();
}

$current_user_id = $_SESSION['user_id'];
$family_member_name = $_POST['family_member_name'];
$relationship = $_POST['relationship'];
$contact_info = $_POST['contact_info'];

// Find family_user_id based on contact_info
$sql_get_family_user_id = "SELECT user_id FROM Users WHERE phone_number = ?";
$stmt_get_family_user_id = mysqli_prepare($conn, $sql_get_family_user_id);
mysqli_stmt_bind_param($stmt_get_family_user_id, "s", $contact_info);
mysqli_stmt_execute($stmt_get_family_user_id);
$result_get_family_user_id = mysqli_stmt_get_result($stmt_get_family_user_id);

if ($row_get_family_user_id = mysqli_fetch_assoc($result_get_family_user_id)) {
    $family_user_id = $row_get_family_user_id['user_id'];

    // Insert family member information into Family table
    $sql_insert_family_info = "INSERT INTO Family (user_id, family_user_id, family_member_name, relationship, contact_info) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_family_info = mysqli_prepare($conn, $sql_insert_family_info);
    mysqli_stmt_bind_param($stmt_insert_family_info, "iisss", $current_user_id, $family_user_id, $family_member_name, $relationship, $contact_info);
    mysqli_stmt_execute($stmt_insert_family_info);
    mysqli_stmt_close($stmt_insert_family_info);

    echo '<script type="text/javascript">
            alert("가족 정보가 저장되었습니다.");
            window.location.href = "save_family_info.php";
          </script>';
} else {
    echo '<script type="text/javascript">
            alert("해당 연락처로 사용자를 찾을 수 없습니다.");
            window.location.href = "save_family_info.php";
          </script>';
}

mysqli_stmt_close($stmt_get_family_user_id);
mysqli_close($conn);
?>