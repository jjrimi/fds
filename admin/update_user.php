<?php
include "../db_conn.php";
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !$_SESSION['user_id'] || !$_SESSION['user_name']) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요한 서비스 입니다.']);
    exit();
}

$user_role = $_SESSION['role'];
if ($user_role !== 'admin') {
    echo json_encode(['success' => false, 'message' => '접근 권한이 부족합니다.']);
    exit();
}

// POST 데이터의 존재 여부 확인
if (!isset($_POST['currentPassword']) || !isset($_POST['newRole']) || !isset($_POST['newStatus']) || !isset($_POST['userId'])) {
    echo json_encode(['success' => false, 'message' => 'POST 데이터 수신 오류']);
    exit();
}

$currentPassword = $_POST['currentPassword'];
$newRole = $_POST['newRole'];
$newStatus = $_POST['newStatus'];
$userId = $_POST['userId'];

// 현재 로그인된 사용자의 정보를 가져옵니다.
$loggedInUserId = $_SESSION['user_id'];
$loggedInUserName = $_SESSION['user_name'];

// 사용자의 입력 비밀번호를 DB에 저장된 비밀번호와 확인
$sql = "SELECT user_password FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $loggedInUserId);
$stmt->execute();
$stmt->bind_result($userPassword);
$stmt->fetch();
$stmt->close();

if ($currentPassword !== $userPassword) {
    echo json_encode(['success' => false, 'message' => '비밀번호가 잘못되었습니다.']);
    exit();
}

// 사용자 정보 업데이트
$sql = "UPDATE Users SET role = ?, status = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $newRole, $newStatus, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '유저 정보 업데이트 실패']);
}

$stmt->close();
$conn->close();
?>
