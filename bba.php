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
            $row = mysqli_fetch_assoc($result);
            ?>
            <tr>
                <td><?php echo $row['booking_date']; ?></td>
                <td><?php echo $row['booking_fname'] . ' ' . $row['booking_lname']; ?></td>
                <td><?php echo $row['booking_aadharno']; ?></td>
                <td><?php echo $row['booking_plotsize']; ?></td>
                <td><?php echo $row['booking_plotno']; ?></td>
                <td><?php echo $row['booking_totalamt']; ?></td>

            </tr>
        </table>




        <!-- /Breadcrumb -->





        <?php
        $images = glob("assets/bba/*.jpg");

        $srno = 0;
        foreach ($images as $img) {
            $srno++;
            ?>
            <div style="margin-bottom:30px; text-align:center;">

                <!-- ✅ Sirf pehli image par name -->
                <?php if ($srno == 1) { ?>
                    <div style="position:relative; width:100%;">

                        <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                        <div style="
                            position:absolute;
                            bottom:320px;   /*  yaha adjust kar sakte ho */
                            left:95px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;
                        ">
                            <?php echo $row['booking_fname'] . ' ' . $row['booking_lname']; ?>
                        </div>

                    </div>

                <?php } elseif ($srno == 2) { ?>
                    <div style="position:relative; width:100%;">

                        <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                        <div style="
                            position:absolute;
                            bottom:197px;   /*  yaha adjust kar sakte ho */
                            left:200px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_fname'] . ' ' . $row['booking_lname'] . ' (Aadhar No: ' . $row['booking_aadharno'] . ')';
                            ?>

                        </div>

                    </div>
                <?php } elseif ($srno == 4) { ?>

                    <div style="position:relative; width:100%;">

                        <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                        <div style="
                            position:absolute;
                            bottom:781px;   /*  yaha adjust kar sakte ho */
                            left:497px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:15px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_plotsize'];
                            ?>

                        </div>
                        <div style="
                            position:absolute;
                            bottom:533px;   /*  yaha adjust kar sakte ho */
                            left:497px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_plotno'];
                            ?>

                        </div>

                    </div>

                <?php } elseif ($srno == 7) { ?>

                    <div style="position:relative; width:100%;">

                        <!-- Image -->
                        <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                        <!-- Name just above Allottee(s) -->

                        <div style="
                            position:absolute;
                            bottom:473px;   /*  yaha adjust kar sakte ho */
                            left:880px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_plotno'];
                            ?>

                        </div>
                        <div style="
                            position:absolute;
                            bottom:450px;   /*  yaha adjust kar sakte ho */
                            left:250px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:10px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_plotsize'];
                            ?>

                        </div>
                        <div style="
                            position:absolute;
                            bottom:370px;   /*  yaha adjust kar sakte ho */
                            left:750px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;
                        ">
                            <?php
                            echo $row['booking_totalamt'];
                            ?>

                        </div>

                    </div>





                <?php } elseif ($srno == 17) { ?>

                    <div style="position:relative; width:100%;">

                        <!-- Image -->
                        <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                        <!-- Name just above Allottee(s) -->



                        <div style="
                            position:absolute;
                            bottom:370px;   /*  yaha adjust kar sakte ho */
                            left:0px;
                            width:80%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;
                        ">

                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SNO</th>
                                            <th>Installment Date</th>
                                            <th>Particulars</th>
                                            <th>%</th>
                                            <th>Amount</th>
                                            <th>Remaining Amount</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sn = 1;
                                        $cumulative_amount = 0;
                                        $totalamt = $_GET['propertamt'];
                                        $installmentqry = "SELECT * FROM `installment_master` WHERE installment_bookingid='$booking_id' ORDER BY installment_date";
                                        $installmentres = mysqli_query($con, $installmentqry);
                                        if (!$installmentres) {
                                            error_log("Failed to fetch installments: " . mysqli_error($con));
                                        }
                                        while ($installmentdata = mysqli_fetch_assoc($installmentres)) {
                                            $cumulative_amount += $installmentdata['installment_amount'];
                                            $remaining_amount = $totalamt - $cumulative_amount;
                                            ?>
                                            <tr>
                                                <td><?php echo $sn; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($installmentdata['installment_date'])); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($installmentdata['installment_particular']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($installmentdata['installment_emiper']); ?></td>
                                                <td><?php echo number_format($installmentdata['installment_amount'], 2); ?></td>
                                                <td><?php echo number_format($remaining_amount, 2); ?></td>
                                                <!-- <td><?php echo number_format($remaining_amount, 2); ?></td> -->
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





                <?php } else { ?>

                    <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                <?php } ?>





            </div>
        <?php } ?>





















    </div>
</div>


<?php
include "layout/footer.php";
?>