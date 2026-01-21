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

// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";

//  TOTAL USERS COUNT 
$getuserqry = "
    SELECT COUNT(user_id) AS total_users 
    FROM user_master 
    WHERE user_status='Enable' 
    AND user_id!='1'
";
$getuserres = mysqli_query($con, $getuserqry);
$getuserrow = mysqli_fetch_assoc($getuserres);

$total_users = $getuserrow['total_users'] ?? 0;






/* TOTAL ONLINE BOOKING  */
$getOnlineBookingQry = "
    SELECT COUNT(booking_id) AS total_online 
    FROM booking_master 
    WHERE booking_status = 'Enabled'
";
$getOnlineBookingRes = mysqli_query($con, $getOnlineBookingQry);
$getOnlineBookingRow = mysqli_fetch_assoc($getOnlineBookingRes);

$total_online_booking = $getOnlineBookingRow['total_online'] ?? 0;


/* ================= TOTAL DIRECT BOOKING ================= */
/* agar direct ka koi field hai (example: booking_type = 'Direct') */
// $getDirectBookingQry = "
//     SELECT COUNT(booking_id) AS total_direct 
//     FROM booking_master 
//     WHERE booking_status = 'Enabled'
//     AND booking_type = 'Direct'
// ";
// $getDirectBookingRes = mysqli_query($con, $getDirectBookingQry);
// $getDirectBookingRow = mysqli_fetch_assoc($getDirectBookingRes);

// $total_direct_booking = $getDirectBookingRow['total_direct'] ?? 0;








$getuserqry1 = "
    SELECT COUNT(user_id) AS total_users 
    FROM user_master 
    WHERE user_status='Disable' 
    AND user_id!='1'
";
$getuserres1 = mysqli_query($con, $getuserqry1);
$getuserrow1 = mysqli_fetch_assoc($getuserres1);

$remaining_users = $getuserrow1['total_users'] ?? 0;


?>

<!-- Page Wrapper -->
<div class="page-wrapper">

  <div class="content">

    <!-- Breadcrumb -->
    <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
      <div class="my-auto">
        <h1>Add Advisor</h1>
      </div>
    </div>
    <!-- /Breadcrumb -->

    <div class="row">
      <form method="post" action="advisor-add-db.php" enctype="multipart/form-data">

        <input type="hidden" name="usergetid" value="0">

        <div class="row">


          <div class="col-md-3"></div>
          <div class="col-md-6 mt-3">
            <div class="row">

              <div class="col-md-6 mb-5">
                <label class="fw-bold">User Name *</label>
                <input type="text" class="form-control py-4" name="user_name" required>
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






  </div>

</div>

<!-- Delete Modal -->
<!-- <div class="modal fade" id="delete_modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
          <i class="ti ti-trash-x fs-36"></i>
        </span>
        <h4 class="mb-1">Confirm Delete</h4>
        <p class="mb-3">You want to delete all the marked items, this cant be undone once you delete.</p>
        <div class="d-flex justify-content-center">
          <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
          <a href="employee-dashboard.html" class="btn btn-danger">Yes, Delete</a>
        </div>
      </div>
    </div>
  </div>
</div> -->
<!-- /Delete Modal -->

<?php
include "layout/footer.php";
?>