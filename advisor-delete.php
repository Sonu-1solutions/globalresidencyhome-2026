


<?php
	include('database.php');

	$getuserid=$_GET['getuserid'];
	$status=$_GET['status'];
	
	$cdate=date('Y-m-d H:i:s');
	$cby=$_SESSION['user_id'];
	
	
	$sql ="UPDATE `user_master` SET `user_status`='$status',`user_changetime`='$cdate',`user_changeby`='$cby' WHERE user_id='$getuserid'";	
    mysqli_query($con, $sql);	
    
	// redirect back
header("Location: advisor-list.php");
?>