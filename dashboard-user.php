

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
    AND user_id!='1'
";
$getuserres = mysqli_query($con, $getuserqry);
$getuserrow = mysqli_fetch_assoc($getuserres);

$total_users = $getuserrow['total_users'] ?? 0;






/* TOTAL ONLINE BOOKING  */
$getOnlineBookingQry = "
    SELECT COUNT(booking_id) AS total_online 
    FROM booking_master 
    WHERE booking_status = 'Enabled'
    AND booking_advisorid = '$uid'
";

$getOnlineBookingRes = mysqli_query($con, $getOnlineBookingQry);
$getOnlineBookingRow = mysqli_fetch_assoc($getOnlineBookingRes);

$total_online_booking = $getOnlineBookingRow['total_online'] ?? 0;



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

<!-- Page Wrapper -->
<div class="main-wrapper">



	<div class="page-wrapper">



		<div class="content">
			<div class="d-block text-center page-breadcrumb mb-3 pagetitle">
				<div class="my-auto">
					
						<h1>Advisor Dashboard</h1>
				</div>
			</div>

			<div class="row">


				<!--  TOTAL BOOKING  -->
				<div class="col-md-4">
					<div class="row card1">


						<div class="card-header">
							<h4 class="card-title mb-0">Total Booking</h4>
						</div>

							<a href="booking-list-user.php">
							<div class="card-body">
								<div id="chart-donut2"></div>
							</div>
							</a>
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
				'Users': '#FF7D23'
			}
		},
		donut: {
			title: totalUsers.toString()
		},
		legend: { show: false }
	});

	/* BOOKING DUMMY */
	/* ONLINE BOOKING DONUT */
var onlineBooking = <?php echo (int) $total_online_booking; ?>;

// column + color decide karna
var bookingColumns;
var bookingColor;

if (onlineBooking === 0) {
    bookingColumns = [
        ['Online', 1]   // dummy value so chart render ho
    ];
    bookingColor = '#E0E0E0'; // GRAY when zero
} else {
    bookingColumns = [
        ['Online', onlineBooking]
    ];
    bookingColor = '#6771dc'; // color when data > 0
}

c3.generate({
    bindto: '#chart-donut2',
    data: {
        columns: bookingColumns,
        type: 'donut',
        colors: {
            'Online': bookingColor
        },
        labels: false,
        tooltip: {
            show: false
        }
    },
    donut: {
        title: onlineBooking.toString()
    },
    legend: {
        show: false
    }
});

	var directValue = 0;

	c3.generate({
		bindto: '#chart-donut3',
		data: {
			columns: [
				['Direct', directValue],
				['Other', 100]   // sirf chart fill ke liye
			],
			type: 'donut',
			colors: {
				'Direct': '#F2F2F2',
				'Other': '#F2F2F2'
			},
			//  labels completely OFF
			labels: false,
			//  tooltip OFF (taaki Other 100% na dikhe)
			tooltip: {
				show: false
			}
		},
		donut: {
			title: directValue + ""
		},
		legend: {
			show: false
		}
	});


</script>


<?php
include "layout/footer.php";
?>