<?php
session_start();

// Check if admin is not logged in, redirect to login page if true
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Fetch events from the database
$sql = "SELECT * FROM Events";
$result = $conn->query($sql);

// Check if event deletion success message is set
if(isset($_SESSION['event_deleted'])) {
    if($_SESSION['event_deleted'] === true) {
        $event_message = "<span style='color: red;'>Event deleted successfully!</span>";
    } else {
        $event_message = "<span style='color: red;'>Error deleting event: " . $conn->error . "</span>";
    }
    // Unset the session variable
    unset($_SESSION['event_deleted']);
}

$add_message = "";
if(isset($_GET['add']) && $_GET['add'] === 'success' && isset($_GET['event_name'])) {
    $event_name = $_GET['event_name'];
    $add_message = "<span style='color: green;'>$event_name added successfully!</span>";
    // Unset the add flag to prevent displaying the message on page refresh
    unset($_GET['add']);
}
// Check if event update success message is set
$update_message = "";
if(isset($_GET['update']) && $_GET['update'] === 'success' && isset($_GET['event_name'])) {
    $event_name = $_GET['event_name'];
    $update_message = "<span style='color: blue;'>$event_name updated successfully!</span>";
    // Unset the update flag to prevent displaying the message on page refresh
    unset($_GET['update']);
}

// Display admin dashboard with events list
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script>
        function confirmRemoveEvent(eventId) {
            if (confirm('Are you sure you want to remove this event?')) {
                window.location.href = 'remove_event.php?id=' + eventId;
            }
        }
    </script>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <a href="add_event.php">Add Event</a><br><br>

    <!-- Display existing events and options to manage them -->
    <?php
    if (!empty($update_message)) {
        echo "<p>$update_message</p>";
    }
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h3>" . $row['name'] . "</h3>";
            echo "<p>Description: " . $row['description'] . "</p>";
            echo "<p>Ticket Price (Regular): $" . $row['ticket_price_regular'] . "</p>";
            echo "<p>Ticket Price (VIP): $" . $row['ticket_price_vip'] . "</p>";
            echo "<p>Max Attendees: " . $row['max_attendees'] . "</p>";
            echo "<a href='edit_event.php?id=" . $row['id'] . "'><button>Edit</button></a>";
            echo "<button onclick=\"confirmRemoveEvent(" . $row['id'] . ")\">Remove</button>";
            echo "</div><br>";
        }
    } else {
        echo "No events found";
    }
    ?>
    <?php echo $add_message; ?>
    <br><br>
    <a href="logout.php">Logout</a>
</body>
</html>
