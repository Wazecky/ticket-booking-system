<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
</head>
<body>
    <h2>Reservation Confirmation</h2>
    <p>Your reservation has been successfully processed!</p>
    <!-- Display reservation details -->
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
</head>
<body>
    <h2>Reservation Confirmation</h2>
    <p>Your reservation has been successfully processed!</p>
    <!-- Display reservation details -->
    <h3>Reservation Details:</h3>
    <?php
    // Include database connection
    include_once '../includes/db_connection.php';

    // Retrieve reservation details from the database based on the reservation ID
    if(isset($_GET['reservation_id'])) {
        $reservation_id = $_GET['reservation_id'];

        // Fetch reservation details from the database
        $sql = "SELECT * FROM Reservations WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 1) {
            $reservation = $result->fetch_assoc();
            echo "<p>Event ID: " . $reservation['event_id'] . "</p>";
            echo "<p>User ID: " . $reservation['user_id'] . "</p>";
            echo "<p>Ticket Type: " . $reservation['ticket_type'] . "</p>";
            echo "<p>Number of Tickets: " . $reservation['num_tickets'] . "</p>";
            // You can display more details here as needed
        } else {
            echo "<p>Reservation not found!</p>";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "<p>Reservation ID not provided!</p>";
    }
    ?>
</body>
</html>
