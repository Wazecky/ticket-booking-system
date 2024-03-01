<?php
// Include database connection
include_once '../includes/db_connection.php';

// Initialize variables for error messages
$username_error = "";
$password_error = "";

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to retrieve user from the database
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])) {
            // Start session and store user ID
            session_start();
            $_SESSION['user_id'] = $user['id'];
            // Redirect to events page
            header("Location: events.php");
            exit;
        } else {
            $password_error = "Invalid password!";
        }
    } else {
        $username_error = "User not found!";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/style1.css">
    <title>USER LOGIN</title>
    <style>
        .eye-icon.crossed {
            text-decoration: line-through;
        }
        .goback-button {
            display: block;
            margin: 0 auto;
            padding: 10px 20px;
            color: #009999;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
    </style>
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
<?php //include_once '../admin/nav.php'; ?> 
    <div class="login-container">
        <h1>LOGIN</h1> <br>
        <form action="#" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <div class="password-toggle">
                <input type="password" id="password" name="password" required>
                <span class="eye-icon" onclick="togglePasswordVisibility()">👁️</span>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="register.php">Sign up</a></p>
        <a href="../index.php" class="goback-button">GO BACK</a>
        <?php if($username_error) { ?>
                <p style="color: red" class="error-message"><?php echo $username_error; ?></p>
            <?php } ?>
            <?php if($password_error) { ?>
                <p style="color: red" class="error-message"><?php echo $password_error; ?></p>
            <?php } ?>
    </div>
</body>
</html>
