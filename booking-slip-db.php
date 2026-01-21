<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
include('database.php');
 
if (isset($_POST['slipbtn'])) {
 
    // Generate random slip number (temporary)
    $slip_no = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
 
    // Collect POST data
    $registrationNumber = $_POST['registrationNumber'] ?? '';
    $currentDate        = $_POST['currentDate'] ?? '';
    $receiveName        = $_POST['receiveName'] ?? '';
    $sumAmount          = $_POST['sumAmount'] ?? '';
    $paymentby          = $_POST['paymentby'] ?? '';
    $drawnon            = $_POST['drawnon'] ?? '';
    $chequedate         = $_POST['chequedate'] ?? '';
    $projectname        = $_POST['projectname'] ?? '';
    $plotno             = $_POST['plotno'] ?? '';
    $plotsize           = $_POST['plotsize'] ?? '';
    $totalamout         = $_POST['totalamout'] ?? '';
 
    // ✅ NEW FIELDS
    $percentage         = $_POST['percentage'] ?? '';
    $advisoramount      = $_POST['ammount'] ?? '';
 
    // Insert Query
    $stmt = $con->prepare("
    INSERT INTO payment_slip 
    (
        registration_number,
        slip_id,
        `current_date`,
        receive_name,
        amount_in_word,
        payment_by,
        drawn_on,
        payment_by_date,
        project_name,
        plot_no,
        plot_size,
        total_amout,
        percentage,
        advisor_amount
    )
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

 
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }
 
    // Bind parameters
    $stmt->bind_param(
        "ssssssssssssss",
        $registrationNumber,
        $slip_no,
        $currentDate,
        $receiveName,
        $sumAmount,
        $paymentby,
        $drawnon,
        $chequedate,
        $projectname,
        $plotno,
        $plotsize,
        $totalamout,
        $percentage,
        $advisoramount
    );
 
    // Execute insert
    if ($stmt->execute()) {
 
        $slipid = $stmt->insert_id;
        $newslipid = 6400 + $slipid;
 
        // Update slip_id
        $update_stmt = $con->prepare("UPDATE payment_slip SET slip_id=? WHERE id=?");
 
        if ($update_stmt === false) {
            die("Update prepare failed: " . $con->error);
        }
 
        $update_stmt->bind_param("ii", $newslipid, $slipid);
 
        if ($update_stmt->execute()) {
            $redirect_url = "booking-slip-list.php?slip_id=" . urlencode($registrationNumber);
            echo "<script>window.location='{$redirect_url}';</script>";
        } else {
            echo "Slip ID update error: " . $update_stmt->error;
        }
 
        $update_stmt->close();
 
    } else {
        echo "Insert error: " . $stmt->error;
    }
 
    $stmt->close();
}
 
$con->close();
?>