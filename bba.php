<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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


        <!-- BBA -->

        <?php

        $booking_no = $_GET['booking_no'];
        $booking_id = $_GET['booking_id'];


        if (!$booking_no) {
            echo '<script> window.location="booking-list.php"; </script>';
            exit;
        }

        if (!$booking_id) {
            echo '<script>window.location="booking-list.php";</script>';
            exit;
        }


        $query = "SELECT * FROM bba WHERE booking_no='$booking_no'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);





if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('BBA not generated')</script>";
    echo "<script> window.location='booking-view.php?booking_id=$booking_id'; </script>";
    exit;
}



        ?>


        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <div class="row">
                    <div class="col-md-10">
                        <h1>BBA</h1>
                    </div>
                    <div class="col-md-2">

                        <a href="booking-view.php?booking_id=<?= $booking_id ?>" class="btn btn-sm btn-success">
                            ← Back
                        </a>
                    </div>
                </div>

            </div>
        </div>





        <!-- /BBA -->



        <style>
            table.table.dataTable>tbody>tr td,
            table.table.dataTable>thead>tr th {
                font-size: 13px;
                padding: 5px 10px;
            }
        </style>



        <button onclick="printDiv()" style="
            padding:10px 20px;
            background:#007bff;
            color:#fff;
            border:none;
            cursor:pointer;
            margin-bottom:20px;
