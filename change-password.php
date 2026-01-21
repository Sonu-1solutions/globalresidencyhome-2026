<?php

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


// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";
// include("layout/head-table.php");



?>


<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <h1>Change Password</h1>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            <form action="update-password.php" method="post">

                <!-- USER ID -->
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                <div class="row">



                    <div class="col-md-3"></div>
                    <div class="col-md-6 mt-3">
                        <div class="row">
                            <?php
                            if (isset($_SESSION['msg'])) {
                                echo '<div class="alert alert-success text-center">' . $_SESSION['msg'] . '</div>';
                                unset($_SESSION['msg']);
                            }
                            ?>

                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">Current Password </label>
                                <input type="password" class="form-control py-4" name="current_password"
                                    placeholder="Enter current password">
                            </div>

                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">New Password </label>
                                <input type="password" class="form-control py-4" name="new_password"
                                    placeholder="Enter new password">
                            </div>


                            <div class="col-md-12 mb-5">
                                <label class="fw-bold">Confirm New Password </label>
                                <input type="password" class="form-control py-4" name="confirm_password"
                                    placeholder="Confirm new password">
                            </div>

                            <div class="col-md-12  mb-5">
                                <button type="submit" name="change_password_btn" class="btn advisor-btn py-3"
                                    style="width:100%">Change Password</button>
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