<?php
session_start(); // 세션 시작

// 모든 세션 변수를 제거
session_unset();

// 세션을 파괴하여 로그아웃
session_destroy();

// 로그아웃 알림 메시지와 함께 index.php로 리디렉션
echo "<script>alert('로그아웃 되었습니다.'); location.href='../index.php';</script>";
?>

