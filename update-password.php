<?php
include 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['change_password_btn'])) {

    $user_id          = $_POST['user_id'];
    $current_password = md5($_POST['current_password']);
    $new_password     = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    // New & Confirm password check
    if ($new_password !== $confirm_password) {
        $_SESSION['msg'] = "New password and confirm password do not match";
        header("Location: change-password.php");
        exit;
    }

    // Fetch current password from DB
    $check = mysqli_query(
        $con,
        "SELECT user_password FROM user_master WHERE user_id='$user_id'"
    );

    if (mysqli_num_rows($check) == 0) {
        $_SESSION['msg'] = "User not found";
        header("Location: change-password.php");
        exit;
    }

    $row = mysqli_fetch_assoc($check);

    // Verify current password
    if ($row['user_password'] !== $current_password) {
        $_SESSION['msg'] = "Current password is incorrect";
        header("Location: change-password.php");
        exit;
    }

    // Update new password
    $update = mysqli_query(
        $con,
        "UPDATE user_master 
         SET user_password='$new_password' 
         WHERE user_id='$user_id'"
    );

    if ($update) {
        $_SESSION['msg'] = "Password updated successfully";
    } else {
        $_SESSION['msg'] = "Password update failed";
    }

    header("Location: change-password.php");
    exit;
}
?>
