<?php
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $page_title = 'Board';
    require_once('./board_func.php');

    $page = $_GET['page'] ?? 1;
    $postsPerPage = $_GET['postsPerPage'] ?? 5;

    $searchOrder = $_GET['searchOrder'] ?? '';
    $searchKeyword = $_GET['searchKeyword'] ?? '';

    if ($searchOrder && $searchKeyword) {
        $board = search_board($searchOrder, $searchKeyword, $page, $postsPerPage);
    } else {
        $board = get_board($page, $postsPerPage);
    }

    $pagesPerGroup = 10;  // 페이지 그룹당 페이지 수
    $pageGroup = ceil($page / $pagesPerGroup);  // 페이지 그룹 번호
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>게시판</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include("../navmenu.php"); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">게시판 /</span> 목록</h4>

                    <!-- 게시판 컨트롤 -->
                    <div class="card mb-4">
                        <div class="card-body d-flex justify-content-between">
                            <select id="postsPerPage" class="form-select w-auto me-2">
                                <option value="5" <?php echo $postsPerPage == 5 ? 'selected' : ''; ?>>5개</option>
                                <option value="10" <?php echo $postsPerPage == 10 ? 'selected' : ''; ?>>10개</option>
                                <option value="15" <?php echo $postsPerPage == 15 ? 'selected' : ''; ?>>15개</option>
                            </select>
                            <div class="input-group">
                                <select id="searchOrder" class="form-select">
                                    <option value="author" <?php echo $searchOrder == 'author' ? 'selected' : ''; ?>>작성자</option>
                                    <option value="title" <?php echo $searchOrder == 'title' ? 'selected' : ''; ?>>제목</option>
                                    <option value="content" <?php echo $searchOrder == 'content' ? 'selected' : ''; ?>>내용</option>
                                </select>
                                <input type="text" id="searchKeyword" class="form-control" placeholder="검색어" value="<?php echo htmlspecialchars($searchKeyword); ?>" />
                                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="bx bx-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- 게시글 리스트 -->
                    <div class="card">
                        <h5 class="card-header">게시글 리스트</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>작성자</th>
                                        <th>제목</th>
                                        <th>작성일</th>
                                        <th>조회수</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                    foreach ($board as $row) {
                                        echo '<tr class="board-row" data-id="' . $row["id"] . '">';
                                        echo '<td>' . $row["id"] . '</td>';
                                        echo '<td>' . htmlspecialchars($row["author"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($row["title"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($row["date"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($row["views"]) . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="board_write.php" class="btn btn-secondary">글쓰기</a>
                    </div>

                    <!-- 페이지 링크 -->
                    <div class="pagination">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php
                                $totalPages = get_total_pages($postsPerPage);
                                $firstPage = ($pageGroup - 1) * $pagesPerGroup + 1;
                                $lastPage = min($firstPage + $pagesPerGroup - 1, $totalPages);
                                $prevGroup = $pageGroup > 1 ? $firstPage - 1 : 1;
                                $nextGroup = $pageGroup < ceil($totalPages / $pagesPerGroup) ? $lastPage + 1 : $totalPages;
                                ?>
                                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="board.php?page=<?php echo $prevGroup; ?>&postsPerPage=<?php echo $postsPerPage; ?>">이전</a>
                                </li>
                                <?php for ($i = $firstPage; $i <= $lastPage; $i++): ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link" href="board.php?page=<?php echo $i; ?>&postsPerPage=<?php echo $postsPerPage; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                                    <a class="page-link" href="board.php?page=<?php echo $nextGroup; ?>&postsPerPage=<?php echo $postsPerPage; ?>">다음</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <script>document.write(new Date().getFullYear());</script>, made with ❤️ by <a href="https://themeselection.com" target="_blank" class="footer-link fw-bolder">ThemeSelection</a>
                        </div>
                        <div>
                            <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                            <a href="https://themeselection.com/" target="_blank" class="footer-link me-4">More Themes</a>
                            <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/" target="_blank" class="footer-link me-4">Documentation</a>
                            <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank" class="footer-link me-4">Support</a>
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
        document.querySelector('#postsPerPage').addEventListener('change', function() {
            const postsPerPage = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('postsPerPage', postsPerPage);
            window.location.href = url.href;
        });

        document.querySelector('#btnNavbarSearch').addEventListener('click', function() {
            const searchOrder = document.querySelector('#searchOrder').value;
            const searchKeyword = document.querySelector('#searchKeyword').value;
            const url = new URL(window.location.href);
            url.searchParams.set('searchOrder', searchOrder);
            url.searchParams.set('searchKeyword', searchKeyword);
            window.location.href = url.href;
        });

        document.querySelectorAll('.board-row').forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location.href = `board_detail.php?id=${id}`;
            });
        });
    </script>
</body>
</html>
