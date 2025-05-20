<?php
require_once 'config/database.php';

// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "Invalid request. Please try again.";
    header("Location: account-settings.php");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to update your profile picture.";
    header("Location: login.php");
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['user_picture']) || $_FILES['user_picture']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error_message'] = "No file was uploaded or upload failed.";
    header("Location: account-settings.php");
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file type and size
if (!in_array($_FILES['user_picture']['type'], $allowed_types)) {
    $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
    header("Location: account-settings.php");
    exit;
}

if ($_FILES['user_picture']['size'] > $max_size) {
    $_SESSION['error_message'] = "File size is too large. Maximum allowed size is 5MB.";
    header("Location: account-settings.php");
    exit;
}

// Generate a unique filename
$extension = strtolower(pathinfo($_FILES['user_picture']['name'], PATHINFO_EXTENSION));
$filename = uniqid() . "_" . time() . "." . $extension;
$target_path = "uploads/" . $filename;

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Move uploaded file
if (move_uploaded_file($_FILES['user_picture']['tmp_name'], $target_path)) {
    try {
        // Update user picture in database
        $stmt = $pdo->prepare("UPDATE users SET user_picture = :user_picture WHERE user_id = :user_id");
        $stmt->execute([
            ':user_picture' => $filename,
            ':user_id' => $_SESSION['user_id']
        ]);

        // Update session
        $_SESSION['user_picture'] = $filename;
        $_SESSION['success_message'] = "Profile picture updated successfully!";
    } catch (PDOException $e) {
        error_log("Error updating profile picture: " . $e->getMessage());
        $_SESSION['error_message'] = "Failed to update profile picture. Please try again later.";
    }
} else {
    $_SESSION['error_message'] = "Failed to upload profile picture. Please try again.";
}

header("Location: account-settings.php");
exit;
