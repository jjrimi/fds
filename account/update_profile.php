<?php
session_start();
include('../db_conn.php');

// 유저 ID를 세션에서 가져오기
$userId = $_SESSION['user_id'];

// 폼 데이터 받기
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phoneNumber = $_POST['phoneNumber'];
$gender = $_POST['gender'];
$dateOfBirth = $_POST['dateOfBirth'];

// // 아바타 이미지 업로드 처리
$avatarData = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
    $avatarTmpPath = $_FILES['avatar']['tmp_name'];
    $avatarType = mime_content_type($avatarTmpPath);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (in_array($avatarType, $allowedTypes)) {
        $avatarData = file_get_contents($avatarTmpPath);
    } else {
        echo "<script>
            alert('Allowed file types are JPG, PNG, and GIF.');
            window.location.href = 'mypage.php';
        </script>";
        exit;
    }
}

// SQL 업데이트 쿼리
$query = "UPDATE Users SET full_name = ?, email = ?, phone_number = ?, gender = ?, date_of_birth = ?";
$params = [$fullname, $email, $phoneNumber, $gender, $dateOfBirth];

if ($avatarData !== null) {
    $query .= ", avatar = ?";
    $params[] = $avatarData;
}

$query .= " WHERE user_id = ?";
$params[] = $userId;

$stmt = $conn->prepare($query);

// 바인드 파라미터 설정
$types = str_repeat('s', count($params) - 1) . 'i';
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "<script>
        alert('프로필 업데이트 성공.');
        window.location.href = 'logout.php';
    </script>";
} else {
    echo "<script>
        alert('Error updating profile: " . $stmt->error . "');
        window.location.href = 'mypage.php';
    </script>";
}

$stmt->close();
$conn->close();
?>
