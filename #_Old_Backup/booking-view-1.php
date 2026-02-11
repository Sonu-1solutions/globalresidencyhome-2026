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



<?php
// include('../smtp/PHPMailerAutoload.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');

$booking_id = $_GET['booking_id'];
if (!$booking_id) {
    echo '<script> window.location="booking-list"; </script>';
    exit;
}

$propertyqry = mysqli_query($con, "SELECT * FROM booking_master WHERE booking_id='$booking_id'");
if (!$propertyqry || mysqli_num_rows($propertyqry) == 0) {
    echo '<script> window.location="booking-list"; </script>';
    exit;
}
$propertydata = mysqli_fetch_assoc($propertyqry);

// Convert booking_date from DD-MM-YY to YYYY-MM-DD for display
if (!empty($propertydata['booking_date'])) {
    $date = DateTime::createFromFormat('d-m-y', $propertydata['booking_date']);
    $propertydata['booking_date'] = $date ? $date->format('Y-m-d') : '';
}

// Function to generate installments based on payment plan
function generateInstallments($con, $booking_id, $payplan, $totalamt, $installdate)
{
    error_log("generateInstallments called with: booking_id=$booking_id, payplan=$payplan, totalamt=$totalamt, installdate=$installdate");

    // Delete existing installments
    $delete_query = "DELETE FROM installment_master WHERE installment_bookingid='$booking_id'";
    if (!mysqli_query($con, $delete_query)) {
        error_log("Failed to delete existing installments: " . mysqli_error($con));
        return false;
    }

    $installments = [];
    $cdate = date('Y-m-d H:i:s');
    $cby = $_SESSION['user_id'] ?? 0;

    try {
        // Validate inputs
        if (!$installdate || !strtotime($installdate)) {
            error_log("Invalid installdate: $installdate");
            return false;
        }
        if ($totalamt <= 0) {
            error_log("Invalid totalamt: $totalamt");
            return false;
        }
        $start_date = $installdate;

        // First three fixed installments
        $threedayamt = round((10 * $totalamt) / 100, 2); // 10%
        $eightydayamt = round(($payplan == 'Down Payment' || $payplan == 'Basic' ? 80 : 20) * $totalamt / 100, 2); // 80% or 20%
        $sixtydayamt = round((10 * $totalamt) / 100, 2); // 10%
        $threedayemi = '10%';
        $eightydayemi = ($payplan == 'Down Payment' || $payplan == 'Basic') ? '80%' : '20%';
        $sixtydayemi = '10%';

        $date3 = date('Y-m-d', strtotime('+3 days', strtotime($start_date)));
        $date28 = date('Y-m-d', strtotime('+28 days', strtotime($start_date)));
        $date60 = date('Y-m-d', strtotime('+60 days', strtotime($start_date)));

        $particulars1 = 'On Booking';
        $particulars2 = 'Within 28 days';
        $particulars3 = 'Within 60 days';

        // Add first three installments
        $installments[] = [
            'date' => $date3,
            'particular' => $particulars1,
            'emiper' => $threedayemi,
            'amount' => $threedayamt,
            'percentage' => 3
        ];
        $installments[] = [
            'date' => $date28,
            'particular' => $particulars2,
            'emiper' => $eightydayemi,
            'amount' => $eightydayamt,
            'percentage' => 28
        ];
        $installments[] = [
            'date' => $date60,
            'particular' => $particulars3,
            'emiper' => $sixtydayemi,
            'amount' => $sixtydayamt,
            'percentage' => 60
        ];

        // Calculate remaining amount and number of remaining installments
        $pendingamt = $totalamt - ($threedayamt + $eightydayamt + $sixtydayamt);
        error_log("Pending amount after first three installments: $pendingamt");
        $installment_count = 0;
        switch ($payplan) {
            case 'Six Months':
                $installment_count = 4;
                break;
            case 'Twelve Months':
                $installment_count = 10;
                break;
            case 'Eighteen Months':
                $installment_count = 16;
                break;
            case 'Twenty Four Months':
                $installment_count = 22;
                break;
            case 'Down Payment':
            case 'Basic':
                $installment_count = 0;
                break;
            default:
                error_log("Invalid payment plan: $payplan");
                return false;
        }

        // Generate remaining installments
        if ($installment_count > 0) {
            $installmentamt = round($pendingamt / $installment_count, 2);
            $installment_emipernew = ($installmentamt / $totalamt) * 100;
            $installment_emiper = round($installment_emipernew, 2) . '%';
            error_log("Remaining installment amount: $installmentamt, percentage: $installment_emiper");

            for ($j = 1; $j <= $installment_count; $j++) {
                $noofday = 60 + ($j * 30);
                $date = date('Y-m-d', strtotime("+$noofday days", strtotime($start_date)));
                if (!$date || $date == '1970-01-01') {
                    error_log("Invalid date generated for installment $j: $date");
                    return false;
                }
                $installments[] = [
                    'date' => $date,
                    'particular' => "$j Emi",
                    'emiper' => $installment_emiper,
                    'amount' => $installmentamt,
                    'percentage' => $noofday
                ];
            }
        }

        // Insert installments into database
        foreach ($installments as $index => $installment) {
            $date = mysqli_real_escape_string($con, $installment['date']);
            $particular = mysqli_real_escape_string($con, $installment['particular']);
            $emiper = mysqli_real_escape_string($con, $installment['emiper']);
            $amount = $installment['amount'];
            $percentage = $installment['percentage'];
            $query = "INSERT INTO installment_master (installment_bookingid, installment_date, installment_percentage, installment_amount, installment_ctime, installment_cby, installment_particular, installment_emiper) 
                      VALUES ('$booking_id', '$date', '$percentage', '$amount', '$cdate', '$cby', '$particular', '$emiper')";
            if (!mysqli_query($con, $query)) {
                error_log("Failed to insert installment #$index: " . mysqli_error($con));
                return false;
            }
        }
        error_log("Successfully generated " . count($installments) . " installments for booking_id=$booking_id");
        return true;
    } catch (Exception $e) {
        error_log("Exception in generateInstallments: " . $e->getMessage());
        return false;
    }
}

