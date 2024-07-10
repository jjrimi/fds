<?php
require_once('./board_func.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_id = $_POST['reply_id'];

    if (delete_reply($reply_id)) {
        echo '<script>
                alert("댓글이 삭제되었습니다.");
                </script>';
        echo "<script>window.history.go(-1);</script>";
    } else {
        echo '<script>
                alert("댓글 삭제에 실패하였습니다, 나중에 다시 시도해주세요.");
                </script>';
        echo "<script>window.history.go(-1);</script>";
    }
}
?>
