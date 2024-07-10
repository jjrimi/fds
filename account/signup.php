<?php
include "../db_conn.php";
date_default_timezone_set('Asia/Seoul'); // 시간대를 한국 시간으로 설정

$uploadFileDir = realpath(__DIR__ . '/../account/uploads/') . '/';

if (!is_dir($uploadFileDir)) {
    mkdir($uploadFileDir, 0755, true);
}

if (isset($_POST['checkId'])) {
    // ID 중복 확인 요청을 처리
    $username = $_POST['username'];

    $sql = $conn->prepare("SELECT * FROM Users WHERE user_name = ?");
    $sql->bind_param("s", $username);

    $sql->execute();
    $result = $sql->get_result();

    // 결과가 있다면 중복된 ID가 있음을 의미합니다.
    if ($result->num_rows > 0) {
        echo json_encode(array('duplicate' => true));
    } else {
        echo json_encode(array('duplicate' => false));
    }

    $sql->close();
    $conn->close();

    // ID 중복 확인 요청 처리 후 스크립트 종료
    exit;
}

$gender = $_POST['gender'];
if ($gender != 'Male' && $gender != 'Female') {
    exit;
}

if (isset($_POST['checkSignup'])) {
    // 이메일과 전화번호 중복 확인 요청을 처리
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // 이메일 중복 확인
    $emailSql = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $emailSql->bind_param("s", $email);
    $emailSql->execute();
    $emailResult = $emailSql->get_result();
    $emailDuplicate = $emailResult->num_rows > 0;

    // 전화번호 중복 확인
    $phoneSql = $conn->prepare("SELECT * FROM Users WHERE phone_number = ?");
    $phoneSql->bind_param("s", $phone);
    $phoneSql->execute();
    $phoneResult = $phoneSql->get_result();
    $phoneDuplicate = $phoneResult->num_rows > 0;

    echo json_encode(array('emailDuplicate' => $emailDuplicate, 'phoneDuplicate' => $phoneDuplicate));

    $emailSql->close();
    $phoneSql->close();
    $conn->close();

    // 이메일 및 전화번호 중복 확인 요청 처리 후 스크립트 종료
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$gender = $_POST['gender'];
$phone_number = $_POST['phonenumber'];
$date_of_birth = $_POST['date_of_birth'];
$avatar = 'default-avatar.png'; //아바타 기본값

if ($password !== $confirmPassword) {
    echo "<script>
            alert('비밀번호가 일치하지 않습니다.');
            window.location.href = 'signup.html';
          </script>";
    exit;
}

// 프로필 아바타 이미지 업로드
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
    $avatarTmpPath = $_FILES['avatar']['tmp_name'];
    $avatarFileName = $_FILES['avatar']['name'];
    $dest_path = $uploadFileDir . $avatarFileName;

    if (move_uploaded_file($avatarTmpPath, $dest_path)) {
        $avatar = $avatarFileName; // 업로드 성공 시 아바타 파일명 업데이트
    } else {
        echo "<script>
                alert('아바타 업로드 중 오류가 발생했습니다.');
                window.location.href = 'signup.html';
              </script>";
        exit;
    }
}

// 현재 시간을 'Y-m-d H:i:s' 포맷으로 가져옵니다.
$current_time = date('Y-m-d H:i:s');

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // Prepared statement를 사용한 안전한 회원가입 쿼리문 실행
    $sql = $conn->prepare("INSERT INTO Users (user_name, user_password, email, full_name, gender, phone_number, date_of_birth, registration_date, last_login, status, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)");
    $sql->bind_param("ssssssssss", $username, $password, $email, $full_name, $gender, $phone_number, $date_of_birth, $current_time, $current_time, $avatar);

    if (!$sql->execute()) {
        throw new Exception("Error: " . $sql->error);
    }

    // 삽입된 사용자 ID 가져오기
    $user_id = $conn->insert_id;

    // 모든 쿼리가 성공하면 트랜잭션 커밋
    $conn->commit();

    echo "<script>
            alert('회원 가입 성공!');
            location.href='../index.php';
          </script>";

} catch (Exception $e) {
    // 오류 발생 시 트랜잭션 롤백
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

if (isset($sql)) {
    $sql->close();
}
$conn->close();
?>