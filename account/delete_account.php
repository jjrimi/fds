<?php
session_start();

include "../db_conn.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('비회원입니다!');";
    echo "window.location.href='../index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    // Family 테이블에서 해당 user_id를 참조하는 레코드 삭제
    $sql = "DELETE FROM Family WHERE user_id=? OR family_user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Family 테이블에서 레코드 삭제 실패: " . $stmt->error);
    }
    $stmt->close();

    // Cards 테이블에서 해당 user_id를 참조하는 레코드 삭제
    $sql = "DELETE FROM Cards WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Cards 테이블에서 레코드 삭제 실패: " . $stmt->error);
    }
    $stmt->close();

    // Users 테이블에서 사용자 계정 삭제
    $sql = "DELETE FROM Users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Users 테이블에서 레코드 삭제 실패: " . $stmt->error);
    }
    $stmt->close();

    $conn->commit();

    session_destroy();
    echo "<script>alert('계정 삭제가 완료되었습니다.');";
    echo "window.location.href='../index.php';</script>";
} catch (Exception $exception) {
    $conn->rollback();

    // 상세 오류 메시지 출력
    echo "<script>alert('계정 삭제를 실패하였습니다: " . addslashes($exception->getMessage()) . "');";
    echo "window.location.href='mypage.php';</script>";
}

$conn->close();
exit;
?>
