<?php
session_start(); // 세션 시작

include "../db_conn.php";
date_default_timezone_set('Asia/Seoul'); // 서울 시간대 설정


// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 로그인 확인 및 권한 확인
if (!isset($_SESSION['user_name']) || !$_SESSION['user_name']) {
    header("Location: ../index.php");
    exit();
}

$user_role = $_SESSION['role'];
if ($user_role !== 'admin') {
    echo "<script>alert('권한이 부족합니다.'); window.location.href = '../index.php';</script>";
    exit();
}

// Users 테이블에서 데이터 가져오기
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>

<html
  lang="ko"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>사용자 권한 관리</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
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
        <!-- Menu  & Navbar-->
        <?php include("../navmenu.php"); ?>
          <!-- / Menu & Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">관리자 /</span> 사용자 권한 관리</h4>

              <!-- Basic Bootstrap Table -->
              <div class="container mt-5">
                <div class="card">
                  <h5 class="card-header">유저 정보 열람 및 수정</h5>
                  <div class="table-responsive text-nowrap">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>User ID</th>
                          <th>User Name</th>
                          <th>Email</th>
                          <th>Full Name</th>
                          <th>Phone Number</th>
                          <th>Date of Birth</th>
                          <th>Registration Date</th>
                          <th>Last Login</th>
                          <th>Status</th>
                          <th>Role</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                        <?php
                        if ($result->num_rows > 0) {
                            // 각 행에 대해 데이터를 출력합니다
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>" . $row["user_id"] . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row["user_name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["phone_number"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["date_of_birth"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["registration_date"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["last_login"]) . "</td>";
                                echo "<td><span class='badge bg-label-" . ($row["status"] == 'active' ? "primary" : ($row["status"] == 'inactive' ? "danger" : "warning")) . " me-1'>" . htmlspecialchars($row["status"]) . "</span></td>";
                                echo "<td><span class='badge bg-label-" . ($row["role"] == 'admin' ? "info" : "secondary") . " me-1'>" . htmlspecialchars($row["role"]) . "</span></td>";
                                echo "<td>
                                        <div class='dropdown'>
                                            <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'>
                                                <i class='bx bx-dots-vertical-rounded'></i>
                                            </button>
                                            <div class='dropdown-menu'>
                                                <a class='dropdown-item edit-btn' href='javascript:void(0);' data-user-id='" . $row["user_id"] . "'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                                                <a class='dropdown-item' href='javascript:void(0);'><i class='bx bx-trash me-1'></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <!-- / Content -->

            <!-- Modal for Edit Confirmation -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Confirm Edit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" required>
                                </div>
                                <div class="mb-3">
                                    <label for="newRole" class="form-label">New Role</label>
                                    <select class="form-control" id="newRole">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="newStatus" class="form-label">New Status</label>
                                    <select class="form-control" id="newStatus">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <input type="hidden" id="editUserId">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Edit button click event
                document.querySelectorAll('.edit-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const userId = this.getAttribute('data-user-id');
                        document.getElementById('editUserId').value = userId;
                        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                        editModal.show();
                    });
                });

                // Form submission event
                document.getElementById('editForm').addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    const currentPassword = document.getElementById('currentPassword').value;
                    const newRole = document.getElementById('newRole').value;
                    const newStatus = document.getElementById('newStatus').value;
                    const userId = document.getElementById('editUserId').value;

                    // AJAX request to verify password and update user information
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'update_user.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (this.status == 200) {
                            const response = JSON.parse(this.responseText);
                            if (response.success) {
                                alert('유저 정보 업데이트 성공');
                                location.reload();
                            } else {
                                alert('유저 정보 업데이트 실패: ' + response.message);
                            }
                        } else {
                            alert('에러가 발생하였습니다.');
                        }
                    };
                    xhr.send('currentPassword=' + encodeURIComponent(currentPassword) + '&newRole=' + encodeURIComponent(newRole) + '&newStatus=' + encodeURIComponent(newStatus) + '&userId=' + encodeURIComponent(userId));
                });
            </script>
            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                </div>
                <div>
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

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
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

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>