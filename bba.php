<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$user_name       = $user['user_name'] ?? '';
$user_email      = $user['user_email'] ?? '';
$user_department = $user['user_department'] ?? '';
$user_image      = $user['user_image'] ?? '';

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

		<!-- Breadcrumb -->
         
		<div class="d-block text-center page-breadcrumb mb-3 pagetitle">
			<div class="my-auto">
				<h1> BBA </h1>
			</div>
		</div>



		<table border="1" cellpadding="5" cellspacing="0">
    <tr>
		<th>Booking Date</th>
        <th>Name</th>
        <th>Addhar</th>
        <th>Plot-Size</th>
        <th>Plot No</th>
        <th>Amount</th>
		
        <!-- apne table ke hisaab se columns add karo -->
    </tr>

<?php

$booking_id = $_GET['booking_id'];
if (!$booking_id) {
    echo '<script> window.location="booking-list.php"; </script>';
    exit;
}

$query = "SELECT * FROM booking_master WHERE booking_id = $booking_id";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
?>
    <tr>
        <td><?php echo $row['booking_date']; ?></td>
       <td><?php echo $row['booking_fname'] . ' ' . $row['booking_lname']; ?></td>
	   <td><?php echo $row['booking_aadharno']; ?></td>
	   <td><?php echo $row['booking_plotsize']; ?></td>
	   <td><?php echo $row['booking_plotno']; ?></td>
	   <td><?php echo $row['booking_totalamt']; ?></td>

    </tr>
<?php } ?>
</table>


		<!-- /Breadcrumb -->	
	</div>
</div>


<?php
include "layout/footer.php";
?>