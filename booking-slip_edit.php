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

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10">
                            <h1>Edit Slip</h1>
                        </div>
                        <div class="col-md-2">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- /Breadcrumb -->





        <?php


        if (isset($_POST['slipbtn'])) {

            // GET se aaya hua slip_id (primary key)
            echo $payslip_id = $_GET['slipno'];

            // Form data
            $receiveName = $_POST['receiveName'];
            $paymentby = $_POST['paymentby'];
            $drawnon = $_POST['drawnon'];
            $chequedate = $_POST['chequedate'];
            $plotno = $_POST['plotno'];
            $plotsize = $_POST['plotsize'];
            $totalamout = $_POST['totalamout'];

            $currentamt = $_POST['currentamt'];
            $remainamt = $_POST['remainamt'];

            $sumAmount = $_POST['sumAmount'];
            $slip_id = $_POST['registrationNumber'];

            $newremainbalance = $currentamt - $totalamout;
            $newremainamt = $remainamt + $newremainbalance;

            // Update query
            $updateQry = "
        UPDATE payment_slip SET
            receive_name      = '$receiveName',
            payment_by        = '$paymentby',
            drawn_on          = '$drawnon',
            payment_by_date   = '$chequedate',
            plot_no           = '$plotno',
            plot_size         = '$plotsize',
            total_amout       = '$totalamout',
            remaing_balance       = '$newremainamt',
            amount_in_word  = '$sumAmount'
        WHERE slip_id = '$payslip_id'
    ";

            if (mysqli_query($con, $updateQry)) {
                echo "<script>
                alert('Payment Slip Updated Successfully');
                window.location.href = 'booking-slip-list.php?slip_id={$slip_id}';
            </script>";
            } else {
                echo 'Error: ' . mysqli_error($con);
            }

        }
        ?>















        <div class="row">
            <form action="" method="POST">


                <?php
                $alloted = $_GET['slip_id'];

                $payslip_id = $_GET['slipno'];

                $sn = 1;






                $slipqry = mysqli_query($con, "SELECT * FROM `payment_slip` WHERE `slip_id`='$payslip_id'");
                $slipqryres = mysqli_fetch_assoc($slipqry) ?? [];







                $productqry = mysqli_query($con, "SELECT * FROM booking_master ORDER BY booking_no DESC");
                while ($productdata = mysqli_fetch_assoc($productqry)) {

                    $bookingNo = $productdata['booking_no'];
                    if ($bookingNo == $alloted) {
                        $currentDate = date('d-m-Y');
                        ?>

                        <div class="row">





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

                                // ⭐ yahin main fix
                                return ucwords($words);
                            }
                            ?>


                            <script>
                                function updateWords() {
                                    let val = document.getElementById("totalamount").value;
                                    document.getElementById("sumAmount").value = numToWords(val);
                                }

                                function numToWords(num) {
                                    num = parseFloat(num);
                                    if (isNaN(num)) return "";

                                    let ones = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine",
                                        "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen",
                                        "seventeen", "eighteen", "nineteen"];
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

                                    // ⭐ ucwords equivalent in JS
                                    function capitalizeWords(str) {
                                        return str.replace(/\b\w/g, char => char.toUpperCase());
                                    }

                                    let parts = num.toFixed(2).split(".");
                                    let integer = parseInt(parts[0]);
                                    let decimal = parseInt(parts[1]);

                                    let words = "";
                                    let lakh = Math.floor(integer / 100000);
                                    integer %= 100000;
                                    let thousand = Math.floor(integer / 1000);
                                    integer %= 1000;
                                    let hundreds = integer;

                                    if (lakh) words += convertTwoDigit(lakh) + " lakh ";
                                    if (thousand) words += convertTwoDigit(thousand) + " thousand ";
                                    if (hundreds) words += convertThreeDigit(hundreds);

                                    words = words.trim();

                                    if (decimal > 0)
                                        words += " " + convertTwoDigit(decimal) + " paisa";

                                    return capitalizeWords(words);
                                }
                            </script>
                            <!-- <div class="">
                                <strong>Total Amount:</strong>
                                <?php echo $productdata['booking_totalamt'] ?>
                            </div> -->



                            <!-- <div class="col-md-3"></div> -->
                            <div class="col-md-12 mt-3">
                                <div class="row">


                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Booking ID *</label>
                                            <input class="form-control" style="height:50px;"
                                                value="<?= $productdata['booking_no'] ?>" type="text" name="registrationNumber"
                                                required readonly>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Current Date *</label>
                                            <input class="form-control" style="height:50px;" value="<?= $currentDate ?>"
                                                type="text" name="currentDate" required>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Slip ID *</label>
                                            <input class="form-control" style="height:50px;" value="<?= $payslip_id ?>"
                                                type="text" name="currentDate" required readonly>
                                        </div>

                                    </div>



                                    <div class="row">
                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Received with thanks from *</label>
                                            <input class="form-control" style="height:50px;"
                                                value="<?= $slipqryres['receive_name'] ?>" type="text" name="receiveName"
                                                required>
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">By Online/Cheque/D.D.No. *</label>
                                            <input type="text" class="form-control" style="height:50px;" name="paymentby"
                                                value="<?= $slipqryres['payment_by']; ?>" required>
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Drawn On *</label>
                                            <input type="text" class="form-control py-4" style="height:50px;" name="drawnon"
                                                value="<?= $slipqryres['drawn_on']; ?>" accept="image/*">
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Cheque Date *</label>
                                            <input type="date" class="form-control" name="chequedate"
                                                value="<?= $slipqryres['payment_by_date']; ?>" style="height:50px;" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Project Name *</label>
                                            <input type="text" class="form-control" value="Global Residency Homes"
                                                name="projectname" style="height:50px;" required>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Plot No. *</label>
                                            <input type="text" style="height:50px;" class="form-control"
                                                value="<?= $slipqryres['plot_no']; ?>" name="plotno" required>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Plot Size *</label>
                                            <input type="text" style="height:50px;" class="form-control"
                                                value="<?= $slipqryres['plot_size']; ?> " name="plotsize" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Total Amount *</label>
                                            <input type="hidden" name="currentamt" value="<?= $slipqryres['total_amout']; ?>">
                                            <input type="hidden" name="remainamt"
                                                value="<?= $slipqryres['remaing_balance']; ?>">
                                            <input type="number" class="form-control" id="totalamount"
                                                value="<?= $slipqryres['total_amout']; ?>" style=" height:50px;"
                                                name="totalamout" oninput="updateWords()" required>
                                        </div>
                                        <div class="col-md-8  mb-4">
                                            <label class="fw-bold">The sum of Rupees *</label>
                                            <!-- <input type="text" class="form-control" placeholder="Enter Amount In Words"
                                                name="sumAmount" required> -->
                                            <input type="text" class="form-control" style="height:50px;"
                                                placeholder="Enter Amount In Words" id="sumAmount" name="sumAmount"
                                                value="<?php echo numberToWords($slipqryres['total_amout']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-4 mb-4" style=" display: none; ">
                                        <label class="fw-bold">Advisor Percentage *</label>

                                        <input type="number" style="height:50px;" class="form-control" id="percentage"
                                            name="percentage" oninput="calculateAdvisorAmount()"
                                            value="<?= $productdata['percentage'] ?>" required>
                                    </div>

                                    <div class="col-md-4 mb-4" style="display:none;">
                                        <label class="fw-bold">Advisor Amount *</label>
                                        <input type="text" style="height:50px;" class="form-control" id="advisoramount"
                                            name="ammount" readonly required>

                                    </div> -->

                                    <div class="col-md-12  mb-4">
                                        <button type="submit" name="slipbtn" class="btn btn-primary mt-4">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $sn++;
                    }
                }
                ?>

            </form>
        </div>

    </div>

</div>






<script>
    function calculateAdvisorAmount() {
        let total = parseFloat(document.getElementById('totalamount').value) || 0;
        let percentage = parseFloat(document.getElementById('percentage').value) || 0;

        let advisorAmount = (total * percentage) / 100;

        document.getElementById('advisoramount').value = advisorAmount.toFixed(2);
    }

    // PAGE LOAD HOTE HI AUTO CALCULATE
    window.onload = function () {
        calculateAdvisorAmount();
    };
</script>


<!-- 
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    } 
</script> -->



<?php
include "layout/footer.php";
?>