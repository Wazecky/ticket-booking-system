<?php
// Start session
session_start();

// Check if admin is not logged in, redirect to login page if true
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
