<?php
// require 'lock.php';
// include('../smtp/PHPMailerAutoload.php');

include("database.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');

// Function to generate installments
function generateInstallments($con, $booking_id, $payplan, $totalamt, $installdate) {
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

if (isset($_POST['formadd'])) {
    // Log raw POST data for debugging
    error_log("Received POST data: " . print_r($_POST, true));

    // Sanitize and validate POST data
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id'] ?? '');
    $booking_payplan = mysqli_real_escape_string($con, $_POST['booking_payplan'] ?? '');
    $booking_installdate = mysqli_real_escape_string($con, $_POST['booking_installdate'] ?? '');
    $booking_plotarea = floatval($_POST['booking_plotarea'] ?? 0);
    $booking_plotrate = floatval($_POST['booking_plotrate'] ?? 0);
    $booking_plc = floatval($_POST['booking_plc'] ?? 0);
    $booking_edc = floatval($_POST['booking_edc'] ?? 0);
    $booking_idc = floatval($_POST['booking_idc'] ?? 0);
    $booking_totalamt = floatval($_POST['booking_totalamt'] ?? 0);
    $booking_plotno = mysqli_real_escape_string($con, $_POST['booking_plotno'] ?? '');

    // Validate inputs with specific error messages
    $errors = [];
    if (empty($booking_id)) $errors[] = "booking_id is empty";
    if (empty($booking_payplan)) $errors[] = "booking_payplan is empty";
    if (empty($booking_installdate)) $errors[] = "booking_installdate is empty";
    if ($booking_totalamt <= 0) $errors[] = "booking_totalamt is <= 0 ($booking_totalamt)";
    if (!strtotime($booking_installdate)) $errors[] = "booking_installdate is invalid ($booking_installdate)";
    if ($booking_plotarea <= 0) $errors[] = "booking_plotarea is <= 0 ($booking_plotarea)";
    if ($booking_plotrate <= 0) $errors[] = "booking_plotrate is <= 0 ($booking_plotrate)";
    if (empty($booking_plotno)) $errors[] = "booking_plotno is empty";

    if (!empty($errors)) {
        error_log("Invalid input data for booking_id=$booking_id: " . implode(", ", $errors));
        header("Location: booking-view?booking_id=$booking_id&error=" . urlencode("Invalid input data: " . implode(", ", $errors)));
        exit;
    }

    $cdate = date('Y-m-d H:i:s');
    $cby = $_SESSION['user_id'] ?? 0;

// Get Block No from form
$booking_blockno = $_POST['booking_blockno'] ?? '';

if ($booking_blockno === '') {
    header("Location: booking-view?booking_id=$booking_id&error=Please select Block No");
    exit;
}

// Update booking_master
$updateleadsql = "UPDATE `booking_master` SET 
    `booking_installstatus`='Completed',
    `booking_installdate`='$booking_installdate',
    `booking_plotarea`='$booking_plotarea',
    `booking_plotrate`='$booking_plotrate',
    `booking_plc`='$booking_plc',
    `booking_edc`='$booking_edc',
    `booking_idc`='$booking_idc',
    `booking_totalamt`='$booking_totalamt',
    `booking_plotno`='$booking_plotno',
    `booking_blockno`='$booking_blockno'
    WHERE booking_id='$booking_id'";

if (!mysqli_query($con, $updateleadsql)) {
    error_log("Failed to update booking_master: " . mysqli_error($con));
    header("Location: booking-view?booking_id=$booking_id&error=Failed to update booking");
    exit;
}


    // Generate installments
    if (!generateInstallments($con, $booking_id, $booking_payplan, $booking_totalamt, $booking_installdate, $booking_blockno,)) {
        error_log("Failed to generate installments for booking_id=$booking_id");
        header("Location: booking-view?booking_id=$booking_id&error=Failed to generate installments");
        exit;
    }

    // Fetch installment data for email
    $installment_query = "SELECT * FROM installment_master WHERE installment_bookingid='$booking_id' AND installment_status='Enabled' ORDER BY installment_date";
    $installment_result = mysqli_query($con, $installment_query);
    if (!$installment_result) {
        error_log("Failed to fetch installments: " . mysqli_error($con));
        header("Location: booking-view?booking_id=$booking_id&error=Failed to fetch installments");
        exit;
    }
    $taskcommentsalldata = [];
    while ($installment_data = mysqli_fetch_assoc($installment_result)) {
        $taskcommentsalldata[] = $installment_data;
    }

    // Fetch booking details
    $bookingmasterquery = mysqli_query($con, "SELECT * FROM booking_master WHERE booking_id='$booking_id'");
    if (!$bookingmasterquery || mysqli_num_rows($bookingmasterquery) == 0) {
        error_log("Failed to fetch booking details for booking_id=$booking_id");
        header("Location: booking-view?booking_id=$booking_id&error=Booking not found");
        exit;
    }
    $bookingmasterqry = mysqli_fetch_assoc($bookingmasterquery);
    $to = $bookingmasterqry['booking_email'];
    $user_fullname = $bookingmasterqry['booking_fname'] . ' ' . $bookingmasterqry['booking_lname'];
    $booking_no = $bookingmasterqry['booking_no'];
    $booking_advisor = $bookingmasterqry['booking_advisor'];
    $user_address = $bookingmasterqry['booking_address'] . ', ' . $bookingmasterqry['booking_city'] . ', ' . $bookingmasterqry['booking_state'];

    // Fetch advisor details using advisor name
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

    // Build email content
    $subject = 'Plot Details of ' . $user_fullname;
    $message = '<html><head>
<style>.bkc{
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
        </style>
    </head><body>
     <div class="top-logo">
                 </div>
    <div class="bkc">
        <p>Dated: <strong>' . date('d-m-Y', strtotime($booking_installdate)) . '</strong></p>
        <p>To,</p>
        
        <p><strong>' . $user_fullname . '</strong></p>
        <p><strong>' . $user_address . '</strong></p>
        
        <p>Dear Mr/Mrs/Ms. <strong>' . $user_fullname . '</strong></p>
        <p>Congratulations from Shaan Realtech Pvt. Ltd. on your new investment in Global Residency Homes (Phulera, Rajasthan). It is a perfect choice and you are one of the few lucky ones to get unit at such reasonable rates.</p>
        <p>We at Shaan Realtech Pvt. Ltd. feel privileged to be part of your great investment. We thank you for giving us an opportunity to assist you in making this very investment. We sincerely hope that you are satisfied with our services and will refer us in your circle.</p>
        <h3>Your Lucky Draw Allotment is as Follows:</h3>
        <p>Ticket Id: <strong>' . $booking_no . '</strong></p>
        <p>Project Name: <strong>' . $bookingmasterqry['booking_project'] . '</strong></p>
        <p>Unit Number: <strong>' . $bookingmasterqry['booking_plotno'] . '</strong></p>
        <h3>Brief details about the total cost of the unit and payment plan are as follows:</h3>
        <table border="1" style="width:100%">
            <thead>
                <tr>
                    <th style="padding: 10px 15px;">Client Name</th>
                    <th style="padding: 10px 15px;">Allotted Unit</th>
                    <th style="padding: 10px 15px;">Area (Sq. Yds.)</th>
                    <th style="padding: 10px 15px;">Payment Plan</th>
                    <th style="padding: 10px 15px;">Basic Sales Price (Per Sq. Yard)</th>
                    <th style="padding: 10px 15px;">PLC (in %)</th>
                    <th style="padding: 10px 15px;">EDC (in %)</th>
                    <th style="padding: 10px 15px;">IDC (in %)</th>
                    <th style="padding: 10px 15px;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px 15px;">' . $user_fullname . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_plotno'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_plotarea'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_payplan'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_plotrate'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_plc'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_edc'] . '</td>
                    <td style="padding: 10px 15px;">' . $bookingmasterqry['booking_idc'] . '</td>
                    <td style="padding: 10px 15px;">' . number_format($bookingmasterqry['booking_totalamt'], 2) . '</td>
                </tr>
            </tbody>
        </table>
        <h3>Payment Schedule</h3>
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
    $phliinstalment = '';
    $phliinstalmentamt = '';
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

    $message .= '</tbody></table>
        <p>Request you to transfer the initial amount of 10% (' . number_format($phliinstalmentamt, 2) . ') by ' . date('d-m-Y', strtotime($phliinstalment)) . ' in order to confirm allotment under Global Residency Homes (Phulera, Rajasthan).</p>
        <p>Note: Allotment under Global Residency Homes (Phulera, Rajasthan) will only be confirmed in case of 10% (' . number_format($phliinstalmentamt, 2) . ') payment received by ' . date('d-m-Y', strtotime($phliinstalment)) . '.</p>
        <p>In the event you fail to make the payment as per the payment plan chosen by you, then allotment of these plots will be automatically cancelled.</p>
        <p><strong>Payment can be transferred online using the following details:<br><br>
        Account Name: Shaan Realtech Pvt Ltd<br>
        Account Number: 03278630000188<br>
        Bank: HDFC BANK<br>
        IFSC CODE: HDFC0000327<br>
        </strong></p>
        <p>Your account manager is <strong>' . htmlspecialchars($aduser_name) . '</strong> and will be reachable on <strong>' . htmlspecialchars($aduser_mobile) . '</strong> for any queries.</p>
        <br><br>
        <p><strong>With Best Regards</strong></p>
        <p>
        Anurag Pathak<br>
        Accounts Manager<br>
        Shaan Realtech Pvt. Ltd.<br>
        Web: www.globalresidencyhome.com/<br>
        Corporate Office: Office-1, 2nd Floor,<br>
        A-12, A-13, Sector 16, Noida, UP 201301.<br>
        <p style="text-align:center">This is computer generated document hence requires no signature.</p> </div>
        <div class="last-part"></div>
        </body></html>';

    // Send email
    // $mail = new PHPMailer();
    // try {
    //     $mail->SMTPDebug = SMTP::DEBUG_OFF;
    //     $mail->IsSMTP();
    //     $mail->SMTPAuth = true;
    //     $mail->SMTPSecure = 'ssl';
    //     $mail->Host = "smtp.gmail.com";
    //     $mail->Port = 465;
    //     $mail->IsHTML(true);
    //     $mail->CharSet = 'UTF-8';
    //     $mail->Username = "info@shaanrealtech.com";
    //     $mail->Password = "anaqyuhlpymhozrp";
    //     $mail->SetFrom("info@shaanrealtech.com", "Shaan Realtech Pvt. Ltd.");
    //     $mail->Subject = $subject;
    //     $mail->Body = $message;
    //     $mail->AddAddress($to);
    //     $mail->AddCC('info@globalresidencyhome.com');
    //     $mail->AddCC($aduser_email);
    //     $mail->SMTPOptions = [
    //         'ssl' => [
    //             'verify_peer' => false,
    //             'verify_peer_name' => false,
    //             'allow_self_signed' => true
    //         ]
    //     ];

    //     if (!$mail->Send()) {
    //         error_log("Failed to send email: " . $mail->ErrorInfo);
    //         $error = "Failed to send email notification";
    //     } else {
    //         error_log("Email sent successfully to $to");
    //     }
    // } catch (Exception $e) {
    //     error_log("PHPMailer exception: " . $e->getMessage());
    //     $error = "Failed to send email notification due to an error";
    // }

    // Redirect back to booking-view.php
    $redirect_url = "booking-view.php?booking_id=$booking_id";
    if (isset($error)) {
        $redirect_url .= "&error=" . urlencode($error);
    }
    header("Location: $redirect_url");
    exit;
}
?>