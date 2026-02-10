<?php

// DATABASE
require_once "database.php";

// USER ID
$uid = (int) $_SESSION['user_id'];

// FETCH USER
$q = mysqli_query($con, "
    SELECT user_name, user_email, user_department, user_image
    FROM user_master
    WHERE user_id = $uid
");

$user = mysqli_fetch_assoc($q) ?? [];

// SAFE VARIABLES
$user_name = $user['user_name'] ?? '';
$user_email = $user['user_email'] ?? '';
$user_department = $user['user_department'] ?? '';
$user_image = $user['user_image'] ?? '';


// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";
// include("layout/head-table.php");



?>

<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">


<!-- Page Wrapper -->
<div class="page-wrapper">

	<div class="content">

		<!-- Breadcrumb -->
		<div class="d-block text-center page-breadcrumb mb-3 pagetitle">
			<div class="my-auto">
				<!-- <h1>Advisor List</h1> -->
				<h1> <?php echo $user_department ?> List </h1>

			</div>
		</div>
		<!-- /Breadcrumb -->

		<div class="row">
			<div class="col-sm-12">


				<div class="card-body">

					<div class="table-responsive">
						<table class="table table-striped datatable ">
							<thead>
								<tr>
									<th>ID</th>
									<th>Username</th>
									<th>Email</th>
									<th>Mobile</th>
									<th>Department</th>
									<th>Password</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$id = 1;


								$usergetqry = mysqli_query($con, "SELECT * FROM user_master 
									WHERE user_id != '1'
									AND is_deleted = 0
									AND user_department = 'Admin'
									ORDER BY user_id DESC");


								while ($usergetdata = mysqli_fetch_assoc($usergetqry)) {
									?>
									<tr>
										<td><?php echo $id; ?></td>
										<td><?php echo $usergetdata['user_name'] ?></td>
										<td><?php echo $usergetdata['user_email'] ?></td>
										<td><?php echo $usergetdata['user_mobile'] ?></td>
										<td><?php echo $usergetdata['user_department'] ?></td>
										<td><?php echo $usergetdata['user_password'] ?></td>
										<td class="d-flex">
											<a href="advisor-edit.php?getuserid=<?php echo $usergetdata['user_id']; ?>"
												class="mr-1">
												<button class="btn btn-md ">
													<i class="fa fa-edit"></i>
												</button>
											</a>


											<?php
											if ($usergetdata['user_status'] == 'Enable') {
												?>
												<a href="advisor-disable.php?getuserid=<?php echo $usergetdata['user_id']; ?>&status=Disable"
													class="mr-1" onclick="return confirm('Are you sure want to Disable?')">
													<button class="btn btn-sm btn-success"> Enable </button>
												</a>
												<?php
											} else {
												?>
												<a href="advisor-disable.php?getuserid=<?php echo $usergetdata['user_id']; ?>&status=Enable"
													class="mr-1" onclick="return confirm('Are you sure want to Enable?')">
													<button class="btn btn-sm btn-danger"> Disable </button>
												</a>
												<?php
											}
											?>

											<a href="advisor-delete.php?getuserid=<?php echo $usergetdata['user_id']; ?>&delete=1"
												onclick="return confirm('Are you sure want to delete this user?')">
												<button class="btn btn-md">
													<i class="fa fa-trash"></i>
												</button>
											</a>

										</td>
									</tr>

									<?php

									$id++;
								}
								?>

							</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>


	</div>
</div>

<?php
include "layout/footer-table.php";
?>