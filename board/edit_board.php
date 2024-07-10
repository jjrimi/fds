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

            if (mysqli_stmt_execute($stmt)) {
                if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                    upload_file($_FILES['file'], $id);
                }

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

    $page_title = '게시글 수정';
    $board_detail = get_board_detail_by_id($_GET['id']);
?>

<!DOCTYPE html>
<html lang="ko" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include('../navmenu.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="py-3 mb-4"><span class="text-muted fw-light">게시판 /</span> 게시글 수정</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <h5 class="card-header">게시글 수정</h5>
                                <div class="card-body">
                                    <form action="edit_board.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">작성자: <?php echo $_SESSION['user_name']; ?></label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="title" class="form-label">제목</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($board_detail['title']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content" class="form-label">내용</label>
                                            <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($board_detail['content']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">글에서 삭제할 파일 선택</label>
                                            <?php
                                            $files = get_files_by_board_id($board_detail['id']);
                                            foreach ($files as $file) {
                                                echo '<div class="form-check">';
                                                echo '<input class="form-check-input" type="checkbox" id="deleteFile' . $file['file_id'] . '" name="deleteFiles[]" value="' . $file['file_id'] . '">';
                                                echo '<label class="form-check-label" for="deleteFile' . $file['file_id'] . '">' . htmlspecialchars($file['file_name']) . '</label>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="mb-3">
                                            <label for="file" class="form-label">파일</label>
                                            <input type="file" class="form-control" id="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">수정</button>
                                            <a href="board.php" class="btn btn-secondary ms-2">취소</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <script>document.write(new Date().getFullYear());</script>, Template with by
                            <a href="https://themeselection.com" target="_blank" class="footer-link fw-medium">ThemeSelection</a>
                        </div>
                    </div>
                </footer>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
