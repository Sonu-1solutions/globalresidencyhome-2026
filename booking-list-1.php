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

<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">


<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <h1>Booking List</h1>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            <div class="col-sm-12">


                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped datatable ">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Booking Date</th>
                                    <th>Name</th>
                                    <th>Booking No</th>
                                    <th>Plot No</th>
                                    <th>Plot Size</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                $sn = 1;
                                if ($user_department == 'Admin') {
                                    $productqry = mysqli_query($con, "SELECT * FROM booking_master ORDER BY booking_id DESC");
                                } else {
                                    $productqry = mysqli_query($con, "SELECT * FROM booking_master WHERE booking_advisorid='$user_id' ORDER BY booking_id DESC");
                                }

                                while ($productdata = mysqli_fetch_assoc($productqry)) {
                                    ?>
                                    <tr id="row-<?php echo $productdata['booking_id']; ?>">
                                        <td><?php echo $sn; ?></td>
                                        <td><?php echo $productdata['booking_date']; ?></td>
                                        <td><?php echo $productdata['booking_fname']; ?>
                                            <?php echo $productdata['booking_lname']; ?>
                                        </td>
                                        <td><?php echo $productdata['booking_no']; ?></td>
                                        <td><?php echo $productdata['booking_plotno']; ?></td>
                                        <td><?php echo $productdata['booking_plotarea']; ?></td>
                                        <td><?php echo $productdata['booking_email']; ?></td>
                                        <td><?php echo $productdata['booking_phone']; ?></td>
                                        <td class="d-flex">
                                            <a href="#" class="mr-1">
                                                <button class="btn btn-sm btn-warning action-btn"> View </button>
                                            </a>
                                            <a href="#" class="mr-1">
                                                <button class="btn btn-sm btn-success action-btn"> Add Slip </button>
                                            </a>
                                            <a href="#" class="mr-1">
                                                <button class="btn btn-sm btn-dark action-btn"> View Slip </button>
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-booking"
                                                data-id="<?php echo $productdata['booking_id']; ?>" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                    $sn++;
                                }
                                ?>



                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

<?php
include "layout/footer-table.php";
?>