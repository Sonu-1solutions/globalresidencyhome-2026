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
            <h1>Advisor Details</h1>
        </div>


        <form action="advisor-save.php" method="post">


            <div class="row">
                <div class="col-md-6 mb-5">
                    <label class="fw-bold">Advisor Name</label>
                    <select class="form-control" name="booking_advisor" required style="height:50px;">
                        <option value="">— Please choose Advisor —</option>
                        <?php
                        $query = mysqli_query(
                            $con,
                            "SELECT * FROM user_master WHERE user_status='Enable' AND user_department='User'"
                        );

                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<option value="' . $row['user_id'] . '">'
                                . htmlspecialchars($row['user_name']) .
                                '</option>';
                        }
                        ?>
                    </select>

                </div>

                <div class="col-md-6 mt-4">
                    <button type="submit" name="usergetadd" class="btn advisor-btn" style="height:45px;">
                        Submit
                    </button>
                </div>
            </div>

        </form>



        <!-- Popup / Model -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- Popup / Model -->



        <style>
            .modal-dialog {
                width: 90%;
                max-width: 100%;
            }

            .bgr {
                /* background-color: lavender; */
            }

            .bgb {
                /* background-color: lightblue; */
            }

            .fw600 {
                font-weight: 700;
            }
        </style>




        <div class="table-responsive">
            <table id="example1" class="table table-striped datatable">
                <thead>
                    <tr>

                        <th>Payment slip</th>
                        <th>Advisor</th>
                        <th>ID</th>
                        <th>Booking Date</th>
                        <th>Booking No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Aadhar No</th>
                        <th>PAN No</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Project</th>
                        <th>Plot Type</th>
                        <th>Plot Size</th>
                        <th>Plot No</th>
                        <th>Plot Area</th>
                        <th>Plot Rate</th>
                        <th>Total Amount</th>
                        <!-- <th>Status</th> -->
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $adviserid = $_POST['booking_advisor'];

                    echo $query = "SELECT * FROM booking_master WHERE booking_advisorid='$adviserid'";
                    $result = mysqli_query($con, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {



                            $bokingno = $row['booking_no'];

                            ?>
                            <tr id="row-<?php echo $row['booking_id']; ?>">

                                <td>
                                    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#myModal<?= $bokingno ?>">
                                        View</a>
                                </td>
                                <td><?php echo $row['booking_id']; ?></td>
                                <td><?php echo $row['booking_advisor']; ?></td>
                                <td><?php echo $row['booking_date']; ?></td>
                                <td><?php echo $row['booking_no']; ?></td>
                                <td><?php echo $row['booking_fname']; ?></td>
                                <td><?php echo $row['booking_lname']; ?></td>
                                <td><?php echo $row['booking_phone']; ?></td>
                                <td><?php echo $row['booking_email']; ?></td>
                                <td><?php echo $row['booking_aadharno']; ?></td>
                                <td><?php echo $row['booking_panno']; ?></td>
                                <td><?php echo $row['booking_state']; ?></td>
                                <td><?php echo $row['booking_city']; ?></td>
                                <td><?php echo $row['booking_address']; ?></td>
                                <td><?php echo $row['booking_project']; ?></td>
                                <td><?php echo $row['booking_plottype']; ?></td>
                                <td><?php echo $row['booking_plotsize']; ?></td>
                                <td><?php echo $row['booking_plotno']; ?></td>
                                <td><?php echo $row['booking_plotarea']; ?></td>
                                <td><?php echo $row['booking_plotrate']; ?></td>
                                <td><?php echo $row['booking_totalamt']; ?></td>
                                <!-- <td><?php echo $row['booking_status']; ?></td> -->
                                <td><?php echo $row['booking_createat']; ?></td>
                            </tr>


                            <!-- Payment Slip Models -->
                            <div class="modal fade" id="myModal<?= $bokingno ?>" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Plot No <?= $bokingno ?> </h4>
                                        </div>
                                        <div class="modal-body ">


                                            <?php

                                            $query1 = "SELECT * FROM `payment_slip` WHERE `registration_number`='$bokingno' Order BY `id` ASC limit 1";
                                            $result1 = mysqli_query($con, $query1);

                                            if ($result1 && mysqli_num_rows($result1) > 0) {
                                                while ($row1 = mysqli_fetch_assoc($result1)) {
                                                    ?>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-1 bgb">
                                                                <span class="fw600">Reg. No</span><br>
                                                                <?php echo $row1['registration_number']; ?>
                                                            </div>
                                                            <div class="col-md-1 bgr">
                                                                <span class="fw600">Plot No</span><br>
                                                                <?php echo $row1['plot_no']; ?>
                                                            </div>
                                                            <div class="col-md-2 bgb">
                                                                <span class="fw600">Plot Size</span><br>
                                                                <?php echo $row1['plot_size']; ?>
                                                            </div>
                                                            <div class="col-md-2 bgb">
                                                                <span class="fw600">Project Name</span><br>
                                                                <?php echo $row1['project_name']; ?>
                                                            </div>
                                                            <div class="col-md-2 bgr">
                                                                <span class="fw600">Receive Name</span><br>
                                                                <?php echo $row1['receive_name']; ?>
                                                            </div>
                                                            <div class="col-md-2 bgb">
                                                                <span class="fw600">Advisor Percentage</span><br>
                                                                <!-- <?php echo $row1['percentage']; ?> -->
                                                            </div>
                                                            <div class="col-md-2 bgb">
                                                                <span class="fw600">Advisor Amount</span><br>
                                                                <!-- <?php echo $row1['advisor_amount']; ?> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>


                                                    <?php
                                                }
                                            } else {
                                                echo "<center><h5>Payment Installments not created yet</h5><center>";
                                            }
                                            ?>
                                            <hr>

                                            <?php

                                            $query1 = "SELECT * FROM `payment_slip` WHERE `registration_number`='$bokingno'";
                                            $result1 = mysqli_query($con, $query1);

                                            if ($result1 && mysqli_num_rows($result1) > 0) {
                                                while ($row1 = mysqli_fetch_assoc($result1)) {
                                                    ?>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-3 bgr">
                                                                <span class="fw600">Payment Date</span>
                                                                <?php echo $row1['payment_by_date']; ?>
                                                            </div>
                                                            <div class="col-md-3 bgb">
                                                                <span class="fw600">Date</span>
                                                                <?php echo $row1['current_date']; ?>
                                                            </div>
                                                            <div class="col-md-3 bgr">
                                                                <span class="fw600">Slip No</span>
                                                                <?php echo $row1['slip_id']; ?>
                                                            </div>
                                                            <div class="col-md-3 bgb">
                                                                <span class="fw600">Payment By</span>
                                                                <?php echo $row1['payment_by']; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <div class="col-md-3 bgb">
                                                                <span class="fw600">Drawn On</span>
                                                                <?php echo $row1['drawn_on']; ?>
                                                            </div>
                                                            <div class="col-md-3 bgr">
                                                                <span class="fw600">Total Ammount</span>
                                                                <?php echo $row1['total_amout']; ?>
                                                            </div>
                                                            <div class="col-md-6 bgr">
                                                                <span class="fw600">Ammount in Word</span>
                                                                <?php echo $row1['amount_in_word']; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<center><h5>Payment Installments not created yet</h5><center>";
                                            }
                                            ?>
                                        </div>

                                    </div>

                                </div>
                            </div>


                            <!-- Payment Slip -->


                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='23' class='text-center'>No Records Found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>




        <!-- /Breadcrumb -->
    </div>
</div>



<?php
include "layout/footer-table.php";
?>