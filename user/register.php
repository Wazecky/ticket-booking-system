<?php
// Include database connection
include_once '../includes/db_connection.php';

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to insert new user into the database
    $sql = "INSERT INTO Users (username, email, password) VALUES (?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password_hash);

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Execute the statement
    if($stmt->execute()) {
        // User registered successfully
        echo "User registered successfully!";
    } else {
        // Error occurred
        echo "Error registering user: " . $conn->error;
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
    <title>User Registration</title>
</head>
<body>
    <h2>User Registration</h2>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
