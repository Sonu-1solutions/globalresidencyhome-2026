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
            <div class="col-md-10"><h1>View Slip</h1></div>
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
        $slipqry = mysqli_query($con, "select * from payment_slip order by registration_number DESC");
        while ($slipdata = mysqli_fetch_assoc($slipqry)) {
          $bookingNo = $slipdata['registration_number'];
          if ($bookingNo == $alloted) {
            $currentDate = date('d-m-Y');
            ?>
            <div id="printableArea<?php echo $count++ ?>">
              <div class="payment-slip" style="position: relative; width: 100%; margin: 0 auto;display: inline-block;">
                <img class="slip-img" src="https://globalresidencyhome.com/erp/booking-image/payment-slip.png">
               

                <input
                  style="position: absolute;top: 31%;left: 8%;font-size: 25px;font-weight: 500;font-family: fantasy;letter-spacing: 3px;text-align: center;border: none;background: transparent;padding: 0;"
                  type="text" value="<?= $slipdata['slip_id']; ?>" maxlength="4">

                <p style="position: absolute;top: 32%;right: 8%;font-size: 16px;font-weight: 500;width: 150px;color: #000;">
                  <?= $slipdata['current_date']; ?>
                </p>

                <p
                  style="position: absolute;top: 40%;left: 34%;font-size: 16px;font-weight: 500;width: 55%;color: #000;text-transform: capitalize;">
                  <?= $slipdata['receive_name']; ?> ( <?= $slipdata['registration_number']; ?>)
                </p>

                <input
                  style="position: absolute;top: 47.5%;left: 26%;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 71%;"
                  type="text" value="<?= $slipdata['amount_in_word']; ?>">

                <input
                  style="position: absolute;top: 54%;right: 30px;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 26%;"
                  type="text" value="<?= $slipdata['payment_by']; ?>">

                <input
                  style="position: absolute;bottom: 34.8%;left: 19%;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 49%;"
                  type="text" value="<?= $slipdata['drawn_on']; ?>">

                <input
                  style="position: absolute;bottom: 34.8%;right: 30px;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 22%;"
                  type="text" value="<?= $slipdata['payment_by_date']; ?>">



                <p
                  style="position: absolute;bottom: 26%;left: 21%;font-size: 16px;font-weight: 500;width: 76%;color: #000;">
                  <?php echo $slipdata['project_name']; ?>
                </p>

                <input
                  style="position: absolute;bottom: 19.8%;left: 16%;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 30%;"
                  type="text" value="<?php echo $slipdata['plot_no']; ?>">

                <input
                  style="position: absolute;bottom: 19.8%;right: 7%;font-size: 16px;font-weight: 500;border: none;background: transparent;padding: 0;width: 30%;"
                  type="text" value="<?php echo $slipdata['plot_size']; ?>">


                <input
                  style="position: absolute;bottom: 8.5%;left: 10%;font-size: 20px;font-weight: 600;border: none;background: transparent;padding: 0;width: 15%;font-family: cursive;"
                  type="text" value="<?php echo $slipdata['total_amout']; ?>">

              </div>
            </div>
            <button class="btn btn-primary my-3" onclick="printDiv('printableArea<?php echo $countbtn++ ?>')"
              style="font-size: 18px;padding:">Print Now</button>
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