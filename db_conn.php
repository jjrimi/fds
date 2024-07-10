<?php
    define('DB_SERVER', 'db');
    define('DB_USERNAME', 'user');
    define('DB_PASSWORD', 'user1234');
    define('DB_NAME', 'card_db');

    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if(!$conn) {
        die('Error: '.mysqli_connect_error());
    }

    if (!mysqli_set_charset($conn, "utf8mb4")) {
        printf("Error loading character set utf8mb4: %s\n", mysqli_error($conn));
        exit;
    }

    // Ensure $conn is global
    $GLOBALS['conn'] = $conn;
?>
