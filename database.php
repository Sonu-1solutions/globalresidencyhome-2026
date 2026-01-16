<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbHost     = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName     = "globalresidencyhome";

$con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
$con->query("SET NAMES 'UTF8'");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
