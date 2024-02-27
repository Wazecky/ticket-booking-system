<?php
// Include database connection
include_once '../includes/db_connection.php'; 

// Check if event_id is provided via GET parameter
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        session_start();
        $user_id = $_SESSION['user_id']; // Retrieve user ID from session
        $ticket_type = $_POST['ticket_type'];
        $num_tickets = $_POST['num_tickets'];

        // Check if the user has already reserved tickets for the same event
        $check_existing_reservation = "SELECT * FROM Reservations WHERE event_id = ? AND user_id = ?";
        $stmt_check = $conn->prepare($check_existing_reservation);
        $stmt_check->bind_param("ii", $event_id, $user_id);
        $stmt_check->execute();
        $existing_reservation_result = $stmt_check->get_result();

        if ($existing_reservation_result->num_rows > 0) {
            // Check if the total number of tickets reserved exceeds the limit
            $total_tickets_reserved = 0;
            while ($row = $existing_reservation_result->fetch_assoc()) {
                $total_tickets_reserved += $row['num_tickets'];
            }

            if (($total_tickets_reserved + $num_tickets) > 5) {
                echo "You have used up all the available 5 tickets across the two ticket types (Regular and VIP) for this event.";
            } else {
                // Proceed with reservation
                reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets);
            }
            
        } else {
            // No existing reservations, proceed with reservation
            reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets);
        }

        // Close statements and connection
        $stmt_check->close();
        $conn->close();
    }
} else {
    echo "Event ID not provided.";
}

// Function to reserve tickets
function reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets) {
    // Check if the event has available tickets
    $check_available_tickets = "SELECT max_attendees FROM Events WHERE id = ?";
    $stmt_tickets = $conn->prepare($check_available_tickets);
    $stmt_tickets->bind_param("i", $event_id);
    $stmt_tickets->execute();
    $tickets_result = $stmt_tickets->get_result();

    if ($tickets_result->num_rows == 1) {
        $event = $tickets_result->fetch_assoc();
        $max_attendees = $event['max_attendees'];

        // Get the total number of tickets reserved for this event and ticket type
        $tickets_reserved = getTicketsReserved($conn, $event_id, $ticket_type);

        // Check if the event has available tickets for the selected ticket type and quantity
        $tickets_available = $max_attendees - $tickets_reserved;

        if ($tickets_available >= $num_tickets) {
            // Proceed with reservation
            $insert_reservation = "INSERT INTO Reservations (event_id, user_id, ticket_type, num_tickets) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_reservation);
            $stmt_insert->bind_param("iisi", $event_id, $user_id, $ticket_type, $num_tickets);

            if ($stmt_insert->execute()) {
                echo "Tickets reserved successfully!";
            } else {
                echo "Error reserving tickets: " . $conn->error;
            }
            // Close the statement
            $stmt_insert->close();
        } else {
            // Not enough tickets available
            echo "Sorry, there are not enough tickets available for the selected ticket type. The available tickets are $tickets_available.";
        }
    } else {
        // Event not found
        echo "Event not found.";
    }

    // Close the statement if initialized
    if (isset($stmt_tickets)) {
        $stmt_tickets->close();
    }
}

// Function to get the number of tickets reserved for a specific event and ticket type
function getTicketsReserved($conn, $event_id, $ticket_type)
{
    $total_tickets_reserved = 0;

    $query = "SELECT SUM(num_tickets) AS total_tickets FROM Reservations WHERE event_id = ? AND ticket_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $event_id, $ticket_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $total_tickets_reserved = $row['total_tickets'];
    }

    $stmt->close();
    return $total_tickets_reserved;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Tickets</title>
</head>
<body>
    <h2>Reserve Tickets</h2>
    <form action="#" method="post">
        <p style="font-style: italic; color: orange; font-size: small;">Note: You can reserve a maximum of 5 tickets across Regular and VIP types.</p>
        <label for="ticket_type">Ticket Type:</label>
        <select id="ticket_type" name="ticket_type">
            <option value="regular">Regular</option>
            <option value="VIP">VIP</option>
        </select><br><br>
        <label for="num_tickets">Number of Tickets:</label>
        <input type="number" id="num_tickets" name="num_tickets" min="1" max="5" required><br><br>
        <button type="submit">Reserve Tickets</button>
    </form>
</body>
</html>