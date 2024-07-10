<?php
include "../db_conn.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'];
$status = $data['status'];

$stmt = $conn->prepare("UPDATE Notifications SET notification_status = ? WHERE notification_id = ?");
$stmt->bind_param("si", $status, $notification_id);
$stmt->execute();

$response = [
    'success' => $stmt->affected_rows > 0
];

$stmt->close();

echo json_encode($response);
?>