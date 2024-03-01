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
    <link rel="stylesheet" href="../Style/style4.css">
    <style>
        /* New CSS styles */
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        /* Styles for the "GO BACK" button */
        .goback-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .goback-button:hover {
            background-color: #da190b;
        }
    </style>
    <title>ADD EVENT</title>
</head>
<body>
    <h1>ADD EVENT</h1>
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
        
        <!-- Container for the buttons -->
        <div class="button-container">
            <!-- Add Event button -->
            <button type="submit">Add Event</button>
            
            <!-- GO BACK button -->
            <button class="goback-button" onclick="goBack()">GO BACK</button>
        </div>

        <!-- JavaScript function for the GO BACK button -->
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </form>
</body>
</html>

