<?php
function get_user_info($user_id) { 
    require_once(dirname(__FILE__) . '/../db_conn.php');
    
    // Access global $conn variable
    global $conn;

    // Check if $conn is set and is of type mysqli
    if (!isset($conn) || !($conn instanceof mysqli)) {
        die('Database connection failed');
    }

    $sql = "SELECT * FROM Users WHERE user_id=?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res) > 0) { 
            return mysqli_fetch_array($res, MYSQLI_ASSOC); 
        } 
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_stmt_error($stmt);
    }
    return null; 
}
?>
