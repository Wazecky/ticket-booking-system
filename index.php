<?php
session_start();

if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit;
} elseif(isset($_SESSION['user_id'])) {
    header("Location: user/events.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Booking System</title>
</head>
<body>
    <h1>Welcome to Ticket Booking System</h1>
    <p>Please login as an admin or user.</p>
    <ul>
        <li><a href="admin/login.php">Admin Login</a></li>
        <li><a href="user/login.php">User Login</a></li>
        <li><a href="user/register.php">User Registration</a></li>
    </ul>
</body>
</html>
