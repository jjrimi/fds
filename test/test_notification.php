<?php
session_start();
include "../db_conn.php";

$user_id = $_SESSION['user_id']; // 로그인된 사용자 ID

$stmt = $conn->prepare("SELECT message, created_at FROM test_notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>알림 목록</h2>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<p>" . htmlspecialchars($row['message']) . "</p>";
        echo "<p>" . htmlspecialchars($row['created_at']) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>알림이 없습니다.</p>";
}

$stmt->close();
$conn->close();
?>
