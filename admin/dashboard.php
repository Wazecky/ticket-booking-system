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
    <title>ADMIN PANEL</title>
    <link rel="stylesheet" href="../Style/style3.css">
    <script>
        function confirmRemoveEvent(eventId) {
            if (confirm('Are you sure you want to remove this event?')) {
                window.location.href = 'remove_event.php?id=' + eventId;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>ADMIN PANEL</h1>
        <div class="button-group">
            <button onclick="window.location.href='add_event.php'" class="btn btn-add">Add Event</button>
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>

        <table>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Ticket Price (Regular)</th>
                <th>Ticket Price (VIP)</th>
                <th>Max Attendees</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>$" . $row['ticket_price_regular'] . "</td>";
                    echo "<td>$" . $row['ticket_price_vip'] . "</td>";
                    echo "<td>" . $row['max_attendees'] . "</td>";
                    echo "<td>
                            <a href='edit_event.php?id=" . $row['id'] . "' class='btn edit-btn'>Edit</a>
                            <button onclick=\"confirmRemoveEvent(" . $row['id'] . ")\" class='btn remove-btn'>Remove</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No events found</td></tr>";
            }
            ?>
        </table>

        <?php echo $add_message; ?>
    </div>
</body>
</html>
