<?php
session_start();
include "../db_conn.php";
date_default_timezone_set('Asia/Seoul');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_name']) || !$_SESSION['user_name']) {
    header("Location: ../index.php");
    exit();
}

$user_role = $_SESSION['role'];
if ($user_role !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$items_per_page = 30;
$current_page_detections = isset($_GET['page_detections']) ? (int)$_GET['page_detections'] : 1;
$current_page_requests = isset($_GET['page_requests']) ? (int)$_GET['page_requests'] : 1;
$offset_detections = ($current_page_detections - 1) * $items_per_page;
$offset_requests = ($current_page_requests - 1) * $items_per_page;

// Fraud_detections 테이블에서 데이터 가져오기
$sql_detections = "SELECT * FROM Fraud_Detections LIMIT $items_per_page OFFSET $offset_detections";
$result_detections = $conn->query($sql_detections);
$fraud_detections = [];
if ($result_detections->num_rows > 0) {
    while ($row = $result_detections->fetch_assoc()) {
        $fraud_detections[] = $row;
    }
}

// Payment_Requests 테이블에서 데이터 가져오기
$sql_requests = "SELECT * FROM Payment_Requests LIMIT $items_per_page OFFSET $offset_requests";
$result_requests = $conn->query($sql_requests);
$payment_requests = [];
if ($result_requests->num_rows > 0) {
    while ($row = $result_requests->fetch_assoc()) {
        $payment_requests[] = $row;
    }
}

echo json_encode([
    'fraudDetections' => $fraud_detections,
    'paymentRequests' => $payment_requests,
]);

$conn->close();
?>