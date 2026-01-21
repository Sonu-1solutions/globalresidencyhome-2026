<?php
include 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['update_btn'])) {

    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $name    = mysqli_real_escape_string($con, $_POST['name']);
    $email   = mysqli_real_escape_string($con, $_POST['email']);
    $mobile  = mysqli_real_escape_string($con, $_POST['mobile']);

    /* ✅ CORRECT ROOT PATH */
    $upload_dir = __DIR__ . '/upload/user/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    /* ===== IMAGE UPLOAD ===== */
    if (!empty($_FILES['profile_image']['name'])) {

        $img_name = time() . '_' . basename($_FILES['profile_image']['name']);
        $tmp_name = $_FILES['profile_image']['tmp_name'];

        if (move_uploaded_file($tmp_name, $upload_dir . $img_name)) {

            $update = "
                UPDATE user_master SET 
                user_name='$name',
                user_email='$email',
                user_mobile='$mobile',
                user_image='$img_name'
                WHERE user_id='$user_id'
            ";

            $_SESSION['user_image'] = $img_name;

        } else {
            die("❌ Image upload failed");
        }

    } else {

        $update = "
            UPDATE user_master SET 
            user_name='$name',
            user_email='$email',
            user_mobile='$mobile'
            WHERE user_id='$user_id'
        ";
    }

    if (mysqli_query($con, $update)) {

        $_SESSION['user_name']   = $name;
        $_SESSION['user_email']  = $email;
        $_SESSION['user_mobile'] = $mobile;

        header("Location: profile-edit.php?success=1");
        exit;

    } else {
        echo "❌ Update Failed: " . mysqli_error($con);
    }
}
?>
