<?php
session_start();

if (!isset($_SESSION['email'])) {
  echo '<script type="text/javascript">
          alert("세션 정보가 없습니다.");
          window.location.href = "../index.php";
        </script>';
  exit();
}

include "../db_conn.php";

function generateCardNumber() {
    $cardNumber = '';
    for ($i = 0; $i < 4; $i++) {
        $cardNumber .= str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
    return $cardNumber;
}

function generateAccountNumber() {
    $accountNumber = '';
    for ($i = 0; $i < 4; $i++) {
        $accountNumber .= str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }
    return substr($accountNumber, 0, 20);
}

function randomExpirationDate() {
    $min = strtotime('+3 years');
    $max = strtotime('+5 years');
    $val = mt_rand($min, $max);
    return date('Y-m-d', $val);
}

function generateCSV() {
    return str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['card_type']) && isset($_POST['account_type'])) {
    $card_type = $_POST['card_type'];
    $account_type = $_POST['account_type'];
    $user_id = $_SESSION['user_id'];
    $card_number = generateCardNumber();
    $account_number = generateAccountNumber();
    $issue_date = date('Y-m-d');
    $expiration_date = randomExpirationDate();
    $csv = generateCSV();
    $card_status = 'Active';

    $sql = "INSERT INTO Cards (card_number, user_id, issue_date, expiration_date, csv, card_type, card_status, account_number, account_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssss", $card_number, $user_id, $issue_date, $expiration_date, $csv, $card_type, $card_status, $account_number, $account_type);

    if ($stmt->execute() === TRUE) {
        echo "<script>alert('카드 발급이 완료되었습니다!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html
  lang="ko"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>카드 발급 신청 | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
  <meta name="description" content="" />
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  
  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  
  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  
  <!-- Template customizer & Theme config files -->
  <script src="../assets/js/config.js"></script>
</head>
<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Navbar -->
      <?php include('../navmenu.php'); ?>
      <!-- /Navbar -->

      <!-- Content wrapper -->
      <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
          <h4 class="py-3 mb-4"><span class="text-muted fw-light">카드 발급 /</span> 신청</h4>
          <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> 카드 발급</a>
                    </li>
                  </ul>
                <div class="col-md-12">
                  <div class="card mb-4">
                    <h5 class="card-header">카드 발급 신청</h5>
                    <div class="card-body">
                      <form method="POST" action="">
                        <div class="mb-3">
                          <label for="card_type" class="form-label">카드 종류 선택:</label>
                          <select name="card_type" id="card_type" class="form-select" required>
                            <option value="visa">Visa</option>
                            <option value="mastercard">MasterCard</option>
                          </select>
                        </div>
                        
                        <div class="mb-3">
                          <label for="account_type" class="form-label">계좌 종류 선택:</label>
                          <select name="account_type" id="account_type" class="form-select" required>
                            <option value="Checking">Checking</option>
                            <option value="Savings">Savings</option>
                            <option value="Credit">Credit</option>
                          </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">발급받기</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <!-- / Content -->

        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
          <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
              ©
              <script>
                document.write(new Date().getFullYear());
              </script>
              , Template with by
              <a href="https://themeselection.com" target="_blank" class="footer-link fw-medium">ThemeSelection</a>
            </div>
          </div>
        </footer>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
      </div>
      <!-- / Content wrapper -->
    </div>
    <!-- / Layout container -->

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
</body>
</html>