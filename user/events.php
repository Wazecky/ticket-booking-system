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
    <title>EVENTS</title>
    <link rel="stylesheet" href="../Style/style3.css">
</head>
<body>
    <div class="container">
        <h1>UPCOMING EVENTS</h1>
        <div class="button-group">
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Description</th>
                    <th>Ticket Price (Regular)</th>
                    <th>Ticket Price (VIP)</th>
                    <th>Max Attendees</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>$<?php echo $row['ticket_price_regular']; ?></td>
                        <td>$<?php echo $row['ticket_price_vip']; ?></td>
                        <td><?php echo $row['max_attendees']; ?></td>
                        <td>
                            <a href="reserve_ticket.php?event_id=<?php echo $row['id']; ?>" class="btn edit-btn ">Book</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No events available.</p>
        <?php endif; ?>
    </div>
</body>
</html>

