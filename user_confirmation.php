<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_ec.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Adjust path as needed

// Handle user status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $status = ($_POST['action'] === 'confirm') ? 1 : 3;
    $user_id = intval($_POST['user_id']);

    // Update user status
    $stmt = $conn->prepare("UPDATE users SET user_status = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $status, $user_id);
    $stmt->execute();
    $stmt->close();

    // Fetch user email and name
    $stmt = $conn->prepare("SELECT email, code_name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email, $code_name);
    $stmt->fetch();
    $stmt->close();

    // Send email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eddiemarkbryandoverte@gmail.com'; // Your Gmail address
        $mail->Password = 'uucx sptd lggg nnvl'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('eddiemarkbryandoverte@gmail.com', 'Project Hiraya');
        $mail->addAddress($email, $code_name);
        $mail->isHTML(true);
        $mail->Subject = 'Account Status Update';

        $statusText = ($status === 1) ? 'Approved' : 'Rejected';
        $color = ($status === 1) ? 'green' : 'red';

        $mail->Body = "
            <p>Hi <strong>$code_name</strong>,</p>
            <p>Your account has been <strong style='color: $color;'>$statusText</strong>.</p>
            <p>Thank you,<br>DOST Admin</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
    }
}

// Fetch unconfirmed users
$query = "SELECT user_id, code_name, user_picture, user_status, email, contact_number FROM users WHERE user_status = 0";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
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
            <?php include('includes/topnav.php'); ?>
            <?php include('includes/sidebar.php'); ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h1>New Seller Confirmations</h1>
                        <div id="current-datetime" style="font-size: 1.1rem; font-weight: 500;"></div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Pending Seller Accounts</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped v_center" id="table-1">
                                                <thead>
                                                    <tr>
                                                        <th>Picture</th>
                                                        <th>Seller Name</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            switch ($row['user_status']) {
                                                                case 0: $badge = '<div class="badge badge-warning">Pending</div>'; break;
                                                                case 1: $badge = '<div class="badge badge-warning">Pending</div>'; break;
                                                                case 2: $badge = '<div class="badge badge-success">Confirmed</div>'; break;
                                                                case 3: $badge = '<div class="badge badge-danger">Rejected</div>'; break;
                                                                // No default case needed as we've handled all statuses explicitly
                                                            }
                                                            

                                                            echo "<tr>
                                                               <td class='text-center align-middle'>
    <div style='display: flex; justify-content: center; align-items: center; height: 100%;'>
        <img src='uploads/{$row['user_picture']}' alt='User Pic' style='height: 60px; border-radius: 50%;'>
    </div>
</td>

                                                                <td>{$row['code_name']}</td>
                                                                <td>{$badge}</td>
                                                                <td>
                                                                   <button 
                                                                        class='btn btn-primary btn-detail' 
                                                                        data-id='{$row['user_id']}'
                                                                        data-name='{$row['code_name']}'
                                                                        data-picture='{$row['user_picture']}'
                                                                        data-email='{$row['email']}'
                                                                        data-contact='{$row['contact_number']}'
                                                                    >
                                                                        Details
                                                                    </button>

                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='4' class='text-center'>No new users for confirmation</td></tr>";
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

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Seller Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <img id="modalUserPic" src="" class="img-fluid mb-3 rounded-circle" style="height: 150px;">
                        <h4 id="modalUserName"></h4>
                        <p><strong>Email:</strong> <span id="modalUserEmail"></span></p>
                        <p><strong>Contact:</strong> <span id="modalUserContact"></span></p>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject ❌</button>
                        <button type="submit" name="action" value="confirm" class="btn btn-success">Confirm ✔️</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Processing Modal -->
<div class="modal fade" id="processingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content text-center p-3">
      <div class="spinner-border text-primary mb-2" role="status"></div>
      <p>Processing...</p>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content text-center p-3">
      <div class="text-success display-4 mb-2">✔️</div>
      <p>Successfully updated!</p>
    </div>
  </div>
</div>

    <!-- Scripts -->
    <script src="assets/bundles/lib.vendor.bundle.js"></script>
    <script src="js/CodiePie.js"></script>
    <script src="assets/modules/datatables/datatables.min.js"></script>
    <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
    <script src="js/page/modules-datatables.js"></script>
    <script src="js/scripts.js"></script>
    <script>

    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('modalUserEmail').textContent = this.getAttribute('data-email');
document.getElementById('modalUserContact').textContent = this.getAttribute('data-contact');

            document.getElementById('modalUserId').value = this.getAttribute('data-id');
            document.getElementById('modalUserName').textContent = this.getAttribute('data-name');
            document.getElementById('modalUserPic').src = this.getAttribute('data-picture');
            document.getElementById('modalUserPic').src = 'uploads/' + this.getAttribute('data-picture');

            $('#userModal').modal('show');
        });
    });

    </script>
    <script>
document.querySelectorAll('#userModal form').forEach(form => {
    form.addEventListener('submit', function(e) {
        $('#userModal').modal('hide');
        $('#processingModal').modal('show');
    });
});

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])): ?>
    window.addEventListener('DOMContentLoaded', function () {
        $('#processingModal').modal('hide');
        $('#successModal').modal('show');
        setTimeout(() => {
            $('#successModal').modal('hide');
            location.reload();
        }, 2000);
    });
<?php endif; ?>
</script>

</body>
</html>
