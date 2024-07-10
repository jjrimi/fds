<?php
include "../db_conn.php";
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT n.notification_id, n.detection_id, n.family_id, f.family_user_id, n.notification_status
        FROM Notifications n
        JOIN Family f ON n.family_id = f.family_id
        WHERE f.family_user_id = ? AND n.notification_status = 'Failed'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
   
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();
   
    echo json_encode($notifications);
} else {
    echo json_encode([]);
}
?>