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

//  TOTAL USERS COUNT 
$getuserqry = "
    SELECT COUNT(user_id) AS total_users 
    FROM user_master 
    WHERE user_status='Enable'
	AND user_department='User' 
    AND user_id!='1'
";
$getuserres = mysqli_query($con, $getuserqry);
$getuserrow = mysqli_fetch_assoc($getuserres);

$total_users = $getuserrow['total_users'] ?? 0;




// TOTAL ADMIN
$getAdminQry = "
    SELECT COUNT(user_id) AS total_admin
    FROM user_master
    WHERE user_status = 'Enable'
    AND user_id != 1
    AND LOWER(TRIM(user_department)) = 'admin'
";
$getAdminRes = mysqli_query($con, $getAdminQry);
$getAdminRow = mysqli_fetch_assoc($getAdminRes);
$total_admin = (int) ($getAdminRow['total_admin'] ?? 0);






/* TOTAL ONLINE BOOKING  */
$getOnlineBookingQry = "
    SELECT COUNT(booking_id) AS total_online 
    FROM booking_master 
    WHERE booking_status = 'Enabled'
";
$getOnlineBookingRes = mysqli_query($con, $getOnlineBookingQry);
$getOnlineBookingRow = mysqli_fetch_assoc($getOnlineBookingRes);

$total_online_booking = $getOnlineBookingRow['total_online'] ?? 0;


// TOTAL PENDING BOOKING

$gettotalpendingqry = "
    SELECT COUNT(*) AS total_pending 
    FROM booking_master 
    WHERE booking_installstatus = 'Pending'
";

$gettotalpendingres = mysqli_query($con, $gettotalpendingqry);
$row = mysqli_fetch_assoc($gettotalpendingres);

$total_pending = $row['total_pending'] ?? 0;

// echo $total_pending;





// TOTAL MODERATE
$getModerateQry = "
    SELECT COUNT(user_id) AS total_moderate
    FROM user_master
    WHERE user_status = 'Enable'
    AND user_id != 1
    AND LOWER(TRIM(user_department)) = 'moderate'
";
$getModerateRes = mysqli_query($con, $getModerateQry);
$getModerateRow = mysqli_fetch_assoc($getModerateRes);
$total_moderate = (int) ($getModerateRow['total_moderate'] ?? 0);






$getuserqry1 = "
    SELECT COUNT(user_id) AS total_users 
    FROM user_master 
    WHERE user_status='Disable' 
    AND user_id!='1'
";
$getuserres1 = mysqli_query($con, $getuserqry1);
$getuserrow1 = mysqli_fetch_assoc($getuserres1);

$remaining_users = $getuserrow1['total_users'] ?? 0;


?>



<style>
	.pending-count {
    font-size: 48px;
    font-weight: 700;
    color: #ff9f40; /* orange look */
}

</style>

<!-- Page Wrapper -->
<div class="main-wrapper">



	<div class="page-wrapper">



		<div class="content">
			<div class="d-block text-center page-breadcrumb mb-3 pagetitle">
				<div class="my-auto">

					<h1>Admin Dashboard</h1>
				</div>
			</div>

			<div class="row">



			
				<!-- TOTAL ADMIN -->
				<div class="col-md-3">
					<div class="row card1 mt-5">
						<div class="card-header">
							<h4 class="card-title mb-0">
								Total Admin : <?= $total_admin ?>
							</h4>
						</div>
						<a href="admin-list.php">
							<div class="card-body">
								<div id="chart-donut4"></div>
							</div>
						</a>
					</div>
				</div>

				<!--  TOTAL USER  -->

				<div class="col-md-3">
					<div class="row card1 mt-5">


						<?php
						$total_user_display = $total_users + $remaining_users;
						?>

						<div class="card-header">
							<h4 class="card-title mb-0">
								Total Advisor : <?php echo $total_user_display; ?>
							</h4>
						</div>

						<a href="moderate-list.php">

							<div class="card-body">
								<div id="chart-donut"></div>
							</div>
						</a>

						<!-- <div class="row add-advisor">
							<div class="col-md-8"></div>
							<div class="col-md-4 text-right">
								<a href="advisor-add.php" class="add-user-btn" >
										+ Add Advisor
									</a>
							</div>
						</div> -->

					</div>
				</div>





				<!--  TOTAL MODERATE  -->

				<div class="col-md-3">
					<div class="row card1 mt-5">
						<div class="card-header">
							<h4 class="card-title mb-0">
								Total Moderate : <?= $total_moderate ?>
							</h4>
						</div>
						<a href="total-moderate-list.php">
							<div class="card-body">
								<div id="chart-donut3"></div>
							</div>
						</a>

					</div>
				</div>

				<!--  TOTAL BOOKING  -->
				<div class="col-md-3">
					<div class="row card1 mt-5">



						<div class="card-header">
							<h4 class="card-title mb-0">Total Booking</h4>
						</div>
						<a href="booking-list.php">
							<div class="card-body">
								<div id="chart-donut2"></div>
							</div>
						</a>

						<!-- <div class="row add-advisor">
							<div class="col-md-12">
								<center> 
									<a href="booking-list.php" class="add-user-btn">View All</a>
								</center>

							</div>

						</div> -->


					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-4">
    <div class="card1 mt-5 text-center">
        <div class="card-header">
            <h4 class="card-title mb-0">Total Pending</h4>
        </div>
        <div class="card-body">
            <h1 class="pending-count"><?php echo $total_pending; ?></h1>
            <p class="text-muted mb-0">Pending Installments</p>
        </div>
    </div>
