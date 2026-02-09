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

        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <div class="row">
                        <div class="col-md-10">
                            <h1>BBA</h1>
                        </div>
                        <div class="col-md-2">
                            
                            <a href="booking-view.php?booking_id=<?php echo $row['booking_id'] ?>"
                                class="btn btn-sm btn-success">
                                ← Back
                            </a>
                        </div>
                    </div>
              
            </div>
        </div>



        <table border="1" cellpadding="5" cellspacing="0" style="display: none ;">
            <tr>
                <th>Booking Date</th>
                <th>Name</th>
                <th>Addhar</th>
                <th>Plot-Size</th>
                <th>Plot No</th>
                <th>Amount</th>

                <!-- apne table ke hisaab se columns add karo -->
            </tr>


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
        // PHP function for number to words
        function numberToWords($num)
        {
            $ones = array(
                "",
                "one",
                "two",
                "three",
                "four",
                "five",
                "six",
                "seven",
                "eight",
                "nine",
                "ten",
                "eleven",
                "twelve",
                "thirteen",
                "fourteen",
                "fifteen",
                "sixteen",
                "seventeen",
                "eighteen",
                "nineteen"
            );
            $tens = array(
                "",
                "",
                "twenty",
                "thirty",
                "forty",
                "fifty",
                "sixty",
                "seventy",
                "eighty",
                "ninety"
            );

            function convertTwoDigit($n, $ones, $tens)
            {
                if ($n < 20)
                    return $ones[$n];
                else
                    return $tens[intval($n / 10)] . ($n % 10 ? " " . $ones[$n % 10] : "");
            }

            function convertThreeDigit($n, $ones, $tens)
            {
                $word = "";
                if ($n > 99) {
                    $word .= $ones[intval($n / 100)] . " hundred ";
                    $n = $n % 100;
                }
                if ($n > 0)
                    $word .= convertTwoDigit($n, $ones, $tens);
                return trim($word);
            }

            $parts = explode(".", number_format($num, 2, '.', ''));
            $integer = intval($parts[0]);
            $decimal = intval($parts[1]);

            $words = "";

            $lakh = intval($integer / 100000);
            $integer = $integer % 100000;
            $thousand = intval($integer / 1000);
            $integer = $integer % 1000;
            $hundreds = $integer;

            if ($lakh)
                $words .= convertTwoDigit($lakh, $ones, $tens) . " lakh ";
            if ($thousand)
                $words .= convertTwoDigit($thousand, $ones, $tens) . " thousand ";
            if ($hundreds)
                $words .= convertThreeDigit($hundreds, $ones, $tens);

            $words = trim($words);

            if ($decimal > 0) {
                $words .= " " . convertTwoDigit($decimal, $ones, $tens) . " paisa";
            }

            return ucfirst($words);
        }

        // Default balance amount
        $balanceamt = isset($_GET['balanceamt']) ? $_GET['balanceamt'] : 369837.10;
        ?>

        <style>
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .text-primary {
            color: #007bff;
        }











        @media print {

            body {
                margin: 0;
                padding: 0;
            }

            img {
                width: 100%;
                height: auto;
                display: block;
            }

            /* 🔒 Absolute text lock */
            .print-box {
                position: absolute !important;
                transform: translateZ(0);
            }

            /* Page scaling fix */
            @page {
                size: A4;
                margin: 0;
            }
        }


        .print-box {
            bottom: 85mm;
            left: 25mm;
        }
        </style>


        <input type="hidden" class="form-control" value="<?php echo $row['booking_totalamt']; ?>" id="totalamount"
            oninput="updateWords()">

        <!-- Default words from PHP -->


        <script>
        // JS function to convert number to words (simplified same logic)
        function updateWords() {
            let val = document.getElementById("totalamount").value;
            document.getElementById("amountWords").innerText = numToWords(val);
        }

        function numToWords(num) {
            num = parseFloat(num);
            if (isNaN(num)) return "";

            let ones = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine",
                "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen",
                "seventeen", "eighteen", "nineteen"
            ];
            let tens = ["", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];

            function convertTwoDigit(n) {
                if (n < 20) return ones[n];
                return tens[Math.floor(n / 10)] + (n % 10 ? " " + ones[n % 10] : "");
            }

            function convertThreeDigit(n) {
                let word = "";
                if (n > 99) {
                    word += ones[Math.floor(n / 100)] + " hundred ";
                    n = n % 100;
                }
                if (n > 0) word += convertTwoDigit(n);
                return word.trim();
            }

            let parts = num.toFixed(2).split(".");
            let integer = parseInt(parts[0]);
            let decimal = parseInt(parts[1]);

            let words = "";
            let lakh = Math.floor(integer / 100000);
            integer = integer % 100000;
            let thousand = Math.floor(integer / 1000);
            integer = integer % 1000;
            let hundreds = integer;

            if (lakh) words += convertTwoDigit(lakh) + " lakh ";
            if (thousand) words += convertTwoDigit(thousand) + " thousand ";
            if (hundreds) words += convertThreeDigit(hundreds);

            words = words.trim();
            if (decimal > 0) words += " " + convertTwoDigit(decimal) + " paisa";

            return words.charAt(0).toUpperCase() + words.slice(1);
        }
        </script>







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
            🖨 Print BBA Document
        </button>



        <!-- PRINT AREA START -->
        <div id="printArea">




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

                    <div class="print-box" style="
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
                            font-size:20px;
                            color:black;
                        ">
                        <?php
                                echo $row['booking_plotarea'];
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
                            font-size:20px;
                            color:black;
                        ">
                        <?php
                                echo $row['booking_plotarea'];
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

                        <span style="font-size:20px;">
                            <?php echo $row['booking_totalamt']; ?>
                        </span>

                    </div>
                    <div style="
                            position:absolute;
                            bottom:340px;   /*  yaha adjust kar sakte ho */
                            left:125px;
                            width:100%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;
                        ">
                        <span id="amountWords" class="" style="font-size:20px;">
                            <?php echo numberToWords($row['booking_totalamt']); ?>
                        </span>

                    </div>

                </div>





                <?php } elseif ($srno == 17) { ?>

                <div style="position:relative; width:100%;">

                    <!-- Image -->
                    <img src="<?php echo $img; ?>" style="width:100%; display:block;">

                    <!-- Name just above Allottee(s) -->



                    <div style="
                            position:absolute;
                            bottom:800px;   /*  yaha adjust kar sakte ho */
                            left:0px;
                            width:88%;
                            text-align:left;
                            padding-left:60px;
                            font-weight:bold;
                            font: size 20px;
                            color:black;
                        ">


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
                                    $booking_id = $_GET['booking_id'] ?? '';

                                    if (!$booking_id) {
                                        echo '<script>window.location="booking-list.php";</script>';
                                        exit;
                                    }

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
                            bottom:-210px;   /*  yaha adjust kar sakte ho */
                            left:0px;
                            width:88%;
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