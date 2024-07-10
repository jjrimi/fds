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
    echo "<script>alert('권한이 부족합니다.'); window.location.href = '../index.php';</script>";
    exit();
}

$items_per_page = 30;
$current_page_detections = isset($_GET['page_detections']) ? (int)$_GET['page_detections'] : 1;
$current_page_requests = isset($_GET['page_requests']) ? (int)$_GET['page_requests'] : 1;
$offset_detections = ($current_page_detections - 1) * $items_per_page;
$offset_requests = ($current_page_requests - 1) * $items_per_page;

// Fraud_detections 테이블에서 데이터 가져오기
$sql_detections = "SELECT SQL_CALC_FOUND_ROWS * FROM Fraud_Detections LIMIT $items_per_page OFFSET $offset_detections";
$result_detections = $conn->query($sql_detections);
$total_detections = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$total_pages_detections = ceil($total_detections / $items_per_page);

// Payment_Requests 테이블에서 데이터 가져오기
$sql_requests = "SELECT SQL_CALC_FOUND_ROWS * FROM Payment_Requests LIMIT $items_per_page OFFSET $offset_requests";
$result_requests = $conn->query($sql_requests);
$total_requests = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$total_pages_requests = ceil($total_requests / $items_per_page);

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>관리자 로그</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include("../navmenu.php"); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">관리자 /</span> 로그</h4>

                    <!-- Fraud Detections Table -->
                    <div class="card">
                        <h5 class="card-header">사기 결제 탐지 로그</h5>
                        <div class="table-responsive text-nowrap" id="fraud-detections-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Detection ID</th>
                                        <th>Request ID</th>
                                        <th>Detection Date</th>
                                        <th>Detection Result</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                    if ($result_detections->num_rows > 0) {
                                        while ($row = $result_detections->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td><strong>" . $row["detection_id"] . "</strong></td>";
                                            echo "<td>" . htmlspecialchars($row["request_id"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["detection_date"]) . "</td>";
                                            echo "<td><span class='badge bg-label-" . ($row["detection_result"] == 1 ? "danger" : "primary") . " me-1'>" . htmlspecialchars($row["detection_result"] == 1 ? "Fraud" : "Normal") . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No fraud detections found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="pagination">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item <?php if ($current_page_detections == 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_detections=1"><<</a>
                                        </li>
                                        <li class="page-item <?php if ($current_page_detections == 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_detections=<?php echo $current_page_detections - 1; ?>"><</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_detections; $i++): ?>
                                            <li class="page-item <?php if ($i == $current_page_detections) echo 'active'; ?>">
                                                <a class="page-link" href="?page_detections=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php if ($current_page_detections == $total_pages_detections) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_detections=<?php echo $current_page_detections + 1; ?>">></a>
                                        </li>
                                        <li class="page-item <?php if ($current_page_detections == $total_pages_detections) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_detections=<?php echo $total_pages_detections; ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Requests Table -->
                    <div class="card mt-4">
                        <h5 class="card-header">결제 요청 로그</h5>
                        <div class="table-responsive text-nowrap" id="payment-requests-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Card ID</th>
                                        <th>User ID</th>
                                        <th>Request Date</th>
                                        <th>Amount</th>
                                        <th>Request Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                    if ($result_requests->num_rows > 0) {
                                        while ($row = $result_requests->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td><strong>" . $row["request_id"] . "</strong></td>";
                                            echo "<td>" . htmlspecialchars($row["card_id"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["request_date"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
                                            echo "<td><span class='badge bg-label-" . ($row["request_status"] == 'Approved' ? "success" : ($row["request_status"] == 'Declined' ? "danger" : "warning")) . " me-1'>" . htmlspecialchars($row["request_status"]) . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No payment requests found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="pagination">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item <?php if ($current_page_requests == 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_requests=1"><<</a>
                                        </li>
                                        <li class="page-item <?php if ($current_page_requests == 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_requests=<?php echo $current_page_requests - 1; ?>"><</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_requests; $i++): ?>
                                            <li class="page-item <?php if ($i == $current_page_requests) echo 'active'; ?>">
                                                <a class="page-link" href="?page_requests=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php if ($current_page_requests == $total_pages_requests) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_requests=<?php echo $current_page_requests + 1; ?>">></a>
                                        </li>
                                        <li class="page-item <?php if ($current_page_requests == $total_pages_requests) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page_requests=<?php echo $total_pages_requests; ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <script>document.write(new Date().getFullYear());</script>, made with ❤️ by <a href="https://themeselection.com" target="_blank" class="footer-link fw-bolder">ThemeSelection</a>
                        </div>
                        <div>
                            <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                            <a href="https://themeselection.com/" target="_blank" class="footer-link me-4">More Themes</a>
                            <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/" target="_blank" class="footer-link me-4">Documentation</a>
                            <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank" class="footer-link me-4">Support</a>
                        </div>
                    </div>
                </footer>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../js/update_logs.js"></script>
</body>
</html>

<?php
$conn->close();
?>