">
             Print BBA Document
        </button>






        <!-- PRINT AREA START -->
        <div id="printArea">



            <?php
            $images = glob("assets/bba/*.jpg");

            $srno = 0;
            foreach ($images as $img) {
                $srno++;
                ?>
                <div class="print-page" style="margin-bottom:30px; text-align:center;">


                    <!--  Sirf pehli image par name -->
                    <?php if ($srno == 2) { ?>
                        <div style="position:relative; width:100%;">

                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            <div class="print-box" style="
                                position:absolute;
                                bottom:50.5%;   /*  yaha adjust kar sakte ho */
                                left:7%;
                                width:100%;
                                text-align:left;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['allottee_name']; ?>
                            </div>

                        </div>

                    <?php } elseif ($srno == 3) { ?>
                        <div style="position:relative; width:100%;">

                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            <div style="
                            position:absolute;
                            bottom:46.60%;   /*  yaha adjust kar sakte ho */
                            left:200px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;   ">

                                <?php
                                echo $row['allottee_name'] . ' (Aadhar No: ' . $row['addhar_no'] . ')';
                                ?>

                            </div>

                            <div style="
                            position:absolute;
                            bottom:45%;   /*  yaha adjust kar sakte ho */
                            left:25%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;     ">

                                <?php
                                echo $row['allottee_fname'];
                                ?>

                            </div>

                            <div style="
                            position:absolute;
                            bottom:41.4%;   /*  yaha adjust kar sakte ho */
                            left:11%;
                            width:78%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;     ">

                                <?php
                                echo $row['allottee_address'];
                                ?>

                            </div>

                        </div>
                    <?php } elseif ($srno == 4) { ?>

                        <div style="position:relative; width:100%;">

                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            <div style="
                            position:absolute;
                            bottom:22.3% ;   /*  yaha adjust kar sakte ho */
                            left:50%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black;   ">

                                <?php
                                echo $row['booking_date'];
                                ?>

                            </div>

                        </div>

                    <?php } elseif ($srno == 5) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <!-- Name just above Allottee(s) -->

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>



                            <div style="
                            position:absolute;
                            bottom:84%;   /*  yaha adjust kar sakte ho */
                            left:42%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['plot_area'];
                                ?>

                            </div>

                            <div style="
                            position:absolute;
                            bottom:82%;   /*  yaha adjust kar sakte ho */
                            left:30%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['booking_date'];

                                ?>


                            </div>

                            <div style="
                            position:absolute;
                            bottom: 68.5%; /*  yaha adjust kar sakte ho */
                            left:43%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['plot_no'];

                                ?>


                            </div>

                            <div style="
                            position:absolute;
                            bottom: 66.8%; /*  yaha adjust kar sakte ho */
                            left:9%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['booking_date'];

                                ?>


                            </div>

                        </div>

                    <?php } elseif ($srno == 6) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>

                    <?php } elseif ($srno == 7) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>

                    <?php } elseif ($srno == 8) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            <div style="
                            position:absolute;
                            bottom: 63.6%; /*  yaha adjust kar sakte ho */
                            left: 12%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['plot_no'];

                                ?>


                            </div>

                            <div style="
                            position:absolute;
                            bottom:63.6%;   /*  yaha adjust kar sakte ho */
                            left:38%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['plot_area'];
                                ?>

                            </div>

                            <div style="
                            position:absolute;
                            bottom:63.6%;   /*  yaha adjust kar sakte ho */
                            left:73%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            color:black; ">

                                <?php
                                echo $row['khasra_no'];
                                ?>

                            </div>

                            <div style="
                            position:absolute;
                            bottom: 58.5%;   /*  yaha adjust kar sakte ho */
                            left:50%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;  ">

                                <span style="font-size:20px;">
                                    <?php echo $row['total_amount']; ?>
                                </span>

                            </div>

                            <div style="
                            position:absolute;
                            bottom:57%;   /*  yaha adjust kar sakte ho */
                            left:10%;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;  ">

                                <span id="amountWords" class="" style="font-size:20px;">
                                    <?= $row['total_amount_word'] ?>
                                </span>

                            </div>

                        </div>
                    <?php } elseif ($srno == 9) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                        </div>
                    <?php } elseif ($srno == 10) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>
                    <?php } elseif ($srno == 11) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>
                    <?php } elseif ($srno == 12) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>
                    <?php } elseif ($srno == 13) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>
                    <?php } elseif ($srno == 14) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>
                    <?php } elseif ($srno == 15) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            <div style="
                            position:absolute;
                            bottom:65%;   /*  yaha adjust kar sakte ho */
                            left:11%;
                            width:78%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font-size:20px;
                            text-decoration:underline;
                            color:black;     ">

                                <?php
                                echo $row['allottee_address'];
                                ?>

                            </div>

                        </div>

                         <?php } elseif ($srno == 16) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>


                        </div>

                        



                    <?php } elseif ($srno == 17) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                           <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            

                        </div>


                    <?php } elseif ($srno == 18) { ?>

                        <div style="position:relative; width:100%;">

                            <!-- Image -->
                            <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                            <!-- Name just above Allottee(s) -->

                            <div class="print-box" style="
                                position:absolute;
                                bottom:95%;   /*  yaha adjust kar sakte ho */
                                /* left:95px; */
                                width:100%;
                                text-align:center;
                                padding-left:60px;
                                font-weight:bold;
                                font-size:20px;
                                color:black;   ">

                                <?php echo $row['estamp_no']; ?>
                            </div>

                            


                            <div style="
                            position:absolute;
                            bottom:75%;   /*  yaha adjust kar sakte ho */
                            left:0px;
                            width:88%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;  ">


                                <style>
                                    /* Table overall */
                                    #example1 {
                                        width: 100%;
                                        border-collapse: collapse;
                                        table-layout: fixed;
                                        /* important for wrapping */
                                    }

                                    /* Table headers & cells */
                                    #example1 th,
                                    #example1 td {
                                        border: 1px solid #ccc;
                                        padding: 8px;
                                        font-size: 13px;
                                        /* text-align: left; */
                                        vertical-align: top;

                                        white-space: normal;
                                        word-wrap: break-word;
                                        word-break: break-word;
                                    }

                                    /* Optional: header styling */
                                    #example1 th {
                                        background: #f5f5f5;
                                        font-weight: 600;
                                    }
                                </style>

                                <table id="example1" class="table table-bordered table-striped">
                                    <tr>
                                        <th style="width:70px;">Booking Date</th>
                                        <th style="width:90px;">Client Name</th>
                                        <th style="width:80px;">Allotted Unit</th>
                                        <th style="width:80px;">Area (Sq. Yds.)</th>
                                        <th style="width:90px;">Payment Plan</th>
                                        <th style="width:110px;">Basic Sales Price (Per Sq. Yard)</th>
                                        <th style="width:60px;">PLC</th>
                                        <th style="width:60px;">IDC</th>
                                        <th style="width:90px;">Total Cost</th>
                                    </tr>

                                    <?php

                                    $query = "SELECT * FROM booking_master WHERE booking_id='$booking_id'";
                                    $result = mysqli_query($con, $query);
                                    $row = mysqli_fetch_assoc($result);
                                    ?>

                                    <tr>
                                        <td><?php echo $row['booking_date']; ?></td>
                                        <td><?php echo $row['booking_fname'] . ' ' . $row['booking_lname']; ?></td>
                                        <td><?php echo $row['booking_plotno']; ?></td>
                                        <td><?php echo $row['booking_plotarea']; ?></td>
                                        <td><?php echo $row['booking_payplan']; ?></td>
                                        <td><?php echo $row['booking_plotrate']; ?></td>
                                        <td><?php echo $row['booking_plc']; ?></td>
                                        <td><?php echo $row['booking_idc']; ?></td>
                                        <td><?php echo $row['booking_totalamt']; ?></td>
                                    </tr>
                                </table>


                            </div>


                            <div style="
                            position:absolute;
                            bottom:15%;   /*  yaha adjust kar sakte ho */
                            left:0px;
                            width:88%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black; ">


                                <style>
                                    /* Table overall */
                                    #example1 {
                                        width: 100%;
                                        border-collapse: collapse;
                                        table-layout: fixed;
                                        /* important for wrapping */
                                    }

                                    /* Table headers & cells */
                                    #example1 th,
                                    #example1 td {
                                        border: 1px solid #ccc;
                                        padding: 8px;
                                        font-size: 13px;
                                        /* text-align: left; */
                                        vertical-align: top;

                                        white-space: normal;
                                        word-wrap: break-word;
                                        word-break: break-word;
                                    }

                                    /* Optional: header styling */
                                    #example1 th {
                                        background: #f5f5f5;
                                        font-weight: 600;
                                    }
                                </style>

                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style=" text-align:center; ">SNO</th>
                                                <th style=" text-align:center; ">Installment Date</th>
                                                <th style=" text-align:center; ">Particulars</th>
                                                <th style=" text-align:center; ">%</th>
                                                <th style=" text-align:center; ">Amount</th>
                                                <th style=" text-align:center; ">Remaining Amount</th>

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
                                                    <td style=" text-align:center; "><?php echo $sn; ?></td>
                                                    <td style=" text-align:center; ">
                                                        <?php echo date('d/m/Y', strtotime($installmentdata['installment_date'])); ?>
                                                    </td>
                                                    <td style=" text-align:center; ">
                                                        <?php echo htmlspecialchars($installmentdata['installment_particular']); ?>
                                                    </td>
                                                    <td style=" text-align:center; ">
                                                        <?php echo htmlspecialchars($installmentdata['installment_emiper']); ?>
                                                    </td>
                                                    <td style=" text-align:center; ">
                                                        <?php echo number_format($installmentdata['installment_amount'], 2); ?>
                                                    </td>
                                                    <td style=" text-align:center; ">
                                                        <?php echo number_format($remaining_amount, 2); ?>
                                                    </td>
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

    <script>
        function printDiv() {
            var printContents = document.getElementById("printArea").innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = `
        <html>
        <head>
            <title>Print</title>
            <style>
                img { width:100%; }
                table { width:100%; border-collapse:collapse; }
                table, th, td { border:1px solid #000; }
                th, td { padding:6px; font-size:12px; }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `;

            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>




    <?php
    include "layout/footer.php";
    ?>