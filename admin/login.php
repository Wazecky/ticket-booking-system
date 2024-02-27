<?php
session_start();

// Check if admin is already logged in, redirect to dashboard if true
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check username and password (you should hash passwords in a real application)
    $username = "admin";
    $password = "adminpassword";

    if($_POST['username'] == $username && $_POST['password'] == $password) {
        // Authentication successful, set session variable
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        // Authentication failed, display error message
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <?php if(isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>
    <form action="login.php" method="post"> <!-- Update form action -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" autocomplete="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
