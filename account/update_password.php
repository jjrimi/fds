<?php
session_start();

include "../db_conn.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('비회원입니다!');";
    echo "window.location.href='../index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $old_password = $_POST['oldPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_new_password = $_POST['confirmNewPassword'];

    // 새 비밀번호와 새 비밀번호 확인 일치 여부 확인
    if ($new_password !== $confirm_new_password) {
        echo "<script>alert('새 비밀번호와 새 비밀번호 확인이 일치하지 않습니다.');";
        echo "window.location.href='mypage.php';</script>";
        exit;
    }

    // 현재 비밀번호 확인 부분 (주석 처리된 상태)
    // $sql = "SELECT user_password FROM Users WHERE user_id='$user_id'";
    // $result = mysqli_query($conn, $sql);
    // $row = mysqli_fetch_assoc($result);
    // if (!password_verify($old_password, $row['user_password'])) {
    //     echo "<script>alert('현재 비밀번호가 일치하지 않습니다.');";
    //     echo "window.location.href='mypage.php';</script>";
    //     exit;
    // }

    // 새 비밀번호 해싱 (주석 처리된 상태)
    // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // 비밀번호 업데이트
    $sql = "UPDATE Users SET user_password='$new_password' WHERE user_id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        session_destroy();
        echo "<script>alert('비밀번호가 성공적으로 변경되었습니다. 다시 로그인해주세요.');";
        echo "window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('비밀번호 변경에 실패하였습니다.');";
        echo "window.location.href='mypage.php';</script>";
    }
    exit;
}
?>