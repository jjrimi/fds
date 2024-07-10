<?php
require_once('./board_func.php');

if (!isset($_GET['id']) || !isset($_GET['file_name'])) {
    echo '필요한 정보가 누락되었습니다.';
    exit;
}

$id = (int)$_GET['id'];
$file_name = $_GET['file_name'];

// 파일 경로를 가져오기
$file_path = get_file_path($id, $file_name);

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    echo '파일이 존재하지 않습니다.';
}
?>
