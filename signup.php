<?php
require_once 'db_ec.php';

$message = "";
$messageType = ""; // success or danger

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = (int) $_POST['role'];
    $code_name = htmlspecialchars(trim($_POST['code_name']));
    $user_status = 0;
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));

    $user_picture = null;

    // Check for duplicate username or email
    $check_sql = "SELECT username FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "Username or email already exists. Please try again.";
        $messageType = "danger";
    } else {
        // Upload image if provided
        if (isset($_FILES['user_picture']) && $_FILES['user_picture']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $fileTmpPath = $_FILES['user_picture']['tmp_name'];
            $originalName = basename($_FILES['user_picture']['name']);
            $safeName = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $originalName);
            $fileName = uniqid("img_", true) . "_" . $safeName;
            $targetFile = $targetDir . $fileName;

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileMimeType = mime_content_type($fileTmpPath);

            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                $message = "Only JPG, PNG, GIF, or WEBP images are allowed.";
                $messageType = "danger";
            } elseif (move_uploaded_file($fileTmpPath, $targetFile)) {
                $user_picture = $fileName;
            } else {
                $message = "Failed to upload image. Please try again.";
                $messageType = "danger";
            }
        }

        if (empty($message)) {
            $sql = "INSERT INTO users (username, email, password, role, code_name, user_picture, user_status, contact_number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssisssi", $username, $email, $password, $role, $code_name, $user_picture, $user_status, $contact_number);
                if ($stmt->execute()) {
                    $message = "User registered successfully! You may now login.";
                    $messageType = "success";
                } else {
                    $message = "Registration failed: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
            }
        }
    }

    $check_stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);
        background-size: 400% 400%;
        animation: gradientAnimation 20s ease infinite;
        height: 100vh;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    @keyframes gradientAnimation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .signup-container {
        max-width: 700px;
        width: 100%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        color: white;
        text-align: center;
        margin-bottom: 30px;
    }

    form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        color: white;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .form-group input {
        padding: 10px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.3);
        color: white;
        font-size: 14px;
        outline: none;
    }

    .form-group input::file-selector-button {
        background-color: #388E3C;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
    }

    .form-group input:focus {
        background: rgba(255, 255, 255, 0.4);
    }

    .form-actions {
        grid-column: span 2;
        text-align: center;
        margin-top: 20px;
    }

    .btn-primary {
        background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);
        color: white;
        padding: 12px 20px;
        border: none;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        max-width: 250px;
        transition: transform 0.3s ease;
    }

    .btn-primary:hover {
        transform: scale(1.05);
    }

    .signup-link {
        text-align: center;
        margin-top: 15px;
        color: white; /* Change the text color to white */
    }

    .signup-link a {
        color:rgb(202, 234, 203); /* Change the link color torgb(143, 249, 148) */
        text-decoration: none;
        font-weight: bold;
    }

    .logo {
        display: block;
        margin: 0 auto 20px;
        width: 100px;
        border-radius: 50%; /* Optional: makes it circular if your logo is square */
    background: white; /* Makes the logo stand out */
    padding: 10px; /* Adds space inside the logo */
    box-shadow: 0 8px 16px rgba(0, 100, 0, 0.3); /* Subtle green glow */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .logo:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 24px rgba(76, 175, 80, 0.6); /* Brighter green glow on hover */
}

    /* ✅ Mobile responsiveness */
    @media (max-width: 768px) {
        form {
            grid-template-columns: 1fr;
        }
        .form-actions {
            grid-column: span 1;
        }
    }
    .form-group select {
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.3);
    color: white;
    font-size: 14px;
    outline: none;
}

.form-group select:focus {
    background: #97c799;
}

</style>

</head>
<body>

<?php if (!empty($message)) : ?>
<!-- Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-<?php echo $messageType; ?>">
      <div class="modal-header bg-<?php echo $messageType; ?> text-white">
        <h5 class="modal-title" id="feedbackModalLabel">
          <?php echo $messageType === "success" ? "Success!" : "Oops!"; ?>
        </h5>
      </div>
      <div class="modal-body">
        <?php echo $message; ?>
      </div>
      <div class="modal-footer">
        <?php if ($messageType === "success") : ?>
          <a href="login.php" class="btn btn-success">Go to Login</a>
        <?php else: ?>
          <a href="signup.php" class="btn btn-danger">Back to Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Trigger Modal on Page Load -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
  feedbackModal.show();
</script>
<?php endif; ?>




    <div class="signup-container">
        <img src="assets/img/logo.png" alt="Logo" class="logo">
        <h2>Project Hiraya - Sign Up</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="code_name">Fullname</label>
                <input type="text" name="code_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" name="contact_number" required>
            </div>

            <div class="form-group">
                <label for="role">Account type</label>
                <select name="role" required>
                    <option value="3">Member</option>
                    <option value="2">Innovator</option>
                </select>
            </div>

            <div class="form-group">
                <label for="user_picture">Profile Picture</label>
                <input type="file" name="user_picture" accept="image/*" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Sign Up</button>
            </div>
        </form>

        <div class="signup-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const inputs = document.querySelectorAll("input[required]");
            for (let input of inputs) {
                if (input.value.trim() === "") {
                    alert("Please fill out all required fields.");
                    input.focus();
                    return false;
                }
            }
            return true;
        }
    </script>

</body>
</html>
