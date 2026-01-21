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



$getuserid=$_GET['getuserid'];
if($getuserid)
{
$productqry1=mysqli_query($con,"select * from user_master where user_id='$getuserid'");
$productdata1=mysqli_fetch_assoc($productqry1);
}
else
{
echo '<script> window.location="user-list"; </script>';
}

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
      <form action="user-add-db.php" method="post">

        <input type="hidden" name="usergetid" value="0">

        <div class="row">


          <div class="col-md-3"></div>
          <div class="col-md-6 mt-3">
            <div class="row">

              <div class="col-md-6 mb-5">
                <label class="fw-bold">User Name *</label>
                <input type="hidden" class="form-control" name="usergetid" value="<?php echo $productdata1['user_id'] ?>" required />
              </div>

              <div class="col-md-6 mb-5">
                <label class="fw-bold">User Mobile *</label>
                <input type="text" class="form-control py-4" name="user_mobile" required>
              </div>


              <div class="col-md-6 mb-5">
                <label class="fw-bold">User Email *</label>
                <input type="email" class="form-control py-4" name="user_email" required>
              </div>

              <div class="col-md-6  mb-5">
                <label class="fw-bold">User Password *</label>
                <input type="password" class="form-control py-4" name="user_password" required>
              </div>

              <div class="col-md-6 mb-5">
                <label class="fw-bold">User Department *</label>
                <select class="form-control department" name="user_department" required>
                  <option value="">Select</option>
                  <option value="Admin">Admin</option>
                  <option value="User">Advisor</option>
                </select>
              </div>

              <div class="col-md-6 mb-5">
                <label class="fw-bold">Profile Image</label>
                <input type="file" class="form-control py-4" name="profile_image" accept="image/*">
              </div>

              <div class="col-md-12  mb-5">
                <button type="submit" name="usergetadd" class="btn advisor-btn py-3" style="width:100%">Submit</button>
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