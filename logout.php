<?php
session_start();
include 'db_ec.php'; // Database connection



// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];



}

// Redirect to login page
header("Location: login.php");
exit();
?>
