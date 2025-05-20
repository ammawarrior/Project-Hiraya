<?php
require_once 'config/database.php';
require_once 'layout/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new Exception('Invalid CSRF token');
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Handle password update
    if (isset($_POST['new_password_modal']) || isset($_POST['confirm_password_modal'])) {
        // If this is an AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Validate password match
            if ($_POST['new_password_modal'] !== $_POST['confirm_password_modal']) {
                throw new Exception('New passwords do not match.');
            }

            // Validate password length
            if (strlen($_POST['new_password_modal']) < 8) {
                throw new Exception('New password must be at least 8 characters long.');
            }

            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($_POST['current_password_modal'], $user['password'])) {
                throw new Exception('Current password is incorrect.');
            }

            // Hash new password and update
            $hashedPassword = password_hash($_POST['new_password_modal'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password, last_password_change = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                ':password' => $hashedPassword,
                ':user_id' => $_SESSION['user_id']
            ]);

            // Commit transaction
            $pdo->commit();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
            exit;
        } else {
            // For non-AJAX requests, continue with regular form handling
            // Validate password match
            if ($_POST['new_password_modal'] !== $_POST['confirm_password_modal']) {
                throw new Exception('New passwords do not match.');
            }

            // Validate password length
            if (strlen($_POST['new_password_modal']) < 8) {
                throw new Exception('New password must be at least 8 characters long.');
            }

            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($_POST['current_password_modal'], $user['password'])) {
                throw new Exception('Current password is incorrect.');
            }

            // Hash new password and update
            $hashedPassword = password_hash($_POST['new_password_modal'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password, last_password_change = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                ':password' => $hashedPassword,
                ':user_id' => $_SESSION['user_id']
            ]);

            // Success message
            $_SESSION['success_message'] = 'Password updated successfully!';
        }
    }

    // Commit transaction
    $pdo->commit();
    header('Location: account-settings.php');
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    // Error message
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: account-settings.php');
    exit;
}
?>
