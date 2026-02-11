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


<?php
// require 'lock.php';

// Handle delete request
if (isset($_POST['delete_booking_id'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['delete_booking_id']);

    // Check if the user has permission to delete
    if ($user_department == 'Admin') {
        $query = "DELETE FROM booking_master WHERE booking_id = '$booking_id'";
    } else {
        $query = "DELETE FROM booking_master WHERE booking_id = '$booking_id' AND booking_advisorid = '$user_id'";
    }

    if (mysqli_query($con, $query)) {
        if (mysqli_affected_rows($con) > 0) {
            echo '<script>alert("Booking deleted successfully"); window.location.href="booking-list.php";</script>';
        } else {
            echo '<script>alert("No booking found with the provided ID or you lack permission.");</script>';
        }
    } else {
        echo '<script>alert("Error deleting booking: ' . mysqli_error($con) . '");</script>';
    }
}
?>

<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">







<style>
    table.table.dataTable>tbody>tr td,
    table.table.dataTable>thead>tr th {
        font-size: 13px;
        padding: 5px 10px;
    }
</style>


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
                        <table id="example1" class="table table-striped datatable ">
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
                                    <th>Advisor Name</th>
                                    <th>Pending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                $sn = 1;
                                $productqry = mysqli_query(
                                    $con,
                                    "SELECT * FROM booking_master 
                                    WHERE booking_advisorid = '$uid'
                                    ORDER BY booking_id DESC"
                                );

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
                                        <td><?php echo $productdata['booking_advisor']; ?></td>

                                        <td>


                                            <?php
                                            $totalreceiveamt = 0;
                                            $brokragetamotrec = 0;
                                            $cumulative_amount = 0;
                                            $totalamt = $productdata['booking_totalamt'];
                                            $paymentslipno = $productdata['booking_no'];

                                            $qry = "SELECT SUM(total_amout)   AS total_received, SUM(advisor_amount) AS total_brokageamt FROM payment_slip WHERE registration_number = '$paymentslipno'";

                                            $res = mysqli_query($con, $qry);
                                            $row = mysqli_fetch_assoc($res);
                                            //    echo"</pre>";
                                            //     print_r($row);
                                        
                                            $totalreceiveamt = (float) ($row['total_received'] ?? 0);
                                            $brokragetamotrec = (float) ($row['total_brokageamt'] ?? 0);


                                            // echo "<b>Total Amt : </b>".$totalamt;
// echo " | <b>Received Amt : </b>".$totalreceiveamt;
// echo " | <b>Balance Amt : </b>".$totalpendingbalnce = $totalamt - $totalreceiveamt;
                                            echo $totalpendingbalnce = $totalamt - $totalreceiveamt;

                                            // echo "<br>";
                                        
                                            // echo "<b>A-Total Amt : </b>".$productdata['advisor_amount'];
// echo " | <b>A-Received Amt : </b>".$brokragetamotrec;
// echo " | <b>A-Balance Amt : </b>".$totalpendingadvisor = $productdata['advisor_amount'] - $brokragetamotrec;
                                        
                                            ?>

                                        </td>

                                        <td class="d-flex">
                                            <a href="booking-view.php?booking_id=<?php echo $productdata['booking_id']; ?>"
                                                class="mr-1">
                                                <button class="btn btn-sm btn-warning action-btn"> View </button>
                                            </a>
                                            <a href="booking-slip.php?slip_id=<?php echo $productdata['booking_no']; ?>"
                                                class="mr-1">
                                                <button class="btn btn-sm btn-success action-btn" style="display:none;"> Add
                                                    Slip </button>
                                            </a>
                                            <a href="booking-slip-list.php?slip_id=<?php echo $productdata['booking_no']; ?>"
                                                class="mr-1" target="_blank">
                                                <button class="btn btn-sm btn-success action-btn"> View Slip </button>
                                            </a>
                                            <form action="" method="post" onsubmit="return confirmSubmission()">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <input type="hidden" name="delete_booking_id"
                                                    value="<?php echo $productdata['booking_id']; ?>">
                                            </form>
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




<script>

    function confirmSubmission() {
        var agree = confirm("Are you sure you want to submit the form?");
        if (agree) {
            return true;
        } else {
            return false;
        }
    }
</script>


<!-- <script>
    $(document).ready(function () {
        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable('#example1')) {
            $('#example1').DataTable().destroy();
        }

        // Initialize DataTable
        $('#example1').DataTable({
            "pageLength": 100
        });

        // Delete booking functionality
        $('.delete-booking').click(function () {
            var bookingId = $(this).data('id'); // Fixed: Correctly use 'data-id'
            if (confirm('Are you sure you want to delete this booking?')) {
                // Create a form dynamically to submit the delete request
                var form = $('<form>', {
                    'method': 'POST',
                    'action': ''
                }).append($('<input>', {
                    'type': 'hidden',
                    'name': 'delete_booking_id',
                    'value': bookingId
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });
</script> -->

<?php
include "layout/footer-table.php";
?>