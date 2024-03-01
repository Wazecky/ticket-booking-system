<?php
// Add session validation to ensure user is logged in as admin
include_once 'session_validation.php';

// Include database connection
include_once '../includes/db_connection.php';

// Check if event ID is provided via GET parameter
if(isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch event details from the database
    $sql = "SELECT * FROM Events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found!";
        exit;
    }
} else {
    echo "Event ID not provided!";
    exit;
}

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $regular_price = $_POST['regular_price'];
    $vip_price = $_POST['vip_price'];
    $max_attendees = $_POST['max_attendees'];

    // Prepare SQL statement to update event in the database
    $sql = "UPDATE Events SET name = ?, description = ?, ticket_price_regular = ?, ticket_price_vip = ?, max_attendees = ? WHERE id = ?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddii", $name, $description, $regular_price, $vip_price, $max_attendees, $event_id);

    // Execute the statement
    if($stmt->execute()) {
        // Event updated successfully
        $event_name = $name;
        header("Location: dashboard.php?update=success&event_name=" . urlencode($event_name));
        exit;
    } else {
        // Error occurred
        echo "Error updating event: " . $conn->error;
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
    <title>EDIT EVENT</title>
</head>
<body>
    <h1>EDIT EVENT</h1>
    <form action="#" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $event['name']; ?>" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required><?php echo $event['description']; ?></textarea><br><br>
        <label for="regular_price">Regular Ticket Price:</label>
        <input type="text" id="regular_price" name="regular_price" value="<?php echo $event['ticket_price_regular']; ?>" required><br><br>
        <label for="vip_price">VIP Ticket Price:</label>
        <input type="text" id="vip_price" name="vip_price" value="<?php echo $event['ticket_price_vip']; ?>" required><br><br>
        <label for="max_attendees">Max Attendees:</label>
        <input type="number" id="max_attendees" name="max_attendees" value="<?php echo $event['max_attendees']; ?>" required><br><br>
        
            <!-- Container for the buttons -->
        <div class="button-container">
            <!-- Add Event button -->
            <button type="submit">Update Event</button>
            
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
