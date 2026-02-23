<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// SESSION START
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// DATABASE
require_once "database.php";

// USER ID
$uid = (int) $_SESSION['user_id'];

// FETCH USER
$q = mysqli_query($con, "
        SELECT user_name, user_email, user_department, user_image
        FROM user_master
        WHERE user_id = $uid
    ");

$user = mysqli_fetch_assoc($q) ?? [];

// SAFE VARIABLES
$user_name = $user['user_name'] ?? '';
$user_email = $user['user_email'] ?? '';
$user_department = $user['user_department'] ?? '';
$user_image = $user['user_image'] ?? '';

// PROFILE IMAGE
$uploadPath = __DIR__ . "/upload/user/" . $user_image;
if (!empty($user_image) && file_exists($uploadPath)) {
    $profile_img = "upload/user/" . $user_image;
} else {
    $profile_img = "dist/img/favicon.png";
}

// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";

?>





<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">



        <!-- BBA -->

        <?php

        $booking_no = $_GET['booking_no'];
        $booking_id = $_GET['booking_id'];


        if (!$booking_no) {
            echo '<script> window.location="booking-list.php"; </script>';
            exit;
        }

        if (!$booking_id) {
            echo '<script>window.location="booking-list.php";</script>';
            exit;
        }


        $query = "SELECT * FROM bba WHERE booking_no='$booking_no'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);





        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<script>alert('BBA not generated')</script>";
            echo "<script> window.location='booking-view.php?booking_id=$booking_id'; </script>";
            exit;
        }
        ?>

















<style>
    .page-break{
        page-break-after: always;
    }
</style>

<form method="POST" action="generate_pdf.php">
    <button type="submit">Download PDF</button>
</form>

<?php
    include 'pdf_content.php';
?>


























    </div>
</div>




<?php
include "layout/footer.php";
?>