</div>

				</div>
			</div>

		</div>
	</div>

</div>


<!--  JS  -->
<script src="assets/plugins/c3-chart/d3.v5.min.js"></script>
<script src="assets/plugins/c3-chart/c3.min.js"></script>

<script>
	var totalUsers = <?php echo (int) $total_users; ?>;
	var remainingUsers = <?php echo (int) $remaining_users; ?>;
	var totalModerate = <?= $total_moderate ?>;

	var onlineBooking = <?php echo (int) $total_online_booking; ?>;


	/* TOTAL USER DONUT */
	c3.generate({
		bindto: '#chart-donut',
		data: {
			columns: [
				['Remaining', remainingUsers],
				['Users', totalUsers]
			],
			type: 'donut',
			colors: {
				'Remaining': '#F2F2F2',
				'Users': '#6771dc'
			}
		},
		donut: {
			title: totalUsers.toString()
		},
		legend: { show: false }
	});


	/* ONLINE BOOKING DONUT */
	c3.generate({
		bindto: '#chart-donut2',
		data: {
			columns: [
				['Online', onlineBooking]
			],
			type: 'donut',
			colors: {
				'Online': '#6794dc'
			}
		},
		donut: {
			title: onlineBooking.toString()
		},
		legend: { show: false }
	});



	// TOTAL MODERATE 

	var directValue = 0;

	var moderateColumns;
	var moderateColor;

	if (parseInt(totalModerate) === 0) {
		moderateColumns = [
			['Moderate', 1]   // dummy value so donut render ho
		];
		moderateColor = '#E0E0E0'; // GRAY when zero
	} else {
		moderateColumns = [
			['Moderate', totalModerate]
		];
		moderateColor = '#67b7dc'; // color
	}

	c3.generate({
		bindto: '#chart-donut3',
		data: {
			columns: moderateColumns,
			type: 'donut',
			colors: {
				'Moderate': moderateColor
			}
		},
		donut: {
			title: (parseInt(totalModerate) === 0) ? '0' : totalModerate.toString()
		},
		legend: {
			show: false
		}
	});



	// /TOTAL ADMIN


	var totalAdmin = <?= $total_admin ?>;

	var adminColumns;
	var adminColor;

	if (parseInt(totalAdmin) === 0) {
		adminColumns = [
			['Admin', 1] // dummy value
		];
		adminColor = '#E0E0E0'; // gray when zero
	} else {
		adminColumns = [
			['Admin', totalAdmin]
		];
		adminColor = '#517e9e'; // admin color (change if you want)
	}

	c3.generate({
		bindto: '#chart-donut4',
		data: {
			columns: adminColumns,
			type: 'donut',
			colors: {
				'Admin': adminColor
			}
		},
		donut: {
			title: (parseInt(totalAdmin) === 0) ? '0' : totalAdmin.toString()
		},
		legend: {
			show: false
		}
	});




</script>



<?php
include "layout/footer.php";
?>