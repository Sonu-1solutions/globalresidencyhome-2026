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



?>

<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <h1>Advisor Details</h1>
            </div>
        </div>
        <form action="advisor-save.php" method="post">

            <div class="row">

                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Advisor Name</label>
                    <select class="form-control" name="booking_advisor" required style="height:50px;">
                        <option value="">— Please choose Advisor —</option>
                        <?php
                            $advisornames = mysqli_query($con,"SELECT user_id, user_name FROM user_master WHERE user_status='Enable' AND user_department='User'");
                            while ($row = mysqli_fetch_assoc($advisornames)) {
                                echo '<option value="' .$row['user_id'] . '">'. htmlspecialchars($row['user_name']) .'</option>';
                            }
                        ?>
                    </select>
                </div>


                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Bookign Date</label>
                    <input type="date" class="form-control">
                </div>


                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Bookign No</label>
                    <select class="form-control" name="booking_no" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT booking_no FROM booking_master WHERE booking_status='Enabled' order by booking_id desc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            echo '<option value="' .$row['booking_no'] . '">'. htmlspecialchars($row['booking_no']) .'</option>';
                        }
                        ?>
                    </select>
                </div>


                <div class="col-md-2 mb-5">
                    <label class="fw-bold">State</label>
                    <select class="form-control" name="state" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_state) FROM booking_master WHERE booking_status='Enabled'order by booking_state asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_state'])) {
                                echo '<option value="' .$row['booking_state'] . '">'. htmlspecialchars($row['booking_state']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">City</label>
                    <select class="form-control" name="city" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_city) FROM booking_master WHERE booking_status='Enabled'order by booking_city asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_city'])) {
                                echo '<option value="' .$row['booking_city'] . '">'. htmlspecialchars($row['booking_city']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Project</label>
                    <select class="form-control" name="project" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_project) FROM booking_master WHERE booking_status='Enabled'order by booking_project asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_project'])) {
                                echo '<option value="' .$row['booking_project'] . '">'. htmlspecialchars($row['booking_project']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Plot Type</label>
                    <select class="form-control" name="plot_type" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_plottype) FROM booking_master WHERE booking_status='Enabled'order by booking_plottype asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_plottype'])) {
                                echo '<option value="' .$row['booking_plottype'] . '">'. htmlspecialchars($row['booking_plottype']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Plot Size</label>
                    <select class="form-control" name="plot_size" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_plotsize) FROM booking_master WHERE booking_status='Enabled'order by booking_plotsize asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_plotsize'])) {
                                echo '<option value="' .$row['booking_plotsize'] . '">'. htmlspecialchars($row['booking_plotsize']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Plot Area</label>
                    <select class="form-control" name="plot_area" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_plotarea) FROM booking_master WHERE booking_status='Enabled'order by booking_plotarea asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_plotarea'])) {
                                echo '<option value="' .$row['booking_plotarea'] . '">'. htmlspecialchars($row['booking_plotarea']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-5">
                    <label class="fw-bold">Plot Rate</label>
                    <select class="form-control" name="plot_rate" style="height:50px;">
                        <option value="">—  Select —</option>
                        <?php
                        $bookingno = mysqli_query($con,"SELECT DISTINCT(booking_plotrate) FROM booking_master WHERE booking_status='Enabled'order by booking_plotrate asc");
                        while ($row = mysqli_fetch_assoc($bookingno)) {
                            if (!empty($row['booking_plotrate'])) {
                                echo '<option value="' .$row['booking_plotrate'] . '">'. htmlspecialchars($row['booking_plotrate']) .'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mt-4">
                    <button type="submit" name="usergetadd" class="btn advisor-btn" style="height:45px;">
                        Submit
                    </button>
                </div>
            </div>

        </form>













        <!-- /Breadcrumb -->
    </div>
</div>

<?php
include "layout/footer-table.php";
?>