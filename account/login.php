<?php
include "../db_conn.php";
date_default_timezone_set('Asia/Seoul'); // 서울 시간대 설정

session_start(); // 세션 시작

$id = $_POST['id'];
$pw = $_POST['pw'];

// 취약한 SQL 쿼리
$sql = "SELECT * FROM Users WHERE user_name='$id' AND user_password='$pw'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

if (!$row) { // 일치하는 아이디 없음
    echo "<script>
            alert(\"일치하는 아이디가 없거나 아이디 또는 비밀번호가 틀렸습니다\");
            location.href='login.html';
          </script>";
} else {
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['user_name'] = $row['user_name'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['role'] = $row['role'];
    // 로그인 성공 시, last_login 업데이트
    $current_time = date('Y-m-d H:i:s');
    $update_sql = "UPDATE Users SET last_login='$current_time' WHERE user_name='$id'";
    mysqli_query($conn, $update_sql);

    header("Location: ../index.php");
    exit;
}

mysqli_close($conn);
?>