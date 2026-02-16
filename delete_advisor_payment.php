<?php
include "database.php";

$id = $_POST['id'] ?? '';

if ($id == '') {
    echo "error";
    exit;
}

$delete = mysqli_query($con, 
          "DELETE FROM advisor_payments WHERE id='$id'");

if ($delete) {
    echo "success";
} else {
    echo "error";
}
?>
