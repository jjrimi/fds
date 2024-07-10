<?php
require_once('./board_func.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $reply_id = $_POST['reply_id'];
    $id = $_POST['board_id'];

    if (edit_reply($reply_id, $content)) {
        echo '<script>alert("댓글이 수정되었습니다.");</script>';
        echo "<script>location.href='board_detail.php?id={$id}';</script>";
    } else {
        echo '<script>alert("댓글 수정에 실패하였습니다.");</script>';
        echo "<script>location.href='board_detail.php?id={$id}';</script>";
    }
}
?>
