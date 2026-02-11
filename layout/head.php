<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Global Residency">
    <meta name="keywords" content="Global Residency">
    <meta name="author" content="Global Residency">
    <meta name="robots" content="noindex, nofollow">
    <title>Global Residency</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

    <!-- Theme Script js -->
    <!-- <script src="assets/js/theme-script.js"></script> -->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.min.css">

    <!-- Summernote CSS -->
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="assets/plugins/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="assets/plugins/@simonwep/pickr/themes/nano.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mystyle.css">

</head>

<style>
    .avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm {
    width: 36px;
    height: 36px;
}

.avatar-lg {
    width: 64px;
    height: 64px;
}

.avatar-title {
    width: 80%;
    height: 80%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    text-transform: uppercase;
}

.avatar-lg .avatar-title {
    font-size: 26px;
}

</style>

<body>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    /* 🔐 Login check */
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    /* ✅ Database connection */
    require_once 'database.php';

    $uid = $_SESSION['user_id'];

    $departmentare = $_SESSION['user_department'];

    /* ✅ Safe query */
    $q = mysqli_query($con, "
    SELECT user_name, user_email, user_department, user_image 
    FROM user_master 
    WHERE user_id='$uid'
");

    $user = mysqli_fetch_assoc($q);


    /* ===== PROFILE IMAGE FIX (ROOT BASED) ===== */

$user_name  = $user['user_name'] ?? '';
$user_image = $user['user_image'] ?? '';

$first_letter = strtoupper(substr($user_name, 0, 1));

if (!empty($user_image) && file_exists("upload/user/" . $user_image)) {
    $profile_img = "upload/user/" . $user_image;
    $has_image = true;
} else {
    $has_image = false;
}




    ?>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <div class="main-header">

                <div class="header-left">
                    <a href="index.html" class="logo">
                        <img src="assets/img/logo.png" alt="Logo">
                    </a>
                    <a href="index.html" class="dark-logo">
                        <img src="assets/img/logo-white.svg" alt="Logo">
                    </a>
                </div>

                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>

                <div class="header-user">
                    <div class="nav user-menu nav-list">

                        <div class="me-auto d-flex align-items-center" id="header-search">
                            <a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
                                <i class="ti ti-arrow-bar-to-left"></i>
                            </a>
                        </div>


                        <div class="d-flex align-items-center">

                            <div class="dropdown profile-dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <span class="avatar avatar-sm online">
    <?php if ($has_image) { ?>
        <img src="<?= $profile_img . '?v=' . time(); ?>" class="img-fluid rounded-circle">
    <?php } else { ?>
        <span class="avatar-title rounded-circle bg-primary text-white fw-bold">
            <?= $first_letter ?>
        </span>
    <?php } ?>
</span>

                                    <h5 class="mb-0"><?php echo $user['user_name']; ?></h5>
                                </a>
                                <div class="dropdown-menu shadow-none">
                                    <div class="card mb-0">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg me-2 avatar-rounded">
                                                   <?php if ($has_image) { ?>
    <img src="<?= $profile_img . '?v=' . time(); ?>" class="img-fluid rounded-circle">
<?php } else { ?>
    <span class="avatar-title avatar-lg rounded-circle bg-primary text-white fw-bold">
        <?= $first_letter ?>
    </span>
<?php } ?>

                                                </span>
                                                <div>
                                                    <h5 class="mb-0"><?php echo $user['user_name']; ?></h5>
                                                    <!-- <p class="fs-12 fw-medium mb-0">warren@example.com</p> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="profile-edit.php">
                                                <i class="ti ti-user-circle me-1"></i>Edit Profile
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="change-password.php">
                                                <i class="ti ti-lock me-1"></i>Change Password
                                            </a>
                                        </div>
                                        <div class="card-footer py-1">
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="logout.php"><i class="ti ti-login me-2"></i>Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div class="dropdown mobile-user-menu">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="profile-edit.php">Edit Profile</a>
                        <a class="dropdown-item" href="change-password.php">Change Password</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </div>
                <!-- /Mobile Menu -->

            </div>

        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <a href="#" class="logo logo-normal logo">
                    <img src="assets/img/shaan.png" alt="Logo" style="width: 100%; ">
                </a>
                <a href="index.html" class="logo-small">
                    <img src="assets/img/favicon.png" alt="Logo">
                </a>
                <!-- <a href="index.html" class="dark-logo">
                    <img src="assets/img/logo-white.svg" alt="Logo">
                </a> -->
            </div>
            <!-- /Logo -->





            <!-- Main Menu -->
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <!-- <li class="menu-title"><span>MAIN MENU</span></li> -->
                        <!-- <li>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);" class="active subdrop">
                                        <i class="ti ti-smart-home"></i><span>Dashboard 1</span><span
                                            class="badge badge-danger fs-10 fw-medium text-white p-1">Hot</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="index.html">Admin Dashboard</a></li>
                                    </ul>
                                </li>
                               
                            </ul>
                        </li> -->

                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        ?>

                        <!-- 
                        <li>
                            <ul>

                                <?php
                                if ($departmentare == 'Admin') {
                                    $dashboardPage = 'dashboard.php';
                                } elseif ($departmentare == 'User') {
                                    $dashboardPage = 'dashboard-user.php';
                                } elseif ($departmentare == 'Moderate') {
                                    $dashboardPage = 'dashboard-moderate.php';
                                } else {
                                    header("Location: login.php");
                                    exit;
                                }
                                ?>

                                <li class="<?= ($currentPage == basename($dashboardPage)) ? 'active' : '' ?>">
                                    <a href="<?= $dashboardPage ?>">
                                        <i class="ti ti-home"></i>
                                        <span><?= $departmentare ?> Dashboard</span>
                                    </a>
                                </li>


                                <li class="<?= ($currentPage == 'advisor-list.php') ? 'active' : '' ?>">
                                    <a href="advisor-list.php">
                                        <i class="ti ti-user"></i><span>Advisor</span>
                                    </a>
                                </li>

                                <li class="<?= ($currentPage == 'admin-list.php') ? 'active' : '' ?>">
                                    <a href="admin-list.php">
                                        <i class="ti ti-user"></i><span>Admin</span>
                                    </a>
                                </li>

                                <li class="<?= ($currentPage == 'advisor-details.php') ? 'active' : '' ?>">
                                    <a href="advisor-details.php">
                                        <i class="ti ti-details"></i><span>Advisor Details</span>
                                    </a>
                                </li>

                                <li class="<?= ($currentPage == 'booking-list.php') ? 'active' : '' ?>">
                                    <a href="booking-list.php">
                                        <i class="ti ti-details"></i><span>Online Booking</span>
                                    </a>
                                </li>


                            </ul>
                        </li> -->



                        <?php
                        if ($departmentare == 'Admin') {
                            ?>

                            <li>
                                <ul>

                                    <?php
                                    $dashboardPage = 'dashboard.php';
                                    ?>

                                    <li class="<?= ($currentPage == basename('dashboard.php')) ? 'active' : '' ?>">
                                        <a href="dashboard.php">
                                            <i class="ti ti-home"></i>
                                            <span><?= $departmentare ?> Dashboard</span>
                                        </a>
                                    </li>


                                    <li class="<?= ($currentPage == 'advisor-list.php') ? 'active' : '' ?>">
                                        <a href="advisor-list.php">
                                            <i class="ti ti-users"></i><span>Advisor</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'admin-list.php') ? 'active' : '' ?>">
                                        <a href="admin-list.php">
                                            <i class="ti ti-user"></i><span>Admin</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'admin-moderate-list.php') ? 'active' : '' ?>">
                                        <a href="admin-moderate-list.php">
                                            <i class="ti ti-user-cog"></i> <span>Moderate</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'advisor-details.php') ? 'active' : '' ?>">
                                        <a href="advisor-details.php">
                                            <i class="ti ti-search"></i><span>Search Details</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'booking-list.php') ? 'active' : '' ?>">
                                        <a href="booking-list.php">
                                            <i class="ti ti-details"></i><span>Online Booking</span>
                                        </a>
                                    </li>


                                </ul>
                            </li>

                            <?php
                        } elseif ($departmentare == 'User') {
                            ?>

                            <li>
                                <ul>



                                    <li class="<?= ($currentPage == basename('dashboard-user.php')) ? 'active' : '' ?>">
                                        <a href="dashboard-user.php">
                                            <i class="ti ti-home"></i>
                                            <span><?= $departmentare ?> Dashboard</span>
                                        </a>
                                    </li>



                                    <li class="<?= ($currentPage == 'advisor-details-user.php') ? 'active' : '' ?>">
                                        <a href="advisor-details-user.php">
                                            <i class="ti ti-search"></i><span>Search Details</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'booking-list-user.php') ? 'active' : '' ?>">
                                        <a href="booking-list-user.php">
                                            <i class="ti ti-details"></i><span>Booking List</span>
                                        </a>
                                    </li>


                                </ul>
                            </li>

                            <?php
                        } elseif ($departmentare == 'Moderate') {
                            ?>

                            <li>
                                <ul>


                                    <li class="<?= ($currentPage == basename('dashboard-moderate.php')) ? 'active' : '' ?>">
                                        <a href="dashboard-moderate.php">
                                            <i class="ti ti-home"></i>
                                            <span><?= $departmentare ?> Dashboard</span>
                                        </a>
                                    </li>


                                    <li class="<?= ($currentPage == 'moderate-list.php') ? 'active' : '' ?>">
                                        <a href="moderate-list.php">
                                            <i class="ti ti-users"></i> <span>Advisor</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'moderate-admin-list.php') ? 'active' : '' ?>">
                                        <a href="moderate-admin-list.php">
                                            <i class="ti ti-user"></i> <span>Admin</span>
                                        </a>
                                    </li>
                                    </li>

                                    <li class="<?= ($currentPage == 'total-moderate-list.php') ? 'active' : '' ?>">
                                        <a href="total-moderate-list.php">
                                            <i class="ti ti-user-cog"></i> <span>Moderate</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'advisor-details.php') ? 'active' : '' ?>">
                                        <a href="advisor-details.php">
                                            <i class="ti ti-search"></i><span>Advisor Details</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($currentPage == 'moderate-booking-list.php') ? 'active' : '' ?>">
                                        <a href="moderate-booking-list.php">
                                            <i class="ti ti-details"></i><span>Online Booking</span>
                                        </a>
                                    </li>


                                </ul>
                            </li>

                            <?php
                        } else {
                            header("Location: login.php");
                            exit;
                        }
                        ?>



                    </ul>
                </div>
            </div>
            <!-- // Main Menu -->




        </div>
        <!-- /Sidebar -->

