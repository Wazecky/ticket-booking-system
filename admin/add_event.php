<?php
// Include database connection
include_once '../includes/db_connection.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $regular_price = $_POST['regular_price'];
    $vip_price = $_POST['vip_price'];
    $max_attendees = $_POST['max_attendees'];

    // Prepare SQL statement to insert new event into the database
    $sql = "INSERT INTO Events (name, description, ticket_price_regular, ticket_price_vip, max_attendees) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddi", $name, $description, $regular_price, $vip_price, $max_attendees);

    if($stmt->execute()) {
        // Event added successfully
        header("Location: dashboard.php?add=success&event_name=" . urlencode($name));
        exit;
    } else {
        // Error occurred
        echo "Error adding event: " . $conn->error;
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
    <title>Add Event</title>
</head>
<body>
    <h2>Add Event</h2>
    <form action="#" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>
        <label for="regular_price">Regular Ticket Price:</label>
        <input type="text" id="regular_price" name="regular_price" required><br><br>
        <label for="vip_price">VIP Ticket Price:</label>
        <input type="text" id="vip_price" name="vip_price" required><br><br>
        <label for="max_attendees">Max Attendees:</label>
        <input type="number" id="max_attendees" name="max_attendees" required><br><br>
        <button type="submit">Add Event</button>
    </form>
</body>
</html>
