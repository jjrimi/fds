<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['user_id'])) {
    echo '<script type="text/javascript">
            alert("세션 정보가 없습니다.");
            window.location.href = "../index.php";
          </script>';
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Fetch family information for the current user
$sql_get_family_info = "SELECT U.user_name AS family_user_id, F.family_member_name, F.relationship, F.contact_info 
                        FROM Family F 
                        JOIN Users U ON F.family_user_id = U.user_id 
                        WHERE F.user_id = ?";
$stmt_get_family_info = mysqli_prepare($conn, $sql_get_family_info);
mysqli_stmt_bind_param($stmt_get_family_info, "i", $current_user_id);
mysqli_stmt_execute($stmt_get_family_info);
$result_get_family_info = mysqli_stmt_get_result($stmt_get_family_info);
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
    <meta
      name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Family Information</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
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
                    <h4 class="py-3 mb-4"><span class="text-muted fw-light">정보 설정 /</span> 가족 정보</h4>
                    <div class="row">
                        <div class="col-md-12">
                        <ul class="nav nav-pills flex-column flex-md-row mb-3">
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>account/mypage.php"
                                    ><i class="bx bx-link-alt me-1"></i> 내 정보</a>
                                <li class="nav-item">
                                    <a class="nav-link" href="pages-account-settings-notifications.html"
                                        ><i class="bx bx-bell me-1"></i> 알림 구독 설정</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> 가족 정보</a>
                                    </li>
                            </li>
                        </ul>
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <h5 class="card-header">가족 정보 설정</h5>
                                <div class="card-body">
                                    <form id="familyInfoForm" action="family_info.php" method="post">
                                        <div class="mb-3">
                                            <label for="family_member_name" class="form-label">가족 구성원 이름</label>
                                            <input type="text" id="family_member_name" name="family_member_name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="relationship" class="form-label">관계</label>
                                            <input type="text" id="relationship" name="relationship" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact_info" class="form-label">연락처 정보</label>
                                            <input type="text" id="contact_info" name="contact_info" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">저장</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Display family information in table format -->
                            <div class="card mt-4">
                                <h5 class="card-header">저장된 가족 정보</h5>
                                <div class="table-responsive text-nowrap" id="family-info-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>가족 아이디</th>
                                                <th>가족 구성원 이름</th>
                                                <th>관계</th>
                                                <th>연락처 정보</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php
                                            if (mysqli_num_rows($result_get_family_info) > 0) {
                                                while ($row_get_family_info = mysqli_fetch_assoc($result_get_family_info)) {
                                                    echo "<tr>";
                                                    echo "<td><strong>" . htmlspecialchars($row_get_family_info['family_user_id']) . "</strong></td>";
                                                    echo "<td>" . htmlspecialchars($row_get_family_info['family_member_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row_get_family_info['relationship']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row_get_family_info['contact_info']) . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4'>저장된 가족 정보가 없습니다.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
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
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>