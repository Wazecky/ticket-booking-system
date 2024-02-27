<?php
// Include database connection
include_once '../includes/db_connection.php';

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
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
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
    <title>User Login</title>
</head>
<body>
    <h2>User Login</h2>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