// Handle form submission for updating booking details
if (isset($_POST['formupdate'])) {
    $booking_no = mysqli_real_escape_string($con, $_POST['booking_no']);
    $booking_date = mysqli_real_escape_string($con, $_POST['booking_date']);
    $booking_fname = mysqli_real_escape_string($con, $_POST['booking_fname']);
    $booking_lname = mysqli_real_escape_string($con, $_POST['booking_lname']);
    $booking_phone = mysqli_real_escape_string($con, $_POST['booking_phone']);
    $booking_email = mysqli_real_escape_string($con, $_POST['booking_email']);
    $booking_sur = mysqli_real_escape_string($con, $_POST['booking_sur']);
    $booking_surname = mysqli_real_escape_string($con, $_POST['booking_surname']);
    $booking_dob = mysqli_real_escape_string($con, $_POST['booking_dob']);
    $booking_state = mysqli_real_escape_string($con, $_POST['booking_state']);
    $booking_city = mysqli_real_escape_string($con, $_POST['booking_city']);
    $booking_address = mysqli_real_escape_string($con, $_POST['booking_address']);
    $booking_project = mysqli_real_escape_string($con, $_POST['booking_project']);
    $booking_plottype = mysqli_real_escape_string($con, $_POST['booking_plottype']);
    $booking_facing = mysqli_real_escape_string($con, $_POST['booking_facing']);
    $booking_plotsize = mysqli_real_escape_string($con, $_POST['booking_plotsize']);
    $booking_payplan = mysqli_real_escape_string($con, $_POST['booking_payplan']);
    $booking_paymode = mysqli_real_escape_string($con, $_POST['booking_paymode']);
    $booking_acname = mysqli_real_escape_string($con, $_POST['booking_acname']);
    $booking_acno = mysqli_real_escape_string($con, $_POST['booking_acno']);
    $booking_ifsc = mysqli_real_escape_string($con, $_POST['booking_ifsc']);
    $booking_cheque = mysqli_real_escape_string($con, $_POST['booking_cheque']);
    $booking_schemeamt = mysqli_real_escape_string($con, $_POST['booking_schemeamt']);
    $booking_advisor = mysqli_real_escape_string($con, $_POST['booking_advisor']);
    $booking_aadharno = mysqli_real_escape_string($con, $_POST['booking_aadharno']);
    $booking_panno = mysqli_real_escape_string($con, $_POST['booking_panno']);

    // Convert booking_date back to DD-MM-YY for storage
    $booking_date_stored = $booking_date;
    if (!empty($booking_date)) {
        $date = DateTime::createFromFormat('Y-m-d', $booking_date);
        if ($date) {
            $booking_date_stored = $date->format('d-m-y');
        }
    }

    // Handle file uploads
    $booking_aadharphoto = $propertydata['booking_aadharphoto'];
    $booking_panphoto = $propertydata['booking_panphoto'];

    if (!empty($_FILES['booking_aadharphoto']['name'])) {
        $aadhar_tmp = $_FILES['booking_aadharphoto']['tmp_name'];
        $aadhar_name = time() . '_' . $_FILES['booking_aadharphoto']['name'];
        if (!move_uploaded_file($aadhar_tmp, "booking-image/$aadhar_name")) {
            error_log("Failed to upload aadhar photo: $aadhar_name");
        } else {
            $booking_aadharphoto = $aadhar_name;
        }
    }

    if (!empty($_FILES['booking_panphoto']['name'])) {
        $pan_tmp = $_FILES['booking_panphoto']['tmp_name'];
        $pan_name = time() . '_' . $_FILES['booking_panphoto']['name'];
        if (!move_uploaded_file($pan_tmp, "booking-image/$pan_name")) {
            error_log("Failed to upload pan photo: $pan_name");
        } else {
            $booking_panphoto = $pan_name;
        }
    }

    $update_query = "UPDATE booking_master SET 
        booking_no='$booking_no',
        booking_date='$booking_date_stored',
        booking_fname='$booking_fname',
        booking_lname='$booking_lname',
        booking_phone='$booking_phone',
        booking_email='$booking_email',
        booking_sur='$booking_sur',
        booking_surname='$booking_surname',
        booking_dob='$booking_dob',
        booking_state='$booking_state',
        booking_city='$booking_city',
        booking_address='$booking_address',
        booking_project='$booking_project',
        booking_plottype='$booking_plottype',
        booking_facing='$booking_facing',
        booking_plotsize='$booking_plotsize',
        booking_payplan='$booking_payplan',
        booking_paymode='$booking_paymode',
        booking_acname='$booking_acname',
        booking_acno='$booking_acno',
        booking_ifsc='$booking_ifsc',
        booking_cheque='$booking_cheque',
        booking_schemeamt='$booking_schemeamt',
        booking_advisor='$booking_advisor',
        booking_aadharno='$booking_aadharno',
        booking_panno='$booking_panno',
        booking_aadharphoto='$booking_aadharphoto',
        booking_panphoto='$booking_panphoto'
        WHERE booking_id='$booking_id'";

    if (mysqli_query($con, $update_query)) {
        // Update installments if booking_installstatus is Completed
        $installments_updated = false;
        if ($propertydata['booking_installstatus'] == 'Completed') {
            if (!generateInstallments($con, $booking_id, $booking_payplan, $propertydata['booking_totalamt'], $propertydata['booking_installdate'])) {
                error_log("Failed to generate installments for booking_id=$booking_id");
                echo '<script>alert("Error generating installments. Please check error logs.");</script>';
            } else {
                $installments_updated = true;
            }
        }

        // Fetch updated booking details
        $bookingmasterquery = mysqli_query($con, "SELECT * FROM booking_master WHERE booking_id='$booking_id'");
        if ($bookingmasterquery && mysqli_num_rows($bookingmasterquery) > 0) {
            $bookingmasterqry = mysqli_fetch_assoc($bookingmasterquery);
            $to = $bookingmasterqry['booking_email'];
            $user_fullname = $bookingmasterqry['booking_fname'] . ' ' . $bookingmasterqry['booking_lname'];
            $booking_no = $bookingmasterqry['booking_no'];
            $booking_advisor = $bookingmasterqry['booking_advisor'];
            $user_address = $bookingmasterqry['booking_address'] . ', ' . $bookingmasterqry['booking_city'] . ', ' . $bookingmasterqry['booking_state'];

            // Fetch advisor details
            $advisiorqry = mysqli_query($con, "SELECT * FROM user_master WHERE user_name='$booking_advisor' AND user_status='Enable' AND user_department='User'");
            if ($advisiorqry && mysqli_num_rows($advisiorqry) > 0) {
                $advisiordata = mysqli_fetch_assoc($advisiorqry);
                $aduser_email = $advisiordata['user_email'];
                $aduser_mobile = $advisiordata['user_mobile'];
                $aduser_name = $advisiordata['user_name'];
            } else {
                error_log("Advisor '$booking_advisor' not found in user_master for booking_id=$booking_id");
                $aduser_email = 'info@globalresidencyhome.com';
                $aduser_mobile = 'N/A';
                $aduser_name = $booking_advisor ?: 'Unknown Advisor';
            }

            // Fetch installment data if booking_installstatus is Completed
            $taskcommentsalldata = [];
            $phliinstalment = '';
            $phliinstalmentamt = '';
            if ($bookingmasterqry['booking_installstatus'] == 'Completed') {
                $installment_query = "SELECT * FROM installment_master WHERE installment_bookingid='$booking_id' AND installment_status='Enabled' ORDER BY installment_date";
                $installment_result = mysqli_query($con, $installment_query);
                if ($installment_result) {
                    while ($installment_data = mysqli_fetch_assoc($installment_result)) {
                        $taskcommentsalldata[] = $installment_data;
                    }
                } else {
                    error_log("Failed to fetch installments: " . mysqli_error($con));
                }
            }

            // Build email content
            $subject = 'Updated Booking Details for ' . $user_fullname;
            $message = '<html><head><style>.bkc{
            background-image: url("https://www.globalresidencyhome.com/erp/mid-part.jpg");
             background-size: contain; /* <- Now contain */
  background-position: center;
  background-repeat: no-repeat;
  background-color: #ffffff;
  
  font-family: Arial, Helvetica, sans-serif;
  color: #333333;
  margin: 0;
  
  width: 100%;
  min-height: 100vh;
  
  padding: 20px 40px;
        }
        .top-logo{
             background-image: url("https://www.globalresidencyhome.com/erp/top.jpg");
              background-size: 100% auto; /* Width 100%, height auto */
  background-repeat: no-repeat;
  background-position: top center;
  width: 100%;
 
  position: relative;
  padding-top: 10.25%;
    max-width: 500px;

        }
        .last-part{
             background-image: url("https://www.globalresidencyhome.com/erp/last-part.jpg");
            background-size: 100% auto; /* Width 100%, height auto */
  background-repeat: no-repeat;
  background-position: top center;
  width: 100%;
  padding-top: 12.25%; /* Maintain aspect ratio 16:9 */
  position: relative;
  max-width:600px;
        }
        </style></head><body>
                <div class="top-logo">
                 </div>
        <div class="bkc">

       
                <p>Dated: <strong>' . date('d-m-Y') . '</strong></p>
                <p>To,</p>
                
                <p><strong>' . $user_fullname . '</strong></p>
                <p><strong>' . $user_address . '</strong></p>
                
                <p>Dear Mr/Mrs/Ms. <strong>' . $user_fullname . '</strong></p>
                <p>We at Shaan Realtech Pvt. Ltd. have updated your booking details for your investment in ' . htmlspecialchars($bookingmasterqry['booking_project']) . '. Below are the updated details for your reference.</p>
                <h3>Booking Details:</h3>
                <table border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th style="padding: 10px 15px;">Field</th>
                            <th style="padding: 10px 15px;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px 15px;">Booking Number</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_no']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Booking Date</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_date']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Project</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_project']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Plot Type</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plottype']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Plot Facing</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_facing']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Plot Size</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plotsize']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Payment Plan</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_payplan']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Payment Mode</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_paymode']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Scheme Amount</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_schemeamt']) . '</td>
                        </tr>';

            if ($bookingmasterqry['booking_installstatus'] == 'Completed') {
                $message .= '
                        <tr>
                            <td style="padding: 10px 15px;">Plot Number</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plotno']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Plot Area (Sq. Yds.)</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plotarea']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Plot Rate (Per Sq. Yd.)</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plotrate']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">PLC (%)</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_plc']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">EDC (%)</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_edc']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">IDC (%)</td>
                            <td style="padding: 10px 15px;">' . htmlspecialchars($bookingmasterqry['booking_idc']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px;">Total Amount</td>
                            <td style="padding: 10px 15px;">' . number_format($bookingmasterqry['booking_totalamt'], 2) . '</td>
                        </tr>';
            }

            $message .= '</tbody></table>';

            // Add installment schedule if applicable
            if ($bookingmasterqry['booking_installstatus'] == 'Completed' && !empty($taskcommentsalldata)) {
                $message .= '<h3>Payment Schedule</h3>
                    <table border="1" style="width:100%;text-align: center;">
                        <thead>
                            <tr>
                                <th style="padding: 10px 15px;">SNO</th>
                                <th style="padding: 10px 15px;">Date</th>
                                <th style="padding: 10px 15px;">Particulars</th>
                                <th style="padding: 10px 15px;">%</th>
                                <th style="padding: 10px 15px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>';

                $installno = 1;
                foreach ($taskcommentsalldata as $taskrecord) {
                    if ($installno == 1) {
                        $phliinstalment = $taskrecord['installment_date'];
                        $phliinstalmentamt = $taskrecord['installment_amount'];
                    }
                    $message .= '<tr>
                        <td>' . $installno . '</td>
                        <td>' . date('d-m-Y', strtotime($taskrecord['installment_date'])) . '</td>
                        <td>' . htmlspecialchars($taskrecord['installment_particular']) . '</td>
                        <td>' . htmlspecialchars($taskrecord['installment_emiper']) . '</td>
                        <td>Rs. ' . number_format($taskrecord['installment_amount'], 2) . '</td>
                    </tr>';
                    $installno++;
                }

                $message .= '</tbody></table>';

                if ($phliinstalment && $phliinstalmentamt) {
                    $message .= '<p>Please ensure the initial amount of 10% (Rs. ' . number_format($phliinstalmentamt, 2) . ') is paid by ' . date('d-m-Y', strtotime($phliinstalment)) . ' to confirm your allotment.</p>';
                }
            }

            $message .= '
                <p><strong>Payment Details for Reference:</strong><br>
                Account Name: Shaan Realtech Pvt Ltd<br>
                Account Number: 03278630000188<br>
                Bank: HDFC BANK<br>
                IFSC CODE: HDFC0000327<br>
                </p>
                <p>Your account manager is <strong>' . htmlspecialchars($aduser_name) . '</strong> and can be reached at <strong>' . htmlspecialchars($aduser_mobile) . '</strong> for any queries.</p>
                <br><br>
                <p><strong>With Best Regards</strong></p>
                <p>
                Anurag Pathak<br>
                Accounts Manager<br>
                Shaan Realtech Pvt. Ltd.<br>
                Web: www.globalresidencyhome.com/<br>
                Corporate Office: Office-1, 2nd Floor,<br>
                A-12, A-13, Sector 16, Noida, UP 201301.<br>
                </p>
                <p style="text-align:center">This is a computer-generated document and requires no signature.</p>

                </div>
                                <div class="last-part"></div>
                </body></html>';

            // Send email
            $mail = new PHPMailer();
            try {
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465;
                $mail->IsHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Username = "info@shaanrealtech.com";
                $mail->Password = "anaqyuhlpymhozrp";
                $mail->SetFrom("info@shaanrealtech.com", "Shaan Realtech Pvt. Ltd.");
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AddAddress($to);
                $mail->AddCC('info@globalresidencyhome.com');
                $mail->AddCC($aduser_email);
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                if (!$mail->Send()) {
                    error_log("Failed to send update email: " . $mail->ErrorInfo);
                    echo '<script>alert("Booking updated successfully, but failed to send email notification.");</script>';
                } else {
                    error_log("Update email sent successfully to $to");
                }
            } catch (Exception $e) {
                error_log("PHPMailer exception: " . $e->getMessage());
                echo '<script>alert("Booking updated successfully, but failed to send email notification due to an error.");</script>';
            }
        } else {
            error_log("Failed to fetch updated booking details for booking_id=$booking_id");
            echo '<script>alert("Booking updated successfully, but failed to fetch updated details for email notification.");</script>';
        }

        echo '<script>alert("Booking updated successfully"); window.location="booking-view?booking_id=' . $booking_id . '";</script>';
    } else {
        error_log("Failed to update booking: " . mysqli_error($con));
        echo '<script>alert("Error updating booking: ' . mysqli_error($con) . '");</script>';
    }
}
?>

















