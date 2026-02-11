<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SESSION START
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// DATABASE
require_once 'database.php';

// USER ID
$uid = (int) $_SESSION['user_id'];

// FETCH USER
$q = mysqli_query($con, "
    SELECT user_name, user_email, user_department, user_image
    FROM user_master
    WHERE user_id = $uid
");

$user = mysqli_fetch_assoc($q) ?? [];

$user_name = $user['user_name'] ?? '';
$user_email = $user['user_email'] ?? '';
$user_department = $user['user_department'] ?? '';
$user_image = $user['user_image'] ?? '';

// PROFILE IMAGE
$uploadPath = __DIR__ . '/upload/user/' . $user_image;
$profile_img = (!empty($user_image) && file_exists($uploadPath))
    ? 'upload/user/' . $user_image
    : 'dist/img/favicon.png';

include 'layout/head.php';

/* ================= FILTER QUERY ================= */

$where = [];

if (!empty($_POST['booking_advisor'])) {
    $advisor = mysqli_real_escape_string($con, $_POST['booking_advisor']);
    $where[] = "booking_advisorid = '$advisor'";
}

if (!empty($_POST['booking_date'])) {
    $date = mysqli_real_escape_string($con, $_POST['booking_date']);
    $where[] = "booking_date = '$date'";
}

if (!empty($_POST['booking_no'])) {
    $booking_no = mysqli_real_escape_string($con, $_POST['booking_no']);
    $where[] = "booking_no = '$booking_no'";
}

if (!empty($_POST['state'])) {
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $where[] = "booking_state = '$state'";
}

if (!empty($_POST['city'])) {
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $where[] = "booking_city = '$city'";
}

if (!empty($_POST['project'])) {
    $project = mysqli_real_escape_string($con, $_POST['project']);
    $where[] = "booking_project = '$project'";
}

if (!empty($_POST['plot_type'])) {
    $plottype = mysqli_real_escape_string($con, $_POST['plot_type']);
    $where[] = "booking_plottype = '$plottype'";
}

if (!empty($_POST['plot_size'])) {
    $plotsize = mysqli_real_escape_string($con, $_POST['plot_size']);
    $where[] = "booking_plotsize = '$plotsize'";
}

if (!empty($_POST['plot_area'])) {
    $plotarea = mysqli_real_escape_string($con, $_POST['plot_area']);
    $where[] = "booking_plotarea = '$plotarea'";
}

if (!empty($_POST['plot_rate'])) {
    $plotrate = mysqli_real_escape_string($con, $_POST['plot_rate']);
    $where[] = "booking_plotrate = '$plotrate'";
}

$query = "SELECT * FROM booking_master WHERE booking_status='Enabled'";

if (!empty($where)) {
    $query .= ' AND ' . implode(' AND ', $where);
}

$query .= ' ORDER BY booking_no DESC';

$result = mysqli_query($con, $query);

?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Page Title -->
        <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <h1>Advisor Details</h1>
        </div>





        <!-- Popup / Model -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- Popup / Model -->



        <style>
            h1 {
                margin: 0px;
            }

            .modal-dialog {
                width: 90%;
                max-width: 100%;
            }

            .advisor-btn:hover {
                color: #fff;
                font-size: 15px;
            }

            .fw600 {
                font-weight: 700;
            }

            .dataTables_length {
                padding: 15px 0px 15px 15px !important;
            }

            .dataTables_filter {
                margin-bottom: 0px;
                margin-top: 13px;
                margin-right: 15px;
            }

            #example1_wrapper>div.row>div.col-md-6:last-child {
                text-align: right;
                padding-right: 15px;
            }

            div.dataTables_wrapper div.dataTables_info {
                padding: 15px 15px;
                margin-bottom: -15px;
            }

            #example1_wrapper>div.row:last-child>div.col-md-7 {
                text-align: right;
                padding-right: 15px;
            }

            .pagination {
                margin: 12px 0;
            }

            .dataTables_paginate {
                margin-top: 0px !important;
            }

            a:focus,
            a:hover {
                text-decoration: none;
            }
        </style>






        <!-- FILTER FORM -->
        <form method="post" class="mt-5 mb-5">

            <div class="row">

                <!-- <div class="col-md-2 mb-4" title="Advisor Name"> -->
                <!-- <label class="fw-bold">Advisor Name</label> -->
                <!-- <select class="form-control" name="booking_advisor">
                        <option value="">— Select Advisor Name —</option>
                        <?php
                        $advisornames = mysqli_query($con, "SELECT user_id, user_name FROM user_master WHERE user_status='Enable' AND user_department='User'");
                        while ($row = mysqli_fetch_assoc($advisornames)) {
                            $selected = (($_POST['booking_advisor'] ?? '') == $row['user_id']) ? 'selected' : '';
                            echo "<option value='{$row['user_id']}' $selected>{$row['user_name']}</option>";
                        }
                        ?>
                    </select> -->
                <!-- </div> -->

                <!-- <div class="col-md-2 mb-4" title="Booking Date">
                    <input type="date" class="form-control" name="booking_date"
                        value="<?= $_POST['booking_date'] ?? '' ?>">
                </div> -->

                <div class="col-md-2 mb-4">
                    <!-- <label class="fw-bold">Booking No</label> -->
                    <select class="form-control" name="booking_no">
                        <option value="">— Select Booking No —</option>

                        <?php
                        $q = mysqli_query(
                            $con,
                            "SELECT booking_no 
                            FROM booking_master 
                            WHERE booking_status = 'Enabled'
                            AND booking_advisorid = '$uid'"
                        );

                        while ($row = mysqli_fetch_assoc($q)) {
                            $selected = (($_POST['booking_no'] ?? '') == $row['booking_no']) ? 'selected' : '';
                            echo "<option value='{$row['booking_no']}' $selected>{$row['booking_no']}</option>";
                        }
                        ?>
                    </select>

                </div>

                <div class="col-md-2 mb-4" title="State">
                    <!-- <label class="fw-bold">State</label> -->
                    <select class="form-control" name="state">
                        <option value="">— Select State —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_state FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_state'])) {
                                $selected = (($_POST['state'] ?? '') == $row['booking_state']) ? 'selected' : '';
                                echo "<option value='{$row['booking_state']}' $selected>{$row['booking_state']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="City">
                    <!-- <label class="fw-bold">City</label> -->
                    <select class="form-control" name="city">
                        <option value="">— Select City —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_city FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_city'])) {
                                $selected = (($_POST['city'] ?? '') == $row['booking_city']) ? 'selected' : '';
                                echo "<option value='{$row['booking_city']}' $selected>{$row['booking_city']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="Project">
                    <!-- <label class="fw-bold">Project</label> -->
                    <select class="form-control" name="project">
                        <option value="">— Select Project —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_project FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_project'])) {
                                $selected = (($_POST['project'] ?? '') == $row['booking_project']) ? 'selected' : '';
                                echo "<option value='{$row['booking_project']}' $selected>{$row['booking_project']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="Plot Type">
                    <!-- <label class="fw-bold">Plot Type</label> -->
                    <select class="form-control" name="plot_type">
                        <option value="">— Select Plot Type —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_plottype FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_plottype'])) {
                                $selected = (($_POST['plot_type'] ?? '') == $row['booking_plottype']) ? 'selected' : '';
                                echo "<option value='{$row['booking_plottype']}' $selected>{$row['booking_plottype']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="Plot Size">
                    <!-- <label class="fw-bold">Plot Size</label> -->
                    <select class="form-control" name="plot_size">
                        <option value="">— Select Plot Size —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_plotsize FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_plotsize'])) {
                                $selected = (($_POST['plot_size'] ?? '') == $row['booking_plotsize']) ? 'selected' : '';
                                echo "<option value='{$row['booking_plotsize']}' $selected>{$row['booking_plotsize']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="Plot Area">
                    <!-- <label class="fw-bold">Plot Area</label> -->
                    <select class="form-control" name="plot_area">
                        <option value="">— Select Plot Area —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_plotarea FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_plotarea'])) {
                                $selected = (($_POST['plot_area'] ?? '') == $row['booking_plotarea']) ? 'selected' : '';
                                echo "<option value='{$row['booking_plotarea']}' $selected>{$row['booking_plotarea']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-4" title="Plot Rate">
                    <!-- <label class="fw-bold">Plot Rate</label> -->
                    <select class="form-control" name="plot_rate">
                        <option value="">— Select Plot Rate —</option>
                        <?php
                        $q = mysqli_query($con, "SELECT DISTINCT booking_plotrate FROM booking_master WHERE booking_status='Enabled' AND booking_advisorid = '$uid'");
                        while ($row = mysqli_fetch_assoc($q)) {
                            if (!empty($row['booking_plotrate'])) {
                                $selected = (($_POST['plot_rate'] ?? '') == $row['booking_plotrate']) ? 'selected' : '';
                                echo "<option value='{$row['booking_plotrate']}' $selected>{$row['booking_plotrate']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- <div class="col-md-2 mt-4">
                    <button type="submit" class="btn advisor-btn" style="height:45px;">
                        Submit
                    </button>
                </div> -->
                <!-- SEARCH BUTTON -->
                <div class="col-md-2" style="">
                    <button type="submit" class="btn advisor-btn w-100" style="line-height:20px;">
                        Search
                    </button>
                </div>

                <!-- RESET BUTTON -->
                <div class="col-md-2" style="">
                    <a href="" class="btn btn-secondary w-100" style="line-height:20px;">
                        Reset
                    </a>
                </div>

            </div>
        </form>


        <!-- RESULT TABLE -->
        <div class="card mt-4">


            <?php
            $showData = false;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $showData = true;
            }
            ?>


            <?php if ($showData) { ?>
                <?php

                /* ================= MULTI FILTER ================= */

                $where = [];

                // Advisor
                if (!empty($_POST['booking_advisor'])) {
                    $advisor = mysqli_real_escape_string($con, $_POST['booking_advisor']);
                    $where[] = "booking_advisorid = '$advisor'";
                }

                // Booking Date
                if (!empty($_POST['booking_date'])) {
                    $date = mysqli_real_escape_string($con, $_POST['booking_date']);
                    $where[] = "booking_date = '$date'";
                }

                // Booking No
                if (!empty($_POST['booking_no'])) {
                    $booking_no = mysqli_real_escape_string($con, $_POST['booking_no']);
                    $where[] = "booking_no = '$booking_no'";
                }

                // State
                if (!empty($_POST['state'])) {
                    $state = mysqli_real_escape_string($con, $_POST['state']);
                    $where[] = "booking_state = '$state'";
                }

                // City
                if (!empty($_POST['city'])) {
                    $city = mysqli_real_escape_string($con, $_POST['city']);
                    $where[] = "booking_city = '$city'";
                }

                // Project
                if (!empty($_POST['project'])) {
                    $project = mysqli_real_escape_string($con, $_POST['project']);
                    $where[] = "booking_project = '$project'";
                }

                // Plot Type
                if (!empty($_POST['plot_type'])) {
                    $plottype = mysqli_real_escape_string($con, $_POST['plot_type']);
                    $where[] = "booking_plottype = '$plottype'";
                }

                // Plot Size
                if (!empty($_POST['plot_size'])) {
                    $plotsize = mysqli_real_escape_string($con, $_POST['plot_size']);
                    $where[] = "booking_plotsize = '$plotsize'";
                }

                // Plot Area
                if (!empty($_POST['plot_area'])) {
                    $plotarea = mysqli_real_escape_string($con, $_POST['plot_area']);
                    $where[] = "booking_plotarea = '$plotarea'";
                }

                // Plot Rate
                if (!empty($_POST['plot_rate'])) {
                    $plotrate = mysqli_real_escape_string($con, $_POST['plot_rate']);
                    $where[] = "booking_plotrate = '$plotrate'";
                }

                // Base Query
                $query = 'SELECT * FROM booking_master';

                if (!empty($where)) {
                    $query .= ' WHERE ' . implode(' AND ', $where);
                }




                $query .= ' ORDER BY booking_no DESC';

                $result = mysqli_query($con, $query);

                /* ================= ADVISOR NAME SHOW ================= */

                $advisorname = '';
                if (!empty($_POST['booking_advisor'])) {
                    $adviserid = mysqli_real_escape_string($con, $_POST['booking_advisor']);

                    $query1 = "SELECT booking_advisor 
               FROM booking_master 
               WHERE booking_advisorid='$adviserid' 
               LIMIT 1";

                    $result1 = mysqli_query($con, $query1);

                    if ($result1 && mysqli_num_rows($result1) > 0) {
                        $row1 = mysqli_fetch_assoc($result1);
                        $advisorname = $row1['booking_advisor'];
                    }
                }
                ?>

                <!-- <?php if (!empty($advisorname)) { ?>
                <div class="">
                    <strong>Advisor Name:</strong>
                    <?php echo $advisorname; ?>
                </div>
            <?php } ?> -->


                <div class="table-responsive">
                    <table id="example1" class="table table-striped datatable">

                        <thead>
                            <tr>
                                <th>Payment slip</th>
                                <th>Advisor</th>
                                <th>ID</th>
                                <th>Booking Date</th>
                                <th>Booking No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Aadhar No</th>
                                <th>PAN No</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Project</th>
                                <th>Plot Type</th>
                                <th>Plan</th>
                                <th>Plot No</th>
                                <th>Plot Area</th>
                                <th>Plot Rate</th>
                                <th>Total Amount</th>
                                <th>Receive Amount</th>
                                <th>%</th>
                                <th>Pending</th>
                                <th>Status</th>
                                <th>Advisor Amount</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $paymentslipno = $row['booking_no'];

                                    $qry1 = "SELECT 
                    SUM(total_amout) AS total_received,
                    SUM(advisor_amount) AS total_brokageamt
                 FROM payment_slip
                 WHERE registration_number = '$paymentslipno'";

                                    $res1 = mysqli_query($con, $qry1);

                                    $totalreceiveamt1 = 0;

                                    if ($res1) {
                                        $sumrow = mysqli_fetch_assoc($res1);
                                        $totalreceiveamt1 = (float) ($sumrow['total_received'] ?? 0);
                                    }

                                    $bokingno = $row['booking_no'];
                                    ?>

                                    <tr id="row-<?php echo $row['booking_id']; ?>">

                                        <td>
                                            <a href="#" class="btn btn-info" data-toggle="modal"
                                                data-target="#myModal<?= $bokingno ?>">View</a>
                                        </td>

                                        <td><?= $row['booking_advisor']; ?></td>
                                        <td><?= $row['booking_id']; ?></td>
                                        <td><?= $row['booking_date']; ?></td>
                                        <td><?= $row['booking_no']; ?></td>
                                        <td><?= $row['booking_fname'] . ' ' . $row['booking_lname']; ?></td>
                                        <td><?= $row['booking_phone']; ?></td>
                                        <td><?= $row['booking_email']; ?></td>
                                        <td><?= $row['booking_aadharno']; ?></td>
                                        <td><?= $row['booking_panno']; ?></td>
                                        <td><?= $row['booking_state']; ?></td>
                                        <td><?= $row['booking_city']; ?></td>
                                        <td><?= $row['booking_address']; ?></td>
                                        <td><?= $row['booking_project']; ?></td>
                                        <td><?= $row['booking_plottype']; ?></td>
                                        <td><?= $row['booking_payplan']; ?></td>
                                        <td><?= $row['booking_plotno']; ?></td>
                                        <td><?= $row['booking_plotarea']; ?></td>
                                        <td><?= $row['booking_plotrate']; ?></td>
                                        <td><?= $row['booking_totalamt']; ?></td>

                                        <td><?= $totalreceiveamt1 ?></td>

                                        <td>
                                            <?=
                                                ($row['booking_totalamt'] > 0)
                                                ? number_format(min(($totalreceiveamt1 / $row['booking_totalamt']) * 100, 100), 2) . '%'
                                                : '0%'
                                                ?>
                                        </td>

                                        <td><?= ($row['booking_totalamt'] - $totalreceiveamt1); ?></td>

                                        <td>
                                            <?php
                                            $total = (float) $row['booking_totalamt'];
                                            $received = (float) $totalreceiveamt1;

                                            echo ($total > 0 && $received >= $total)
                                                ? 'Completed'
                                                : 'Pending';
                                            ?>
                                        </td>

                                        <td><?= $row['advisor_amount']; ?></td>
                                        <td><?= $row['booking_createat']; ?></td>

                                    </tr>



                                    <!-- Payment Slip Models -->
                                    <div class="modal fade" id="myModal<?= $bokingno ?>" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Plot No <?= $bokingno ?> </h4>
                                                </div>
                                                <div class="modal-body ">


                                                    <?php

                                                    $query1 = "SELECT * FROM `payment_slip` WHERE `registration_number`='$bokingno' Order BY `id` ASC limit 1";
                                                    $result1 = mysqli_query($con, $query1);

                                                    if ($result1 && mysqli_num_rows($result1) > 0) {
                                                        while ($row1 = mysqli_fetch_assoc($result1)) {
                                                            ?>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-1 bgb">
                                                                        <span class="fw600">Reg. No</span><br>
                                                                        <?php echo $row1['registration_number']; ?>
                                                                    </div>
                                                                    <div class="col-md-1 bgr">
                                                                        <span class="fw600">Plot No</span><br>
                                                                        <?php echo $row1['plot_no']; ?>
                                                                    </div>
                                                                    <div class="col-md-2 bgb">
                                                                        <span class="fw600">Plot Size</span><br>
                                                                        <?php echo $row1['plot_size']; ?>
                                                                    </div>
                                                                    <div class="col-md-2 bgb">
                                                                        <span class="fw600">Project Name</span><br>
                                                                        <?php echo $row1['project_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-2 bgr">
                                                                        <span class="fw600">Receive Name</span><br>
                                                                        <?php echo $row1['receive_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-2 bgb">
                                                                        <span class="fw600">Advisor Percentage</span><br>
                                                                        <!-- <?php echo $row1['percentage']; ?> -->
                                                                    </div>
                                                                    <div class="col-md-2 bgb">
                                                                        <span class="fw600">Advisor Amount</span><br>
                                                                        <!-- <?php echo $row1['advisor_amount']; ?> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>


                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<center><h5>Payment Installments not created yet</h5><center>';
                                                    }
                                                    ?>
                                                    <hr>

                                                    <?php

                                                    $query1 = "SELECT * FROM `payment_slip` WHERE `registration_number`='$bokingno'";
                                                    $result1 = mysqli_query($con, $query1);

                                                    if ($result1 && mysqli_num_rows($result1) > 0) {
                                                        while ($row1 = mysqli_fetch_assoc($result1)) {
                                                            ?>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-3 bgr">
                                                                        <span class="fw600">Payment Date</span>
                                                                        <?php echo $row1['payment_by_date']; ?>
                                                                    </div>
                                                                    <div class="col-md-3 bgb">
                                                                        <span class="fw600">Date</span>
                                                                        <?php echo $row1['current_date']; ?>
                                                                    </div>
                                                                    <div class="col-md-3 bgr">
                                                                        <span class="fw600">Slip No</span>
                                                                        <?php echo $row1['slip_id']; ?>
                                                                    </div>
                                                                    <div class="col-md-3 bgb">
                                                                        <span class="fw600">Payment By</span>
                                                                        <?php echo $row1['payment_by']; ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 mt-3">
                                                                    <div class="col-md-3 bgb">
                                                                        <span class="fw600">Drawn On</span>
                                                                        <?php echo $row1['drawn_on']; ?>
                                                                    </div>
                                                                    <div class="col-md-3 bgr">
                                                                        <span class="fw600">Total Ammount</span>
                                                                        <?php echo $row1['total_amout']; ?>
                                                                    </div>
                                                                    <div class="col-md-6 bgr">
                                                                        <span class="fw600">Ammount in Word</span>
                                                                        <?php echo $row1['amount_in_word']; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<center><h5>Payment Installments not created yet</h5><center>';
                                                    }
                                                    ?>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <!-- Payment Slip -->



                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='26' class='text-center'>No Records Found</td></tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>


            <?php } else {
                echo "<div class='alert alert-info text-center'><h5>Please select filter and click <b>Search</b> to view booking data.</h5></div>";
            } ?>




        </div>

    </div>
</div>

<?php include 'layout/footer-table.php'; ?>