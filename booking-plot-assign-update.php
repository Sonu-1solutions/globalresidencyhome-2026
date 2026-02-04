<?php
include("database.php"); // DB connection

if (isset($_POST['formadd'])) {

    $booking_id        = $_POST['booking_id'];
    $booking_payplan   = $_POST['booking_payplan'];
    $booking_installdate = $_POST['booking_installdate'];
    $booking_plotarea  = $_POST['booking_plotarea'];
    $booking_plotrate  = $_POST['booking_plotrate'];
    $booking_plc       = $_POST['booking_plc'];
    $booking_edc       = $_POST['booking_edc'];
    $booking_idc       = $_POST['booking_idc'];
    $booking_totalamt  = $_POST['booking_totalamt'];
    $booking_plotno    = $_POST['booking_plotno'];
    $percentage        = $_POST['percentage'];
    $advisor_amount    = $_POST['advisor_amount'];
    $booking_blockno   = $_POST['booking_blockno'];

    $query = "UPDATE booking_master SET
                booking_payplan = ?,
                booking_installdate = ?,
                booking_plotarea = ?,
                booking_plotrate = ?,
                booking_plc = ?,
                booking_edc = ?,
                booking_idc = ?,
                booking_totalamt = ?,
                booking_plotno = ?,
                percentage = ?,
                advisor_amount = ?,
                booking_blockno = ?
              WHERE booking_id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param(
        "ssddddddssssi",
        $booking_payplan,
        $booking_installdate,
        $booking_plotarea,
        $booking_plotrate,
        $booking_plc,
        $booking_edc,
        $booking_idc,
        $booking_totalamt,
        $booking_plotno,
        $percentage,
        $advisor_amount,
        $booking_blockno,
        $booking_id
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('Plot Updated Successfully');
                window.location.href='booking-list.php';
              </script>";
    } else {
        echo "Update Failed!";
    }
}
?>
