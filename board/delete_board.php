<?php
session_start();
require_once('../db_conn.php');
require_once('./board_func.php');

// 디렉토리와 그 내부 파일들을 삭제하는 함수
function delete_directory($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") 
                   delete_directory($dir . "/" . $object); 
                else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['author'])) {
    $id = $_POST['id'];
    $author = $_POST['author'];

    $board_detail = get_board_detail_by_id($id);
    if ($board_detail['user_id'] != $_SESSION['user_id']) {
            echo '<script>
                alert("글을 삭제 할 수 있는 권한이 없습니다.");
                location.href = "board.php";
                </script>';
            exit;
    }

    if ($author == $_SESSION['user_name']) {
        // 먼저 게시글과 연결된 파일 정보를 삭제합니다.
        $sql = "DELETE FROM files WHERE board_id=?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Error: " . mysqli_error($conn);
                exit;
            }
        }

        // 게시글과 연결된 댓글 정보를 삭제합니다.
        $sql = "DELETE FROM reply WHERE board_id=?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Error: " . mysqli_error($conn);
                exit;
            }
        }

        // 게시글과 연동된 서버의 디렉토리를 삭제합니다.
        $directory_path = './uploaded_files/' . $id . '/';
        delete_directory($directory_path);

        // 그 후 게시글을 삭제합니다.
        $sql = "DELETE FROM board WHERE id=?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $id);

            if (mysqli_stmt_execute($stmt)) {
                echo '<script>
                    alert("게시글이 성공적으로 삭제되었습니다.");
                    location.href = "board.php";
                    </script>';
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    } else {
        echo '<script>
            alert("글을 삭제할 권한이 없습니다.");
            location.href = "board.php";
            </script>';
    }
}
?>
