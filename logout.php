<?php
session_start();

// ✅ All session variables remove
$_SESSION = [];

// ✅ Session destroy
session_destroy();

// ✅ Login page redirect
header("Location: login.php");
exit;
?>
