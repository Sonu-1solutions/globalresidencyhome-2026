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
                            <h1>Add Slip</h1>
                        </div>
                        <div class="col-md-2">
                            <?php
                            $alloted = $_GET['slip_id'];
                            $bkingque = mysqli_query($con, "select booking_id from booking_master where booking_no='$alloted'");
                            $bkingquer = mysqli_fetch_assoc($bkingque);
                            $bkingqueres = $bkingquer['booking_id'];
                            ?>
                            <a href="booking-view.php?booking_id=<?php echo $bkingqueres; ?>"
                                class="btn btn-sm btn-success">
                                ← Back
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
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

            // --- Helpers ---
            $convertTwoDigit = function ($n) use ($ones, $tens) {
                if ($n < 20)
                    return $ones[$n];
                return $tens[intval($n / 10)] . ($n % 10 ? " " . $ones[$n % 10] : "");
            };

            $convertThreeDigit = function ($n) use ($convertTwoDigit, $ones) {
                $word = "";
                if ($n > 99) {
                    $word .= $ones[intval($n / 100)] . " hundred ";
                    $n = $n % 100;
                }
                if ($n > 0)
                    $word .= $convertTwoDigit($n);
                return trim($word);
            };

            // Normalize number to 2 decimals (e.g., 123.40)
            $parts = explode(".", number_format((float) $num, 2, '.', ''));
            $integer = intval($parts[0]);
            $decimal = intval($parts[1]);

            // Special case: 0
            if ($integer === 0 && $decimal === 0) {
                return "Zero Only";
            }

            // Indian numbering system: lakh, thousand, hundreds
            $words = "";

            $lakh = intval($integer / 100000);
            $integer = $integer % 100000;

            $thousand = intval($integer / 1000);
            $integer = $integer % 1000;

            $hundreds = $integer;

            if ($lakh)
                $words .= $convertTwoDigit($lakh) . " lakh ";
            if ($thousand)
                $words .= $convertTwoDigit($thousand) . " thousand ";
            if ($hundreds)
                $words .= $convertThreeDigit($hundreds);

            $words = trim(preg_replace('/\s+/', ' ', $words)); // extra spaces hata de
        
            // Paisa / Paise
            if ($decimal > 0) {
                $paisaWord = $convertTwoDigit($decimal) . " " . ($decimal == 1 ? "paisa" : "paise");
                // Agar integer part 0 ho to sirf paisa part bolein, warna space se jod dein
                $words = ($words ? $words . " " . $paisaWord : $paisaWord);
            }

            // Suffix
            $words .= " only";

            // >>> Title Case <<<
            // Har word ka pehla letter capital:
            $words = ucwords(strtolower($words));

            return $words;
        }

        // Default balance amount
        $balanceamt = isset($_GET['balanceamt']) ? $_GET['balanceamt'] : '0';
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

                if (decimal > 0)
                    words += " " + convertTwoDigit(decimal) + " paisa";

                // 👉 Convert whole string to Title Case
                words = words.toLowerCase().replace(/\b\w/g, function (txt) {
                    return txt.toUpperCase();
                });

                return words + " Only";
            }
        </script>
















        <div class="row">
            <form action="booking-slip-db.php" method="POST">

                <?php
                $alloted = $_GET['slip_id'];
                $sn = 1;

                $productqry = mysqli_query($con, "SELECT * FROM booking_master ORDER BY booking_no DESC");
                while ($productdata = mysqli_fetch_assoc($productqry)) {

                    $bookingNo = $productdata['booking_no'];
                    if ($bookingNo == $alloted) {
                        $currentDate = date('d-m-Y');
                        ?>

                        <div class="row">






                            <!--  -->
                            <div class="row">

                                <?php

                                $totalAmount = $productdata['booking_totalamt'];
                                $amount30 = ($totalAmount * 30) / 100;
                                $amount40 = ($totalAmount * 40) / 100;


                                $totalAmountadvisor = $productdata['advisor_amount'];
                                $advisoramount70 = ($totalAmountadvisor * 70) / 100;
                                $advisoramount30 = ($totalAmountadvisor * 30) / 100;


                                $qry = "SELECT SUM(total_amout) AS total_received, SUM(advisor_amount) AS total_brokageamt FROM payment_slip WHERE registration_number = '$productdata[booking_no]'";

                                $res = mysqli_query($con, $qry);
                                $row = mysqli_fetch_assoc($res);

                                $totalreceiveamt = (float) ($row['total_received'] ?? 0);
                                $brokragetamotrec = (float) ($row['total_brokageamt'] ?? 0);

                                $advisorpendingamt = $totalAmountadvisor - $brokragetamotrec;



                                // echo "Total Advisor Amount : ".$totalAmountadvisor;
                                // echo "<br>";
                                // echo "Advisor Receive Amount : ".$brokragetamotrec;
                                // echo "<br>";
                                // echo "Advisor Pending Amount : ".$advisorpendingamt;
                                // echo "<br><br>";
                        


                                // echo "<br><br>";
                                // echo "Total Amount: ".$totalAmount;
                                // echo "<br>";
                                // echo " Balance Amount: ".$balanceamt;
                                // echo "<br>";
                                // echo " Total Receive Amount: ".$totalreceiveamt;
                                // echo "<br><br>";
                        


                                ?>



                                <!-- <div>
                            <strong>Total Amount:</strong>
                            <?php echo $totalAmount; ?>
                        </div>
                        <br>
                        <div>
                            <strong>30% Amount:</strong>
                            <?php echo $amount30; ?>
                        </div>

                        <div>
                            <strong>40% Amount:</strong>
                            <?php echo $amount40; ?>
                        </div>

                        <br><br> -->

                                <!--                         
                        <div>
                            <strong>Advisor Total Amount:</strong>
                            <?php echo $totalAmountadvisor; ?>
                        </div>
                        <br>
                        <div>
                            <strong>Advisor 70% Amount:</strong>
                            <?php echo $advisoramount70; ?>
                        </div>

                        <div>
                            <strong>Advisor 30% Amount:</strong>
                            <?php echo $advisoramount30; ?>
                        </div>

                        <br><br> -->




                            </div>
                            <!--  -->









                            <style>
                                .labelsec {
                                    display: flex;
                                    align-content: center;
                                    align-items: center;
                                    /* justify-content: flex-end; */
                                }

                                .msgonly {
                                    background: #90ee9096;
                                    border-radius: 50px;
                                }

                                .form-control {
                                    height: 50px;
                                }
                            </style>



                            <!-- <div class="col-md-3"></div> -->
                            <div class="col-md-12 mt-3">
                                <div class="row">

                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <div class="row">
                                                <div class="col-md-4 labelsec">
                                                    <label class="fw-bold">Booking ID *</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input class="form-control" value="<?= $productdata['booking_no'] ?>"
                                                        type="text" name="registrationNumber" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4"></div>

                                        <div class="col-md-4 mb-4">
                                            <div class="row">
                                                <div class="col-md-4 labelsec">
                                                    <label class="fw-bold">Current Date *</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input class="form-control" value="<?= $currentDate ?>" type="text"
                                                        name="currentDate" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Received with thanks from *</label>
                                            <input class="form-control"
                                                value="<?= $productdata['booking_fname'] . ' ' . $productdata['booking_lname'] ?>"
                                                type="text" name="receiveName" required>
                                        </div>


                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">By Online/Cheque/D.D.No. *</label>
                                            <input type="text" class="form-control" name="paymentby" required>
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Drawn On *</label>
                                            <input type="text" class="form-control" name="drawnon" accept="image/*">
                                        </div>

                                        <div class="col-md-3 mb-4">
                                            <label class="fw-bold">Cheque Date *</label>
                                            <input type="date" class="form-control" name="chequedate" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Project Name *</label>
                                            <input type="text" class="form-control" value="Global Residency Homes"
                                                name="projectname" required>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Plot No. *</label>
                                            <input type="text" class="form-control"
                                                value="<?= $productdata['booking_plotno']; ?>" name="plotno" required>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Plot Size *</label>
                                            <input type="text" class="form-control"
                                                value="<?= $productdata['booking_plotarea']; ?> Sqyds." name="plotsize"
                                                required>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-4 mb-4">
                                            <label class="fw-bold">Total Amount *</label>
                                            <?php
                                            $balanceamt = $_GET['balanceamt'];
                                            ?>
                                            <input type="hidden" name="beforepaybalance" value="<?php echo $balanceamt; ?>">
                                            <input type="number" class="form-control" id="totalamount"
                                                value="<?php echo $balanceamt; ?>" max="<?= $balanceamt ?>" id="totalamount"
                                                name="totalamout" oninput="updateWords(); checkAdvisorSection()" required>
                                        </div>


                                        <div class="col-md-8 mb-4">
                                            <label class="fw-bold">The sum of Rupees *</label>
                                            <input type="text" class="form-control" placeholder="Enter Amount In Words"
                                                id="sumAmount" name="sumAmount"
                                                value="<?php echo numberToWords($balanceamt); ?>" required>
                                        </div>

                                    </div>



                                    <div class="row">
                                        <!-- <label class="fw-bold">Advisor Percentage *</label> -->
                                        <input type="hidden" class="form-control" id="advisorpercentage" name="percentage"
                                            value="">
                                        <!-- <label class="fw-bold">Advisor Amount *</label> -->
                                        <input type="hidden" class="form-control" id="advisoramountifany" name="ammount"
                                            value="">
                                    </div>



                                    <!-- Advisor Section -->
                                    <div class="row">

                                        <!-- Below 30% -->
                                        <div class="below30percenttotalamt mb-4" style="display:none;">
                                            <div class="row" style="background:#ebebeb;">
                                                <h4 class="text-center mt-4 mb-4">Below 30% of Total Amount</h4>
                                            </div>
                                        </div>
                                        <!-- Below 30% End -->
                                        <!-- Between 30% and 40% -->
                                        <div class="between30and40percenttotalamt mb-4" style="display:none;">
                                            <?php
                                            if ($brokragetamotrec < $advisoramount70) {
                                                ?>
                                                <div class="row" style="background:#ebebeb;">
                                                    <h4 class="text-center mt-4 mb-4">Between 30% and 40% of Total Amount</h4>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Total Amount</label>
                                                        <input type="number" class="form-control" id="" name=""
                                                            value="<?= $totalAmountadvisor; ?>" readonly>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Pending Amount</label>
                                                        <input type="number" class="form-control" id="advisortotalamount70"
                                                            name="advisortotalamount70" value="<?= $advisorpendingamt; ?>" readonly>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Percentage *</label>
                                                        <input type="number" class="form-control" id="percentage70"
                                                            name="percentage70" oninput="calculateAdvisorAmount70()" value="70"
                                                            required>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Amount *</label>
                                                        <input type="text" class="form-control" id="advisoramount70"
                                                            name="advisoramount70" readonly required>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="row" style="background:#ebebeb;">
                                                    <div class="col-md-12 msgonly">
                                                        <h4 class="text-center mt-4 mb-4">Advisor 70% of Total Amount Already
                                                            Received</h4>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!-- Between 30% and 40% End -->
                                        <!-- Above 40% -->
                                        <div class="above40percenttotalamt mb-4" style="display:none;">
                                            <?php
                                            if ($brokragetamotrec < $totalAmountadvisor) {
                                                ?>
                                                <div class="row" style="background:#ebebeb;">

                                                    <h4 class="text-center mt-4 mb-4">Above 40% of Total Amount</h4>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Total Amount</label>
                                                        <input type="number" class="form-control" value="<?= $totalAmountadvisor ?>"
                                                            readonly>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Pending Amount</label>
                                                        <input type="number" class="form-control" id="advisortotalamount30"
                                                            name="advisortotalamount30" value="<?= $advisorpendingamt; ?>" readonly>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Percentage *</label>
                                                        <input type="number" class="form-control" id="percentage30"
                                                            name="percentage30" oninput="calculateAdvisorAmount30()" value="100"
                                                            readonly required>
                                                    </div>

                                                    <div class="col-md-3 mb-4">
                                                        <label class="fw-bold">Advisor Amount *</label>
                                                        <input type="text" class="form-control" id="advisoramount30"
                                                            name="advisoramount30" readonly required>
                                                    </div>

                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="row" style="">
                                                    <div class="col-md-12 msgonly">
                                                        <h4 class="text-center mt-4 mb-4">Advisor 100% of Total Amount Already
                                                            Received</h4>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!-- Above 40% End -->
                                        <!-- Advisor Total Amount Release -->
                                        <div class="row advisortotalamountrelease mb-4" style="display:none;">
                                            <div class="row" style="background:#ebebeb;">
                                                <h4 class="text-center mt-4 mb-4">Advisor Total Amount Release</h4>
                                            </div>
                                        </div>
                                        <!-- Advisor Total Amount Release End -->

                                    </div>
                                    <!-- Advisor -->


                                    <div class="col-md-12  mb-4">
                                        <button type="submit" name="slipbtn" class="btn btn-primary mt-4">
                                            Submit
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
    function calculateAdvisorAmount70() {
        let total = parseFloat(document.getElementById('advisortotalamount70').value) || 0;
        let percentage = parseFloat(document.getElementById('percentage70').value) || 0;

        let advisorAmount = (total * percentage) / 100;

        document.getElementById('advisoramount70').value = advisorAmount.toFixed(2);
        document.getElementById('advisoramountifany').value = advisorAmount.toFixed(2);
        document.getElementById('advisorpercentage').value = percentage;
    }


    function calculateAdvisorAmount30() {

        let totalField = document.getElementById('advisortotalamount30');
        let percentageField = document.getElementById('percentage30');
        let amountField = document.getElementById('advisoramount30');
        let advisoramountifany = document.getElementById('advisoramountifany');
        let advisorpercentage = document.getElementById('advisorpercentage');

        if (!totalField || !percentageField || !amountField) {
            return; // section exist nahi karta
        }

        let total1 = parseFloat(totalField.value) || 0;
        let percentage1 = parseFloat(percentageField.value) || 0;

        let advisorAmount1 = (total1 * percentage1) / 100;

        amountField.value = advisorAmount1.toFixed(2);
        advisoramountifany.value = advisorAmount1.toFixed(2);
        advisorpercentage.value = percentage1;
    }






    function checkAdvisorSection() {

        // totalAmount = 2650000.00;
        // totalReceiveAmount = 855000;
        // receiveAmount = 1795000;


        let totalAmount = parseFloat(<?= $totalAmount ?>) || 0;
        let totalReceiveAmount = parseFloat(<?= $totalreceiveamt ?>) || 0;


        let amount30 = (totalAmount * 30) / 100;
        let amount40 = (totalAmount * 40) / 100;

        let receiveAmount = parseFloat(document.getElementById('totalamount').value) || 0;

        let updatedTotalReceiveAmount = totalReceiveAmount + receiveAmount;


        if (<?= $brokragetamotrec ?> == <?= $advisoramount70 ?>) {
            document.getElementById('advisoramountifany').value = 0;
            document.getElementById('advisorpercentage').value = 0;
        }

        // alert(totalAmount);
        // alert(totalReceiveAmount);
        // alert(receiveAmount);
        // alert(updatedTotalReceiveAmount);



        let below30 = document.querySelector('.below30percenttotalamt');
        let between30and40 = document.querySelector('.between30and40percenttotalamt');
        let above40 = document.querySelector('.above40percenttotalamt');
        let advisorRelease = document.querySelector('.advisortotalamountrelease');

        // sab hide karo pehle
        below30.style.display = 'none';
        between30and40.style.display = 'none';
        above40.style.display = 'none';

        if (updatedTotalReceiveAmount < amount30) {
            below30.style.display = 'block';
            document.getElementById('advisoramountifany').value = 0;
            document.getElementById('advisorpercentage').value = 0;
        } else if (updatedTotalReceiveAmount >= amount30 && updatedTotalReceiveAmount < amount40) {
            between30and40.style.display = 'block';
            calculateAdvisorAmount70();
        } else if (updatedTotalReceiveAmount >= amount40) {
            above40.style.display = 'block';
            calculateAdvisorAmount30();
        }

        // Advisor release condition
        let brokerageReceived = <?= $brokragetamotrec ?>;
        let advisorTotal = <?= $totalAmountadvisor ?>;

        if (brokerageReceived >= advisorTotal) {
            document.getElementById('advisoramountifany').value = 0;
            document.getElementById('advisorpercentage').value = 0;
            advisorRelease.style.display = 'none';
        }
    }





    // PAGE LOAD HOTE HI AUTO CALCULATE

    window.onload = function () {
        if (document.getElementById('advisortotalamount70')) {
            calculateAdvisorAmount70();
        }

        if (document.getElementById('advisortotalamount30')) {
            calculateAdvisorAmount30();
        }

        checkAdvisorSection();

    };
</script>



<!-- <script>
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