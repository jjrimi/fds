<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo '<script type="text/javascript">
            alert("세션 정보가 없습니다.");
            window.location.href = "../index.php";
          </script>';
    exit();
}

include('../db_conn.php');

$userEmail = $_SESSION['email'];

$query = "SELECT * FROM Users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
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

    <title>계정 설정</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
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
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">정보 설정 /</span> 내 정보</h4>

              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> 내 정보</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-account-settings-notifications.html"
                        ><i class="bx bx-bell me-1"></i> 알림 구독 설정</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="<?php echo $base_url; ?>account/save_family_info.php"
                        ><i class="bx bx-link-alt me-1"></i> 가족 정보</a
                      >
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <h5 class="card-header">프로필</h5>
                    <!-- Account -->
                    <div class="card-body">
                        <form id="formAccountSettings" method="POST" action="update_profile.php" enctype="multipart/form-data">
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($user['avatar']); ?>" alt="user-avatar"
                                    class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                        <span class="d-none d-sm-block">프로필 이미지 업로드</span>
                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" name="avatar" class="account-file-input" hidden
                                            accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                        <i class="bx bx-reset d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">초기화</span>
                                    </button>
                                    <p class="text-muted mb-0"> JPG, GIF 또는 PNG.</p>
                                </div>
                            </div>
                            <hr class="my-0" />
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="fullname" class="form-label">이름</label>
                                    <input class="form-control" type="text" id="fullname" name="fullname"
                                        value="<?php echo $user['full_name']; ?>" autofocus />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">이메일</label>
                                    <input class="form-control" type="text" id="email" name="email"
                                        value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phoneNumber" class="form-label">전화번호</label>
                                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                                        value="<?php echo htmlspecialchars($user['phone_number']); ?>" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="gender">성별</label>
                                    <select id="gender" name="gender" class="select2 form-select">
                                        <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>남성</option>
                                        <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>여성</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="dateOfBirth" class="form-label">생년월일</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth"
                                        value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="address" class="form-label">가입 날짜</label>
                                    <input type="text" class="form-control" id="registrationdate" name="registrationdate"
                                        value="<?php echo htmlspecialchars($user['registration_date']); ?>" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">마지막 로그인 시각</label>
                                    <input class="form-control" type="text" id="lastlogin" name="lastlogin"
                                        value="<?php echo htmlspecialchars($user['last_login']); ?>" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">권한</label>
                                    <input class="form-control" type="text" id="role" name="role"
                                        value="<?php echo htmlspecialchars($user['role']); ?>" readonly />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">저장하기</button>
                                <button type="reset" class="btn btn-outline-secondary">취소</button>
                            </div>
                        </form>
                    </div>
                  </div>
                  <!-- Change Password Section -->
                  <div class="card mb-4">
                      <h5 class="card-header">비밀번호 변경</h5>
                      <div class="card-body">
                          <form id="formChangePassword" method="POST" action="update_password.php">
                              <!-- <div class="mb-3 col-12">
                                  <label for="oldPassword" class="form-label">현재 비밀번호</label>
                                  <input type="password" class="form-control" id="oldPassword" name="oldPassword" required />
                              </div> -->
                              <div class="mb-3 col-12">
                                  <label for="newPassword" class="form-label">새 비밀번호</label>
                                  <input type="password" class="form-control" id="newPassword" name="newPassword" required />
                              </div>
                              <div class="mb-3 col-12">
                                  <label for="confirmNewPassword" class="form-label">새 비밀번호 확인</label>
                                  <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword"
                                      required />
                              </div>
                              <button type="submit" class="btn btn-primary">비밀번호 변경</button>
                          </form>
                      </div>
                  </div>
                  <!-- Delete Account Section -->
                  <div class="card mb-4">
                      <h5 class="card-header">계정 삭제</h5>
                      <div class="card-body">
                          <div class="mb-3 col-12 mb-0">
                              <div class="alert alert-warning">
                                  <h6 class="alert-heading mb-1">계정을 삭제하시겠습니까?</h6>
                                  <p class="mb-0">계정을 삭제하면 되돌릴 수 없습니다. 신중히 선택하세요.</p>
                              </div>
                          </div>
                          <form id="formAccountDeactivation" action="delete_account.php">
                              <button type="submit" class="btn btn-danger deactivate-account">계정 삭제</button>
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
    <script src="../assets/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
