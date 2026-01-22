<?php
// ================= SESSION START =================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================= DATABASE =================
include('database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['usergetadd'])) {

    $usergetid = (int)$_POST['usergetid'];

    $user_name       = mysqli_real_escape_string($con, $_POST['user_name']);
    $user_email      = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_mobile     = mysqli_real_escape_string($con, $_POST['user_mobile']);
    $user_department = mysqli_real_escape_string($con, $_POST['user_department']);

    $cdate = date('Y-m-d H:i:s');
    $cby   = $_SESSION['user_id'];

    /* ================= IMAGE UPLOAD ================= */
    $user_image = '';

    if (!empty($_FILES['profile_image']['name'])) {

        $folder = "upload/user/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $user_image = time() . "." . $ext;

        move_uploaded_file(
            $_FILES['profile_image']['tmp_name'],
            $folder . $user_image
        );
    }

    /* ================= INSERT ================= */
    if ($usergetid == 0) {

        // New user password required
        $user_password = md5($_POST['user_password']);

        $sql = "INSERT INTO user_master 
        (user_name, user_email, user_password, user_department, user_image,
         user_createtime, user_createby, user_changeby, user_changetime, user_mobile)
        VALUES
        ('$user_name','$user_email','$user_password','$user_department','$user_image',
         '$cdate','$cby','$cby','$cdate','$user_mobile')";

        if (!mysqli_query($con, $sql)) {
            die("Insert Error: " . mysqli_error($con));
        }
    }

    /* ================= UPDATE ================= */
    else {

        // Password update only if filled
        $pass_sql = '';
        if (!empty($_POST['user_password'])) {
            $new_password = md5($_POST['user_password']);
            $pass_sql = ", user_password='$new_password'";
        }

        // Image update only if uploaded
        $img_sql = ($user_image != '') ? ", user_image='$user_image'" : "";

        $sql = "UPDATE user_master SET
            user_name='$user_name',
            user_email='$user_email',
            user_department='$user_department',
            user_mobile='$user_mobile'
            $pass_sql
            $img_sql,
            user_changeby='$cby',
            user_changetime='$cdate'
            WHERE user_id='$usergetid'";

        if (!mysqli_query($con, $sql)) {
            die("Update Error: " . mysqli_error($con));
        }
    }

    echo '<script>window.location="advisor-list.php";</script>';
}
?>
