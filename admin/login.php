<?php
session_start();

// Initialize the $password_error variable
$password_error = "";

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
        $password_error = "Invalid Username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/style1.css">
    <title>ADMIN LOGIN</title>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.querySelector(".eye-icon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.add("crossed");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("crossed");
            }
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h1>ADMIN LOGIN</h1> 
        <form action="#" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <div class="password-toggle">
                <input type="password" id="password" name="password" required>
                <span class="eye-icon" onclick="togglePasswordVisibility()">👁️</span>
            </div>
            <?php if($password_error) { ?>
                <p style="color: red" class="error-message"><?php echo $password_error; ?></p>
            <?php } ?>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>
