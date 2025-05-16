<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_ec.php';

// Define role mapping
$roles = [
    1 => 'Admin',
    2 => 'Seller'
];

// Fetch users
$query = "SELECT user_id, username, email, role, code_name, user_picture, created_at FROM users WHERE user_status = 1";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>

    <!-- PAGE SPECIFIC CSS -->
    <link rel="stylesheet" href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
    <link rel="icon" type="image/png" href="assets/img/dost.png">
</head>
<body class="layout-4">
    <div class="page-loader-wrapper">
        <span class="loader"><span class="loader-inner"></span></span>
    </div>

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <?php include('includes/topnav.php'); include('includes/sidebar.php'); ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Manage Users</h1>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4>User Overview</h4>
                                        <button class="btn" style="background-color: #557030; color: white;" onclick="window.location.href='signup_admin.php'">Create a New User</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped v_center" id="table-1">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Profile</th>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Code Name</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            $roleName = $roles[$row['role']] ?? 'Unknown';

                                                            // Fix image path (load from uploads folder)
                                                            $profilePic = !empty($row['user_picture']) && file_exists("uploads/{$row['user_picture']}")
                                                                ? "<img src='uploads/{$row['user_picture']}' alt='Profile' width='50' class='rounded-circle'>"
                                                                : "<img src='assets/img/default.png' alt='Default' width='50' class='rounded-circle'>";

                                                            echo "<tr>
                                                                <td>{$row['user_id']}</td>
                                                                <td>{$profilePic}</td>
                                                                <td>{$row['username']}</td>
                                                                <td>{$row['email']}</td>
                                                                <td>{$roleName}</td>
                                                                <td>{$row['code_name']}</td>
                                                                <td>{$row['created_at']}</td>
                                                                <td>
                                                                    <a href='manage_user_details.php?id={$row['user_id']}' class='btn' style='background-color: #557030; color: white;'>Manage</a>
                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='8' class='text-center'>No users found</td></tr>";
                                                    }
                                                    $conn->close();
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <script src="assets/bundles/lib.vendor.bundle.js"></script>
    <script src="js/CodiePie.js"></script>
    <script src="assets/modules/datatables/datatables.min.js"></script>
    <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
    <script src="assets/modules/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/modules/sweetalert/sweetalert.min.js"></script>
    <script src="js/page/modules-datatables.js"></script>
    <script src="js/page/modules-sweetalert.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