<!-- Page Wrapper -->
<div class="page-wrapper">

    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <h1>Booking Views</h1>
            </div>
        </div>


        <div class="row">

            <div class="col-md-12 mt-3">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Booking No</label>

                            <input type="text" class="form-control py-4" name="booking_no"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_no']); ?>">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Project</label>
                            <select class="form-control" name="booking_project" required style="height: 50px;">
                                <option value="">—Please choose an option—</option>
                                <option value="Global Residency Homes" <?php echo ($propertydata['booking_project'] == 'Global Residency Homes') ? 'selected' : ''; ?>>
                                    Global Residency Homes</option>
                                <option value="Global Farms" <?php echo ($propertydata['booking_project'] == 'Global Farms') ? 'selected' : ''; ?>>Global Farms</option>
                                <option value="Global Galleria Shops" <?php echo ($propertydata['booking_project'] == 'Global Galleria Shops') ? 'selected' : ''; ?>>
                                    Global Galleria Shops</option>
                            </select>

                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Booking Date</label>
                            <input type="date" class="form-control py-4" name="booking_date"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_date']); ?>">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold"> Plot Type </label>
                            <select class="form-control" name="booking_plottype" required style="height: 50px;">
                                <option value="Residential" <?php echo ($propertydata['booking_plottype'] == 'Residential') ? 'selected' : ''; ?>>Residential
                                </option>
                                <option value="Commercial" <?php echo ($propertydata['booking_plottype'] == 'Commercial') ? 'selected' : ''; ?>>Commercial</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">First Name</label>
                            <input type="text" class="form-control py-4" name="booking_fname"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_fname']); ?>">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Plot Facing</label>
                            <select class="form-control" name="booking_facing" required style="height: 50px;">
                                <option value="Corner Facing" <?php echo ($propertydata['booking_facing'] == 'Corner Facing') ? 'selected' : ''; ?>>Corner Facing</option>
                                <option value="Park Facing" <?php echo ($propertydata['booking_facing'] == 'Park Facing') ? 'selected' : ''; ?>>Park Facing</option>
                                <option value="Wide Road Facing" <?php echo ($propertydata['booking_facing'] == 'Wide Road Facing') ? 'selected' : ''; ?>>Wide Road Facing</option>
                                <option value="Behind Commercial" <?php echo ($propertydata['booking_facing'] == 'Behind Commercial') ? 'selected' : ''; ?>>Behind Commercial</option>
                                <option value="None" <?php echo ($propertydata['booking_facing'] == 'None') ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Last Name</label>
                            <input type="text" class="form-control py-4" name="booking_lname"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_lname']); ?>">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Plot Size</label>
                            <select class="form-control" name="booking_plotsize" required style="height: 50px;">
                                <option value="">—Please choose an option—</option>
                                <option value="100 (Sq.Yrd) to 150 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '100 (Sq.Yrd) to 150 (Sq.Yrd)') ? 'selected' : ''; ?>>100 (Sq.Yrd) to 150 (Sq.Yrd)</option>
                                <option value="150 (Sq.Yrd) to 200 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '150 (Sq.Yrd) to 200 (Sq.Yrd)') ? 'selected' : ''; ?>>150 (Sq.Yrd) to 200 (Sq.Yrd)</option>
                                <option value="200 (Sq.Yrd) to 250 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '200 (Sq.Yrd) to 250 (Sq.Yrd)') ? 'selected' : ''; ?>>200 (Sq.Yrd) to 250 (Sq.Yrd)</option>
                                <option value="250 (Sq.Yrd) to 300 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '250 (Sq.Yrd) to 300 (Sq.Yrd)') ? 'selected' : ''; ?>>250 (Sq.Yrd) to 300 (Sq.Yrd)</option>
                                <option value="300 (Sq.Yrd) to 350 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '300 (Sq.Yrd) to 350 (Sq.Yrd)') ? 'selected' : ''; ?>>300 (Sq.Yrd) to 350 (Sq.Yrd)</option>
                                <option value="350 (Sq.Yrd) to 400 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '350 (Sq.Yrd) to 400 (Sq.Yrd)') ? 'selected' : ''; ?>>350 (Sq.Yrd) to 400 (Sq.Yrd)</option>
                                <option value="400 (Sq.Yrd) to 450 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '400 (Sq.Yrd) to 450 (Sq.Yrd)') ? 'selected' : ''; ?>>400 (Sq.Yrd) to 450 (Sq.Yrd)</option>
                                <option value="450 (Sq.Yrd) to 500 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '450 (Sq.Yrd) to 500 (Sq.Yrd)') ? 'selected' : ''; ?>>450 (Sq.Yrd) to 500 (Sq.Yrd)</option>
                                <option value="300 (Sq.Yrd) to 500 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '300 (Sq.Yrd) to 500 (Sq.Yrd)') ? 'selected' : ''; ?>>300 (Sq.Yrd) to 500 (Sq.Yrd) Global Farms Size</option>
                                <option value="700 (Sq.Yrd) to 1000 (Sq.Yrd)" <?php echo ($propertydata['booking_plotsize'] == '700 (Sq.Yrd) to 1000 (Sq.Yrd)') ? 'selected' : ''; ?>>500 (Sq.Yrd) to 700 (Sq.Yrd) Global Farms Size</option>
                                <option value="Area will be 100 sq feet" <?php echo ($propertydata['booking_plotsize'] == 'Area will be 100 sq feet') ? 'selected' : ''; ?>>
                                    Area will be 100 sq feet Global Galleria Shops</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Phone Number</label>
                            <input type="text" class="form-control py-4" name="booking_phone"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_phone']); ?>" minlength="10"
                                maxlength="10">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Payment Plan</label>
                            <select class="form-control" name="booking_payplan" required style="height: 50px;">
                                <option value="">—Please choose an option—</option>
                                <option value="Down Payment" <?php echo ($propertydata['booking_payplan'] == 'Down Payment') ? 'selected' : ''; ?>>Down Payment</option>
                                <option value="Basic" <?php echo ($propertydata['booking_payplan'] == 'Basic') ? 'selected' : ''; ?>>Basic</option>
                                <option value="Six Months" <?php echo ($propertydata['booking_payplan'] == 'Six Months') ? 'selected' : ''; ?>>Six Months</option>
                                <option value="Twelve Months" <?php echo ($propertydata['booking_payplan'] == 'Twelve Months') ? 'selected' : ''; ?>>Twelve Months</option>
                                <option value="Eighteen Months" <?php echo ($propertydata['booking_payplan'] == 'Eighteen Months') ? 'selected' : ''; ?>>Eighteen Months</option>
                                <option value="Twenty Four Months" <?php echo ($propertydata['booking_payplan'] == 'Twenty Four Months') ? 'selected' : ''; ?>>Twenty Four Months</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Email</label>
                            <input type="email" class="form-control py-4" name="booking_email"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_email']); ?>">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">Payment Mode</label>
                            <select class="form-control" name="booking_paymode" id="booking_paymode" required
                                style="height: 50px;">
                                <option value="">Select Payment Mode</option>
                                <option value="offline" <?php echo ($propertydata['booking_paymode'] == 'offline') ? 'selected' : ''; ?>>Offline</option>
                                <option value="netbanking" <?php echo ($propertydata['booking_paymode'] == 'netbanking') ? 'selected' : ''; ?>>Net Banking</option>
                                <option value="online" <?php echo ($propertydata['booking_paymode'] == 'online') ? 'selected' : ''; ?>>Online</option>
                            </select>
                        </div>


                        <div class="col-md-6 mb-5">
                            <label class="fw-bold">S/O, W/o</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" name="booking_sur">
                                        <option value="" <?php echo empty($propertydata['booking_sur']) ? 'selected' : ''; ?>>Select</option>
                                        <option value="C/o" <?php echo ($propertydata['booking_sur'] == 'C/o') ? 'selected' : ''; ?>>C/o</option>
                                        <option value="S/o" <?php echo ($propertydata['booking_sur'] == 'S/o') ? 'selected' : ''; ?>>S/o</option>
                                        <option value="D/o" <?php echo ($propertydata['booking_sur'] == 'D/o') ? 'selected' : ''; ?>>D/o</option>
                                        <option value="W/o" <?php echo ($propertydata['booking_sur'] == 'W/o') ? 'selected' : ''; ?>>W/o</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="booking_surname"
                                        value="<?php echo htmlspecialchars(@$propertydata['booking_surname']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">Scheme Amount</label>
                            <input type="text" class="form-control py-4" name="booking_schemeamt"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_schemeamt']); ?>">
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">DOB</label>
                            <input type="date" class="form-control py-4" name="booking_dob"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_dob']); ?>">
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">Advisor Name</label>
                            <select class="form-control" name="booking_advisor" required style="height: 50px;">
                                <option value="">—Please choose Advisor—</option>
                                <?php
                                $advisiorqry = mysqli_query($con, "SELECT user_id, user_name FROM user_master WHERE user_status='Enable' AND user_department='User'");
                                if (!$advisiorqry) {
                                    error_log("Advisor query failed: " . mysqli_error($con));
                                    echo "<option value=''>Error fetching advisors</option>";
                                } else {
                                    $advisor_found = false;
                                    while ($advisiordata = mysqli_fetch_assoc($advisiorqry)) {
                                        $selected = (
                                            (string) $propertydata['booking_advisor'] === (string) $advisiordata['user_id'] ||
                                            (string) $propertydata['booking_advisor'] === (string) $advisiordata['user_name']
                                        ) ? 'selected' : '';
                                        if ($selected) {
                                            $advisor_found = true;
                                        }
                                        echo "<option value='" . htmlspecialchars($advisiordata['user_name']) . "' $selected>" . htmlspecialchars($advisiordata['user_name']) . "</option>";
                                    }
                                    if (!$advisor_found && !empty($propertydata['booking_advisor'])) {
                                        echo "<option value='" . htmlspecialchars($propertydata['booking_advisor']) . "' selected>" . htmlspecialchars($propertydata['booking_advisor']) . "</option>";
                                        error_log("Advisor '{$propertydata['booking_advisor']}' not found in user_master for booking_id=$booking_id");
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">State</label>
                            <input type="text" class="form-control py-4" name="booking_state"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_state']); ?>">
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">City</label>
                            <input type="text" class="form-control py-4" name="booking_city"
                                value="<?php echo htmlspecialchars(@$propertydata['booking_city']); ?>">
                        </div>

                        <div class="col-md-6  mb-5">
                            <label class="fw-bold">Address</label>
                            <textarea class="form-control"
                                name="booking_address"><?php echo htmlspecialchars(@$propertydata['booking_address']); ?></textarea>
                        </div>


                        <!-- <div class="col-md-6  mb-5">
                        
                    </div> -->





                    </div>
                    <div class="row">

                        <div class="col-md-6 ">
                            <div class="col-md-12">
                                <label class="fw-bold">Pan Details</label>
                                <input type="text" class="form-control py-4" name="booking_panno"
                                    value="<?php echo htmlspecialchars(@$propertydata['booking_panno']); ?>"
                                    minlength="10" maxlength="10">
                            </div>

                            <label>Upload Pan Card</label>
                            <?php if ($propertydata['booking_panphoto']) { ?>
                                <div>
                                    <img src="booking-image/<?php echo htmlspecialchars($propertydata['booking_panphoto']); ?>"
                                        width="100">
                                    <a href="booking-image/<?php echo htmlspecialchars($propertydata['booking_panphoto']); ?>"
                                        target="_blank" class="btn btn-sm btn-info" style="margin-left: 10px;">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="booking-image/<?php echo htmlspecialchars($propertydata['booking_panphoto']); ?>"
                                        download class="btn btn-sm btn-success" style="margin-left: 5px;">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            <?php } else { ?>
                                <p>No file uploaded</p>
                            <?php } ?>
                            <input type="file" class="form-control" name="booking_panphoto" accept="image/*"
                                style="margin-top: 10px;">
                        </div>

                        <div class="col-md-6 mb-4">

                            <div class="row">
                                <div class="col-md-12  mb-2">
                                    <label class="fw-bold">Aadhar Details1</label>
                                    <input type="text" class="form-control py-4" name="booking_aadharno"
                                        value="<?php echo htmlspecialchars(@$propertydata['booking_aadharno']); ?>"
                                        minlength="12" maxlength="12">
                                </div>
                            </div>
                            <div class="row">

                                <label>Upload Aadhar Card1</label>
                                <?php if ($propertydata['booking_aadharphoto']) { ?>
                                    <div>
                                        <img src="booking-image/<?php echo htmlspecialchars($propertydata['booking_aadharphoto']); ?>"
                                            width="100">
                                        <a href="booking-image/<?php echo htmlspecialchars($propertydata['booking_aadharphoto']); ?>"
                                            target="_blank" class="btn btn-sm btn-info" style="margin-left: 10px;">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="booking-image/<?php echo htmlspecialchars($propertydata['booking_aadharphoto']); ?>"
                                            download class="btn btn-sm btn-success" style="margin-left: 5px;">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <p>No file uploaded</p>
                                <?php } ?>
                                <input type="file" class="form-control" name="booking_aadharphoto" accept="image/*"
                                    style="margin-top: 10px;">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <button type="submit" name="formupdate" class="btn btn-primary">Update Booking</button>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <?php
                    $booking_installstatus = $propertydata['booking_installstatus'];
                    if ($booking_installstatus == 'Pending') {
                        ?>
                        <div class="col-md-4 mb-4">
                            <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#plotNoAssign">
                                Plot No Assign
                            </button> -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                Plot No Assign
                            </button>
                        </div>
                        <?php
                    }
                    ?>
                </div>



                <?php
                if ($booking_installstatus == 'Completed') {
                    ?>
                    <div class="row" style="border-top: 2px solid #000;">
                        <div class="col-lg-4" style="margin-top: 20px;">
                            <p><strong>Installment Date</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_installdate']); ?>
                            </p>
                            <p><strong>Plot No</strong> : <?php echo htmlspecialchars(@$propertydata['booking_plotno']); ?>
                            </p>
                            <p><strong>Plot Size</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_plotarea']); ?></p>
                            <p><strong>Plot Rate</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_plotrate']); ?></p>
                            <p><strong>Plot PLC (%)</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_plc']); ?></p>
                            <p><strong>Plot EDC (%)</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_edc']); ?></p>
                            <p><strong>Plot IDC (%)</strong> :
                                <?php echo htmlspecialchars(@$propertydata['booking_idc']); ?></p>
                            <p><strong>Plot Total Amount</strong> :
                                <?php echo number_format($propertydata['booking_totalamt'], 2); ?>
                            </p>
                        </div>

                        <div class="col-lg-8" style="margin-top: 20px;">
                            <h4>Payment Installment
                                <hr>
                            </h4>
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
                                        $totalamt = $propertydata['booking_totalamt'];
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
                    <?php
                }
                ?>




            </div>

        </div>

        <!-- <head> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- </head> -->

        <div class="container">
            <!-- Modal -->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Plot No Assign</h4>
                        </div>
                        <div class="modal-body">
                            <form action="booking-plot-assign.php" method="post">
                                <input type="hidden" name="booking_id"
                                    value="<?php echo htmlspecialchars($propertydata['booking_id']); ?>" required>
                                <input type="hidden" name="booking_payplan"
                                    value="<?php echo htmlspecialchars($propertydata['booking_payplan']); ?>" required>
                                <div class="modal-body">
                                
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Installment Date:</label>
                                        <input type="date" class="form-control" id="booking_installdate"
                                            name="booking_installdate" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Plot Size:</label>
                                        <input type="number" class="form-control" id="booking_plotarea"
                                            name="booking_plotarea" required min="0" value="0" oninput="getProductAmt()"
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Plot Rate:</label>
                                        <input type="number" class="form-control" id="booking_plotrate"
                                            name="booking_plotrate" required min="0" value="0" oninput="getProductAmt()"
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">PLC (%):</label>
                                        <input type="number" class="form-control" id="booking_plc" name="booking_plc"
                                            required min="0" value="0" oninput="getProductAmt()" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">EDC (%):</label>
                                        <input type="number" class="form-control" id="booking_edc" name="booking_edc"
                                            required min="0" value="0" oninput="getProductAmt()" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">IDC (%):</label>
                                        <input type="number" class="form-control" id="booking_idc" name="booking_idc"
                                            required min="0" value="0" oninput="getProductAmt()" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Plot Amount:</label>
                                        <input type="number" class="form-control" id="booking_totalamt"
                                            name="booking_totalamt" required min="0" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Plot No:</label>
                                        <input type="text" class="form-control" id="booking_plotno"
                                            name="booking_plotno" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="formadd" class="btn btn-primary">Generate Now</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>



        <!-- /Breadcrumb -->
    </div>
</div>



<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script type="text/javascript">
    function getProductAmt() {
        var booking_plotarea = parseFloat(document.getElementById('booking_plotarea').value) || 0;
        var booking_plotrate = parseFloat(document.getElementById('booking_plotrate').value) || 0;
        var booking_plc = parseFloat(document.getElementById('booking_plc').value) || 0;
        var booking_edc = parseFloat(document.getElementById('booking_edc').value) || 0;
        var booking_idc = parseFloat(document.getElementById('booking_idc').value) || 0;

        var base_amount = booking_plotarea * booking_plotrate;
        var plc_amount = (base_amount * booking_plc) / 100;
        var edc_amount = (base_amount * booking_edc) / 100;
        var idc_amount = (base_amount * booking_idc) / 100;
        var total_amount = base_amount + plc_amount + edc_amount + idc_amount;

        if (isNaN(total_amount) || total_amount <= 0) {
            console.error("Invalid total amount: area=" + booking_plotarea + ", rate=" + booking_plotrate +
                ", plc=" + booking_plc + ", edc=" + booking_edc + ", idc=" + booking_idc);
            $('#booking_totalamt').val('0.00');
        } else {
            $('#booking_totalamt').val(total_amount.toFixed(2));
        }
    }

    $(document).ready(function () {
        $("#booking_paymode").change(function () {
            if ($(this).val() == "offline") {
                $("#account-details").show();
                $("#netbanking-details").hide();
            } else if ($(this).val() == "netbanking") {
                $("#account-details").hide();
                $("#netbanking-details").show();
            } else {
                $("#account-details").hide();
                $("#netbanking-details").hide();
            }
        });

        // Trigger calculation on input change
        $('#booking_plotarea, #booking_plotrate, #booking_plc, #booking_edc, #booking_idc').on('input', getProductAmt);

        // Validate Plot No Assign form
        $('form[action="booking-plot-assign.php"]').submit(function (event) {
            var totalamt = parseFloat($('#booking_totalamt').val()) || 0;
            var plotarea = parseFloat($('#booking_plotarea').val()) || 0;
            var plotrate = parseFloat($('#booking_plotrate').val()) || 0;
            var installdate = $('#booking_installdate').val();
            var plotno = $('#booking_plotno').val().trim();
            if (totalamt <= 0) {
                alert("Error: Total Amount must be greater than 0. Please check Plot Size and Plot Rate.");
                event.preventDefault();
                return false;
            }
            if (plotarea <= 0) {
                alert("Error: Plot Size must be greater than 0.");
                event.preventDefault();
                return false;
            }
            if (plotrate <= 0) {
                alert("Error: Plot Rate must be greater than 0.");
                event.preventDefault();
                return false;
            }
            if (!installdate || !Date.parse(installdate)) {
                alert("Error: Please enter a valid Installment Date.");
                event.preventDefault();
                return false;
            }
            if (!plotno) {
                alert("Error: Plot No is required.");
                event.preventDefault();
                return false;
            }
        });
    });
</script>




<?php
include "layout/footer.php";
?>