<?php
//php 에러 출력 코드
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '게시판 아이디가 잘못되었습니다.';
    exit;
}
$id = (int)$_GET['id'];

$page_title = 'Board Detail';
require_once('./board_func.php');

// 조회수 증가
$update_views_sql = "UPDATE board SET views = views + 1 WHERE id = ?";
$update_views_stmt = mysqli_prepare($conn, $update_views_sql);
mysqli_stmt_bind_param($update_views_stmt, 'i', $id);
mysqli_stmt_execute($update_views_stmt);

// 게시글 정보 불러오기
$row = get_board_detail_by_id($id);

$replies = get_replies($id); // 게시글의 댓글을 불러옵니다.
?>

<!DOCTYPE html>
<html lang="ko" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $page_title ?></title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include('../navmenu.php'); ?>

            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="py-3 mb-4"><span class="text-muted fw-light">게시판 /</span> 상세보기</h4>
                    
                    <div class="card mb-4">
                        <h5 class="card-header"><?= $row["title"] ?></h5>
                        <div class="card-body">
                            <p><strong>작성자:</strong> <?= $row["author"] ?></p>
                            <p><strong>작성일:</strong> <?= $row["date"] ?></p>
                            <p><strong>조회수:</strong> <?= $row["views"] ?></p>

                            <?php
                            $files = get_files_by_board_id($row['id']);
                            if (!empty($files)) { ?>
                                <p><strong>첨부파일:</strong>
                                <?php
                                foreach ($files as $file) {
                                    if (!empty($file["file_name"])) { ?>
                                        <a href="download_board.php?id=<?= $file["board_id"] ?>&file_name=<?= $file["file_name"] ?>"><?= $file["file_name"] ?></a>
                                <?php }
                                }
                                echo '</p>';
                            }
                            ?>

                            <div class="card mb-3" style="width: 100%; height: auto;">
                                <div class="card-body">
                                    <p class="card-text"><?= $row["content"] ?></p>
                                </div>
                            </div>

                            <?php if ($_SESSION['user_name'] == $row["author"]) { ?>
                                <div class="d-flex justify-content-end">
                                    <button onclick="location.href='edit_board.php?id=<?= $row["id"] ?>'" class="btn btn-primary">수정</button>
                                    <form action="delete_board.php" method="post" class="ms-2">
                                        <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                        <input type="hidden" name="author" value="<?= $row["author"] ?>">
                                        <button type="submit" class="btn btn-danger">삭제</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">댓글</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($replies as $reply) { ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <p><strong>작성자:</strong> <?= $reply["writer"] ?></p>
                                        <div id="reply-content-<?= $reply["idx"] ?>">
                                            <p class="card-text"><?= htmlspecialchars($reply['content']) ?></p>
                                            <p class="card-text"><small class="text-muted">작성일: <?= $reply['regdate'] ?></small></p>
                                            <?php if ($_SESSION['user_id'] == $reply["user_id"]) { ?>
                                                <div class="d-flex justify-content-end">
                                                    <button onclick="showEditForm(<?= $reply["idx"] ?>)" class="btn btn-secondary btn-sm">수정</button>
                                                    <form action="delete_reply.php" method="post" class="ms-2">
                                                        <input type="hidden" name="reply_id" value="<?= $reply["idx"] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                                                    </form>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div id="edit-form-<?= $reply["idx"] ?>" style="display: none;">
                                            <form action="edit_reply.php" method="post">
                                                <input type="hidden" name="reply_id" value="<?= $reply["idx"] ?>">
                                                <input type="hidden" name="board_id" value="<?= $id ?>">
                                                <textarea class="form-control" name="content" rows="3"><?= $reply['content'] ?></textarea>
                                                <div class="d-flex justify-content-end mt-2">
                                                    <button type="submit" class="btn btn-secondary btn-sm">저장</button>
                                                    <button type="button" onclick="hideEditForm(<?= $reply["idx"] ?>)" class="btn btn-secondary btn-sm ms-2">취소</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">댓글 등록</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="write_reply.php" class="d-flex align-items-center">
                                <div class="form-group flex-grow-1 mr-2">
                                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                </div>
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-secondary btn-sm">등록</button>
                            </form>
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

    <script>
        function showEditForm(idx) {
            document.getElementById('reply-content-' + idx).style.display = 'none';
            document.getElementById('edit-form-' + idx).style.display = 'block';
        }

        function hideEditForm(idx) {
            document.getElementById('reply-content-' + idx).style.display = 'block';
            document.getElementById('edit-form-' + idx).style.display = 'none';
        }
    </script>
</body>
</html>
