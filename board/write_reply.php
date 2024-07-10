<?php
session_start();
require_once('./board_func.php');
date_default_timezone_set('Asia/Seoul'); // 서울 시간대 설정

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $id = $_POST['id'];
    // 현재 시간을 'Y-m-d H:i:s' 포맷으로 가져옵니다.
    $current_time = date('Y-m-d H:i:s');

    if (write_reply($id, $_SESSION['user_id'], $_SESSION['user_name'], $content, $current_time)) {
        echo '<script>alert("댓글이 등록되었습니다.");</script>';
        echo "<script>location.href='board_detail.php?id={$id}';</script>";
    } else {
        echo '<script>alert("댓글 등록에 실패하였습니다.");</script>';
        echo "<script>location.href='board_detail.php?id={$id}';</script>";
    }
}
?>
