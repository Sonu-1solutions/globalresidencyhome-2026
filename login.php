<?php
session_start(); //

require 'database.php';
require 'layout/head-login.php';
$error = "";

if (isset($_POST['loginsubmit'])) {

    $login_email = mysqli_real_escape_string($con, $_POST['login_email']);
    $login_password = mysqli_real_escape_string($con, $_POST['login_password']);

    if (!empty($login_email) && !empty($login_password)) {

        $loginpassword = md5($login_password);

        $query = "SELECT user_id 
                  FROM user_master 
                  WHERE user_mobile='$login_email' 
                  AND user_password='$loginpassword' 
                  LIMIT 1";

        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) === 1) {

            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['user_id'];

            //  Proper redirect
            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Your Username or Password is invalid";
        }

    } else {
        $error = "Please Enter Username and Password";
    }
}
?>



<!-- Main Wrapper -->
<div class="main-wrapper">
    <div class="container-fuild">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                <div class="col-md-4 mx-auto vh-100">


                    <form action="" method="post" class="vh-100">
                        <div class="vh-100  p-4 pb-0">
                            <div class=" mx-auto mb-5 text-center">
                                <img src="assets/img/logo.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="">
                                <div class="text-center mb-3">
                                    <h2 class="mb-2">Sign In</h2>
                                    <p class="mb-0">Please enter your details to sign in</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <div class="input-group">
                                        <input type="text" name="login_email" placeholder="Mobile No"
                                            class="form-control border-end-0">
                                        <!-- <span class="input-group-text border-start-0">
                                                <i class="ti ti-mail"></i>
                                            </span> -->
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="pass-group">
                                        <input type="password" name="login_password" placeholder="Password"
                                            class="pass-input form-control">
                                        <!-- <span class="ti toggle-password ti-eye-off"></span> -->
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" name="loginsubmit" class="btn btn-primary w-100">Sign
                                        In</button>
                                </div>


                            </div>
                            <?php
                            if ($error) {
                                ?>
                                <div class="m-t-2">
                                    <p class="text-danger"><?php echo $error; ?></p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                </div>
                </form>


            </div>

        </div>
    </div>
</div>


<?php


require 'layout/footer-login.php';

?>