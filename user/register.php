<?php
// Include database connection
include_once '../includes/db_connection.php';

// Initialize a variable to store the registration status message
$registration_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the username already exists in the database
    $check_username_sql = "SELECT * FROM Users WHERE username = ?";
    $stmt_check = $conn->prepare($check_username_sql);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Username already exists, display error message
        $registration_message = "Username already exists.";
    } else {
        // Username is unique, proceed with registration

        // Prepare SQL statement to insert new user into the database
        $sql = "INSERT INTO Users (username, email, password) VALUES (?, ?, ?)";

        // Prepare and bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password_hash);

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Execute the statement
        if ($stmt->execute()) {
            // User registered successfully
            $registration_message = "User registered successfully!";
            // Redirect to login page
            header("Location: login.php");
            exit;
        } else {
            // Error occurred
            $registration_message = "Error registering user: " . $conn->error;
        }

        // Close statement
        $stmt->close();
    }

    // Close statement and connection
    $stmt_check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/style1.css">
    <title>USER REGISTRATION</title>
    <style>
        .password-toggle {
            position: relative; 
        }
        .eye-icon {
            position: absolute; 
            bottom: 1px; 
            right: 5px; 
        }
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
<div class="login-container">
    <h1>REGISTER</h1><br>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <div class="password-toggle">
            <input type="password" id="password" name="password" required><br><br>
            <span class="eye-icon" onclick="togglePasswordVisibility()">👁️</span>
        </div>
        <button type="submit" class="login-button">Register</button>
        <p style="color: red"><?php echo $registration_message; ?></p>
    </form>
<a href="login.php" class="goback-button">GO BACK</a>
</div>   
</body>
</html>

