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
                        <div class="col-md-10"><h1>Add Slip</h1></div>
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


                            <!-- <div class="col-md-3"></div> -->
                            <div class="col-md-12 mt-3">
                                <div class="row">

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Booking ID *</label>
                                        <input class="form-control" value="<?= $productdata['booking_no'] ?>" type="text"
                                            name="registrationNumber" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Current Date *</label>
                                        <input class="form-control" value="<?= $currentDate ?>" type="text" name="currentDate"
                                            required>
                                    </div>


                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Received with thanks from *</label>
                                        <input class="form-control"
                                            value="<?= $productdata['booking_fname'] . ' ' . $productdata['booking_lname'] ?>"
                                            type="text" name="receiveName" required>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-12  mb-4">
                                            <label class="fw-bold">The sum of Rupees *</label>
                                            <input type="text" class="form-control" placeholder="Enter Amount In Words"
                                                name="sumAmount" required>
                                        </div>

                                    </div>


                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">By Online/Cheque/D.D.No. *</label>
                                        <input type="text" class="form-control" style="height:50px;" name="paymentby" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Drawn On *</label>
                                        <input type="text" class="form-control py-4" name="profile_image" accept="image/*">
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Cheque Date *</label>
                                        <input type="date" class="form-control" name="chequedate" style="height:50px;" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Project Name *</label>
                                        <input type="text" class="form-control" value="Global Residency Homes"
                                            name="projectname" style="height:50px;" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Plot No. *</label>
                                        <input type="text" style="height:50px;" class="form-control"
                                            value="<?= $productdata['booking_plotno']; ?>" name="plotno" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Plot Size *</label>
                                        <input type="text" style="height:50px;" class="form-control"
                                            value="<?= $productdata['booking_plotarea']; ?> Sqyds." name="plotsize" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Total Amount *</label>
                                        <?php
                                        $balanceamt = $_GET['balanceamt'];
                                        ?>
                                        <input type="hidden" name="beforepaybalance" value="<?php echo $balanceamt; ?>">
                                        <input type="number" style="height:50px;" class="form-control"
                                            value="<?php echo $balanceamt; ?>" max="<?= $balanceamt ?>" id="totalamount"
                                            name="totalamout" oninput="calculateAdvisorAmount()" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Advisor Percentage *</label>

                                        <input type="number" style="height:50px;" class="form-control" id="percentage"
                                            name="percentage" oninput="calculateAdvisorAmount()"
                                            value="<?= $productdata['percentage'] ?>" required>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <label class="fw-bold">Advisor Amount *</label>
                                        <input type="text" style="height:50px;" class="form-control" id="advisoramount"
                                            name="ammount" readonly required>

                                    </div>

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



<!-- JAVASCRIPT -->
<!-- <script>
    function calculateAdvisorAmount() {
        let total = parseFloat(document.getElementById('totalamount').value) || 0;
        let percentage = parseFloat(document.getElementById('percentage').value) || 0;

        let advisorAmount = (total * percentage) / 100;

        document.getElementById('advisoramount').value = advisorAmount.toFixed(2);
    }
</script> -->




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



<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>



<?php
include "layout/footer.php";
?>