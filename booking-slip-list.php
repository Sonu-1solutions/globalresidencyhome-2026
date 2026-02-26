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
              <h1>View Slip</h1>
            </div>
            <div class="col-md-2">
              <?php
              $alloted = $_GET['slip_id'];
              $bkingque = mysqli_query($con, "select booking_id from booking_master where booking_no='$alloted'");
              $bkingquer = mysqli_fetch_assoc($bkingque);
              $bkingqueres = $bkingquer['booking_id'];
              ?>
              <a href="booking-view.php?booking_id=<?php echo $bkingqueres; ?>" class="btn btn-sm btn-success">
                ← Back
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>


    <div class="card m-t-3">
      <div class="card-body">
        <?php
        $alloted = $_GET['slip_id'];
        // print_r($alloted);
        $sn = 1;
        $count = 1;
        $countbtn = 1;
        $slipqry = mysqli_query($con, "select * from payment_slip order by slip_id DESC");
        while ($slipdata = mysqli_fetch_assoc($slipqry)) {
          $bookingNo = $slipdata['registration_number'];
          if ($bookingNo == $alloted) {
            $currentDate = date('d-m-Y');
            ?>
            <div id="printableArea<?php echo $count++ ?>">
              <div class="payment-slip">

                <img src="booking-image/payment-slip.png" class="slip-img">

                <!-- Slip No -->
                <div class="slip-text" style="top:250px; left:130px; font-size:22px; font-weight: 700; ">
                  <?= $slipdata['slip_id']; ?>
                </div>

                <!-- Date -->
                <div class="slip-text" style="top:253px; right:120px;">
                  <?= $slipdata['current_date']; ?>
                </div>

                <!-- Received From -->
                <div class="slip-text" style="top:318px; left:380px; width:650px; font-weight: 600;">
                  <?= $slipdata['receive_name']; ?> (<?= $slipdata['registration_number']; ?>)
                </div>

                <!-- Amount In Words -->
                <div class="slip-text" style="top:378px; left:300px; width:750px;">
                  <?= $slipdata['amount_in_word']; ?>
                </div>

                <!-- Payment Mode -->
                <div class="slip-text" style="top:434px; right:20px; width:300px;">
                  <?= $slipdata['payment_by']; ?>
                </div>

                <!-- Drawn On -->
                <div class="slip-text" style="top:500px; left:230px; width:500px;">
                  <?= $slipdata['drawn_on']; ?>
                </div>

                <!-- Payment Date -->
                <div class="slip-text" style="top:500px; left:840px; width:300px;">
                  <?= $slipdata['payment_by_date']; ?>
                </div>

                <!-- Project Name -->
                <div class="slip-text" style="top:555px; left:260px; width:750px;">
                  <?= $slipdata['project_name']; ?>
                </div>

                <!-- Plot No -->
                <div class="slip-text" style="top:619px; left:220px; width:250px;">
                  <?= $slipdata['plot_no']; ?>
                </div>

                <!-- Plot Size -->
                <div class="slip-text" style="top:619px; right:120px; width:250px;">
                  <?= $slipdata['plot_size']; ?>
                </div>

                <!-- Amount -->
                <div class="slip-text" style="top:700px; left:120px; font-weight: 500; font-size:22px;">
                  <?= sprintf("%.2f", $slipdata['total_amout']); ?>
                </div>

              </div>
            </div>

            <button class="btn btn-danger mt-3 mb-3" onclick="downloadPDF('printableArea<?php echo $countbtn++ ?>')">
              Download PDF
            </button>
            <style>
              .payment-slip {
                position: relative;
                width: 1123px;
                /* A4 Landscape */
                height: 794px;
                margin: auto;
                /* font-family: Arial, sans-serif; */
              }

              .slip-img {
                width: 1123px;
                height: 794px;
              }

              .slip-text {
                position: absolute;
                font-size: 16px;
                font-weight: 500;
                color: #000;
                white-space: nowrap;
              }
            </style>



            <?php
            if ($sn == 1) {
              ?>
              <a href="booking-slip_edit.php?slip_id=<?= $slipdata['registration_number']; ?>&slipno=<?= $slipdata['slip_id']; ?>"
                class="btn btn-m btn-success action-btn mt-3 mb-3">
                Edit
              </a>
              <?php
            }
            ?>


            <?php
            $sn++;
          }
        }
        ?>
        <!--<button class="btn btn-primary mt-3" onclick="printDiv('printableArea')" style="font-size: 18px;padding:">Print Now</button>-->
      </div>
    </div>






    <!-- /Breadcrumb -->
  </div>
</div>

<!-- <script>
  function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
  }
</script> -->




<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
  async function downloadPDF(divId) {

    await document.fonts.ready;

    const element = document.getElementById(divId);

    const canvas = await html2canvas(element, {
      scale: 2,
      useCORS: true,
      allowTaint: true
    });

    const imgData = canvas.toDataURL("image/png");

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('landscape', 'mm', 'a4');

    const imgProps = pdf.getImageProperties(imgData);
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
    pdf.save("PaymentSlip.pdf");
  }
</script>

<?php
include "layout/footer.php";
?>