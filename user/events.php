<?php
// Include database connection
include_once '../includes/db_connection.php';

// Fetch events from the database
$sql = "SELECT * FROM Events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events</title>
</head>
<body>
    <h2>Upcoming Events</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo $row['name']; ?></strong><br>
                    Description: <?php echo $row['description']; ?><br>
                    Regular Ticket Price: <?php echo $row['ticket_price_regular']; ?><br>
                    VIP Ticket Price: <?php echo $row['ticket_price_vip']; ?><br>
                    Max Attendees: <?php echo $row['max_attendees']; ?><br>
                    <a href="reserve_ticket.php?event_id=<?php echo $row['id']; ?>">Book</a> <!-- Book button -->
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No events available.</p>
    <?php endif; ?>
    <a href="logout.php">Logout</a>

</body>
</html>
