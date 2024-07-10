<?php 
    session_start();

    require_once('../db_conn.php');
    require_once('./board_func.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        $board_detail = get_board_detail_by_id($id);
        if ($board_detail['user_id'] != $_SESSION['user_id']) {
            echo '<script>
                alert("글을 수정할 권한이 없습니다.");
                location.href = "board.php";
                </script>';
            exit;
        }

        $sql = "UPDATE board SET title=?, content=? WHERE id=?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'ssi', $title, $content, $id);

            // 게시글 수정 후
            if (mysqli_stmt_execute($stmt)) {
                // 파일 업로드
                if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                    upload_file($_FILES['file'], $id);
                }

                // 파일 삭제
                if (isset($_POST['deleteFiles'])) {
                    foreach ($_POST['deleteFiles'] as $fileId) {
                        delete_file($fileId);
                    }
                }

                echo '<script>
                    alert("게시글이 성공적으로 수정되었습니다.");
                    location.href = "board.php";
                    </script>';
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            exit;
        }
    }

    $page_title = 'Board Edit';
    require_once('../base_header.php'); 

    $board_detail = get_board_detail_by_id($_GET['id']);
?>

    <main>
        <div class="container-fluid">
            <h1 class="mt-4">게시글 수정</h1>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form action="edit_board.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">작성자: <?php echo $_SESSION['user_name']; ?></label>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">제목</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $board_detail['title']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">내용</label>
                            <textarea class="form-control" id="content" name="content" rows="20"><?php echo $board_detail['content']; ?></textarea>
                        </div>
                        <!-- 기존 파일 삭제 부분 -->
                        <div class="mb-3">
                            <label class="form-label">글에서 삭제할 파일 선택</label>
                            <?php
                            $files = get_files_by_board_id($board_detail['id']);
                            foreach ($files as $file) {
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" id="deleteFile' . $file['file_id'] . '" name="deleteFiles[]" value="' . $file['file_id'] . '">';
                                echo '<label class="form-check-label" for="deleteFile' . $file['file_id'] . '">' . $file['file_name'] . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <!-- 파일 등록 부분 -->
                        <div class="mb-3">
                            <label for="file" class="form-label">파일</label>
                            <input type="file" class="form-control" id="file" name="file">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-secondary mt-3">수정</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once('../base_footer.php'); ?>
