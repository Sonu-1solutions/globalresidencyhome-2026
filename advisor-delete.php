<?php
session_start();
include('database.php');

$getuserid = $_GET['getuserid'] ?? '';

if (!is_numeric($getuserid)) {
    header("Location: advisor-list.php");
    exit();
}

/* TRASH = SOFT DELETE */
if (isset($_GET['delete']) && $_GET['delete'] == 1) {

    mysqli_query($con,
        "UPDATE user_master 
         SET is_deleted = 1 
         WHERE user_id = '$getuserid'"
    );

/* ENABLE / DISABLE (UNCHANGED) */
} elseif (isset($_GET['status'])) {

    $status = $_GET['status'];
    if ($status === 'Enable' || $status === 'Disable') {
        mysqli_query($con,
            "UPDATE user_master 
             SET user_status = '$status' 
             WHERE user_id = '$getuserid'"
        );
    }
}

header("Location: advisor-list.php");
exit();
?>
