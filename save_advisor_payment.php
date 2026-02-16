<?php

include "database.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $booking_no = $_POST['booking_no'];
    $advisor_total_amt = $_POST['advisor_total_amt'];
    $advisor_id = $_POST['advisor_id'];
    $advisor_receive_amt = $_POST['advisor_receive_amt'];
    $receive_date = $_POST['receive_date'];
    $method = $_POST['method'];
    $other_method = $_POST['other_method'];
    $remark = $_POST['remark'];

    // advisor name fetch karo
    $getName = mysqli_query($con,
        "SELECT user_name FROM user_master WHERE user_id='$advisor_id'"
    );

    $row = mysqli_fetch_assoc($getName);
    $advisor_name = $row['user_name'];

    $insert = mysqli_query($con,
        "INSERT INTO advisor_payments 
        (booking_no, advisor_total_amt, advisor_name, advisor_id, 
         advisor_receive_amt, receive_date, method, other_method, remark, created_at)
         
         VALUES
        ('$booking_no','$advisor_total_amt','$advisor_name','$advisor_id',
         '$advisor_receive_amt','$receive_date','$method','$other_method','$remark',NOW())"
    );

    if ($insert) {
        echo "success";
    } else {
        echo mysqli_error($con);
    }
}
?>
