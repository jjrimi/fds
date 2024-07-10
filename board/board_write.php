<?php 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('board_func.php');
    write_board($_POST['title'], $_POST['content']);
}

$page_title = '문의사항 작성';
require_once('../base_header.php'); 
?>

<main>
    <div class="container-fluid">
        <h1 class="mt-4">게시글 작성</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <form action="board_write.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">작성자: <?php echo $_SESSION['user_name']; ?></label>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">제목</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">내용</label>
                        <textarea class="form-control" id="content" name="content" rows="20"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">파일</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-secondary mt-3">작성</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once('../base_footer.php'); ?>
