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


$getuserid = $_GET['getuserid'];
if ($getuserid) {
	$productqry1 = mysqli_query($con, "select * from user_master where user_id='$getuserid'");
	$productdata1 = mysqli_fetch_assoc($productqry1);
} else {
	echo '<script> window.location="user-list"; </script>';
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
				<h1>Edit Advisor</h1>
			</div>
		</div>

		<div class="row">
			<form action="advisor-add-db.php" method="post">

				<!-- Hidden User ID -->
				<input type="hidden" name="usergetid" value="<?php echo $productdata1['user_id']; ?>">

				<div class="row">


					<div class="col-md-3"></div>
					<div class="col-md-6 mt-3">
						<div class="row">

							<div class="col-md-6 mb-5">
								<label class="fw-bold">User Name *</label>
								<input type="text" name="user_name" class="form-control py-4 "
									value="<?php echo $productdata1['user_name']; ?>" required>
							</div>

							<div class="col-md-6 mb-5">
								<label class="fw-bold">User Mobile *</label>
								<input type="text" name="user_mobile" class="form-control py-4"
									value="<?php echo $productdata1['user_mobile']; ?>" required>
							</div>


							<div class="col-md-6 mb-5">
								<label class="fw-bold">User Email *</label>
								<input type="email" name="user_email" class="form-control py-4"
									value="<?php echo $productdata1['user_email']; ?>" required>
							</div>

							<!-- <div class="col-md-6  mb-5">
								<label class="fw-bold">User Password *</label>
								<input type="password" name="user_password" class="form-control py-4"
									placeholder="Leave blank to keep old password">
							</div> -->

							<div class="col-md-6 mb-5">
								<label class="fw-bold">User Department *</label>
								<select name="user_department" class="form-control " style="height:50px;" required>
									<option value="Admin" <?php if ($productdata1['user_department'] == 'Admin')
										echo 'selected'; ?>>
										Admin
									</option>
									<option value="User" <?php if ($productdata1['user_department'] == 'User')
										echo 'selected'; ?>>
										User
									</option>
								</select>
							</div>

							<!-- <div class="col-md-6 mb-5">
								<label class="fw-bold">Profile Image</label>
								<input type="file" class="form-control py-4" name="profile_image" accept="image/*">
							</div> -->

							<div class="col-md-12  mb-5">
								<button type="submit" name="usergetadd" class="btn advisor-btn py-3"
									style="width:100%">Submit</button>
							</div>


						</div>
					</div>
					<div class="col-md-3"></div>

				</div>
			</form>
		</div>

		<!-- /Breadcrumb -->
	</div>
</div>


<?php
include "layout/footer.php";
?>