<?php

error_reporting(E_ALL);

/* 🔐 Session start & login check */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



/* ✅ Logged-in User ID */
$id = $_SESSION['user_id'];

// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";
// include "database.php";



/* ✅ Fetch user data */
$q = mysqli_query($con, "SELECT * FROM user_master WHERE user_id='$id'");
$data = mysqli_fetch_assoc($q);

/* ===== PROFILE IMAGE FIX ===== */

// server path (file check ke liye)
$upload_dir = __DIR__ . '/upload/user/';

// browser path (image show ke liye)
$upload_url = 'upload/user/';

if (!empty($data['user_image']) && file_exists($upload_dir . $data['user_image'])) {
    $profile_img = $upload_url . $data['user_image'];
} else {
    $profile_img = 'assets/img/favicon.png'; // default image
}


?>


<?php
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

include "layout/head.php";

?>



<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <h1>Profile Edit</h1>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            <form method="POST" action="profile-update.php" enctype="multipart/form-data">

                <input type="hidden" name="user_id" value="<?php echo $data['user_id']; ?>">

                <div class="row">


                    <div class="col-md-3"></div>
                    <div class="col-md-6 mt-3">
                        <div class="row">

                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">Name </label>
                                <input type="text" class="form-control py-4" name="name" value="<?php echo $data['user_name']; ?>">
                            </div>

                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">Email </label>
                                <input type="text" class="form-control py-4" name="email" value="<?php echo $data['user_email'];?>" >
                            </div>


                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">Mobile </label>
                                <input type="text" class="form-control py-4" name="mobile" value="<?php echo $data['user_mobile']; ?>" >
                            </div>

                            <div class="col-md-3  mb-5">
                                <img src="<?php echo $profile_img; ?>" class="rounded-circle mb-2"
                                    style="width:110px;height:110px;object-fit:cover;">
                            </div>

                            <div class="col-md-9 mb-5">
                                <label class="fw-bold">Profile Image</label>
                                <input type="file" class="form-control py-4" name="profile_image" accept="image/*">
                            </div>

                            <div class="col-md-12  mb-5">
                                <button type="submit" name="update_btn" class="btn advisor-btn py-3"
                                    style="width:100%">Update Profile</button>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </form>
        </div>
    </div>

</div>



<?php
include "layout/footer.php";
?>