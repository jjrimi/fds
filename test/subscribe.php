<?php
session_start();
include "./db_conn.php";

$subscription = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id']; // 로그인된 사용자 ID
$endpoint = $subscription['endpoint'];
$publicKey = $subscription['keys']['p256dh'];
$authToken = $subscription['keys']['auth'];

$stmt = $conn->prepare("INSERT INTO push_subscriptions (user_id, endpoint, public_key, auth_token) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE endpoint = VALUES(endpoint), public_key = VALUES(public_key), auth_token = VALUES(auth_token)");
$stmt->bind_param("isss", $user_id, $endpoint, $publicKey, $authToken);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
?>
