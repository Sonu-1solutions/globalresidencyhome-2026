<?php
    include('database.php');

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo '<script> window.location="index"; </script>';
        exit();
    }

    // Get the user ID from the query string
    $getuserid = isset($_GET['getuserid']) ? $_GET['getuserid'] : '';

    // Validate user ID
    if (empty($getuserid) || !is_numeric($getuserid)) {
        echo '<script> window.location="user-list"; </script>';
        exit();
    }

    // Delete the user from the database
    $sql = "DELETE FROM `user_master` WHERE user_id='$getuserid'";
    mysqli_query($con, $sql);

    // Redirect to user list
   // redirect back
header("Location: advisor-list.php");
exit;
?>

