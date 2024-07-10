<?php
    require_once(dirname(__FILE__) . '/account/get_user_info.php');

    // 만약 세션에 유저 아이디가 존재한다면 해당 아이디로 유저 정보를 가져옵니다.
    $userInfo = null;
    if (isset($_SESSION['user_id'])) {
        $userInfo = get_user_info($_SESSION['user_id']);
    }
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo $page_title; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Brand-->
            <div class="mx-auto">
                <a class="navbar-brand ps-3">고객 게시판</a>
            </div>
            <!-- Navbar-->
            <?php if (isset($showNavbar) && $showNavbar): ?>
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" id="userMenu" aria-labelledby="navbarDropdown">
                            <?php if ($userInfo): ?>
                                <li><a class="dropdown-item" href="/account/mypage.php"><div><?php echo $userInfo ? $userInfo['fullname'] : ''; ?></div></a></li>
                                <li><hr class="dropdown-divider"/></li>
                                <li><a class="dropdown-item" href="/account/logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/account/login.html">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="/index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Home
                            </a>
                            <a class="nav-link" href="/account/mypage.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                MyPage
                            </a>
                            <div class="sb-sidenav-menu-heading">Board</div>
                            <a class="nav-link" href="/board/board.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                                게시판
                            </a>
                            <a class="nav-link" href="/board/board_write.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-pen"></i></div>
                                글쓰기
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                