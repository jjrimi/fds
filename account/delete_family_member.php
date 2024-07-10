<?php
session_start();

include "../db_conn.php";

if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('비회원입니다!');";
    echo "window.location.href=\"../index.php\";</script>";
    exit;
}

if (!isset($_GET['family_id'])) {
    echo "<script>alert('잘못된 요청입니다.');";
    echo "window.location.href=\"modify_profile.php\";</script>";
    exit;
}

$login_id = $_SESSION['user_name'];
$family_id = $_GET['family_id'];

// 가족 구성원 정보 삭제
$stmt = $conn->prepare("DELETE FROM Family WHERE user_id=(SELECT user_id FROM Users WHERE user_name=?) AND family_id=?");
$stmt->bind_param("si", $login_id, $family_id);
$result = $stmt->execute();

if ($result) {
    echo "<script>alert('가족 구성원 정보가 성공적으로 삭제되었습니다.');";
} else {
    echo "<script>alert('가족 구성원 정보 삭제에 실패했습니다.');";
}
echo "window.location.href=\"modify_profile.php\";</script>";

$stmt->close();
$conn->close();
?>
