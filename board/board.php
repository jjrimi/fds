<?php
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $page_title = 'Board';
    require_once('../base_header.php');
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
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">게시판</h1>
            
            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between">
                    <!-- 페이지당 게시글 수 선택 -->
                    <select id="postsPerPage" class="me-auto">
                        <option value="5">선택</option>
                        <option value="5">5개</option>
                        <option value="10">10개</option>
                        <option value="15">15개</option>
                    </select>
                    <!-- 검색 순서 선택 -->
                    <div>
                        <select id="searchOrder">
                            <option value="author">작성자</option>
                            <option value="title">제목</option>
                            <option value="content">내용</option>
                        </select>
                        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                            <div class="input-group">
                                <input class="form-control" type="text" id="searchKeyword" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                                <button class="btn btn-secondary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- 게시글 리스트 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i>
                    게시글 리스트
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>작성자</th>
                                <th>제목</th>
                                <th>작성일</th>
                                <th>조회수</th>
                            </tr>
                        </thead>
                        <!-- 테이블 바디 -->
                        <tbody>
                            <?php
                            foreach ($board as $row) {
                                echo '<tr class="board-row" data-id="' . $row["id"] . '">';
                                echo '<td>' . $row["id"] . '</td>';
                                echo '<td>' . $row["author"] . '</td>';
                                echo '<td>' . $row["title"] . '</td>';
                                echo '<td>' . $row["date"] . '</td>';
                                echo '<td>' . $row["views"] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        <a href="board_write.php" class="btn btn-secondary mt-3">글쓰기</a>
                    </div>
                </div>
            </div>

            <!-- 페이지 링크 -->
            <div class="paginate d-flex justify-content-center my-3">
                <?php
                $totalPages = get_total_pages($postsPerPage);
                $firstPage = ($pageGroup - 1) * $pagesPerGroup + 1;
                $lastPage = min($firstPage + $pagesPerGroup - 1, $totalPages);
                $prevGroup = $pageGroup > 1 ? $firstPage - 1 : 1;
                $nextGroup = $pageGroup < ceil($totalPages / $pagesPerGroup) ? $lastPage + 1 : $totalPages;
                ?>
                <a href="board.php?page=<?php echo $prevGroup; ?>&postsPerPage=<?php echo $postsPerPage; ?>">이전</a>
                <?php for ($i = $firstPage; $i <= $lastPage; $i++): ?>
                    <a href="board.php?page=<?php echo $i; ?>&postsPerPage=<?php echo $postsPerPage; ?>" class="mx-1">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                <a href="board.php?page=<?php echo $nextGroup; ?>&postsPerPage=<?php echo $postsPerPage; ?>">다음</a>
            </div>
        </div>
    </main>

<script>
    // 게시글 행 클릭 이벤트
    document.querySelectorAll('.board-row').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.dataset.id;
            location.href = 'board_detail.php?id=' + id;
        });
    });

    // 검색 버튼 클릭 이벤트
    document.getElementById('btnNavbarSearch').addEventListener('click', () => {
        const searchOrder = document.getElementById('searchOrder').value;
        const searchKeyword = document.getElementById('searchKeyword').value;
        location.href = 'board.php?searchOrder=' + searchOrder + '&searchKeyword=' + searchKeyword;
    });

    // 페이지당 게시글 수 선택 이벤트
    document.getElementById('postsPerPage').addEventListener('change', () => {
        const postsPerPage = document.getElementById('postsPerPage').value;
        location.href = 'board.php?page=1&postsPerPage=' + postsPerPage;
    });
</script>
<?php require_once('../base_footer.php'); ?>
