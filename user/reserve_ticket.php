<?php
// Include database connection
include_once '../includes/db_connection.php'; 
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Initialize message variable
$message = "";

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
                $message = "You have used up all the available 5 tickets across the two ticket types (Regular and VIP) for this event.";
            } else {
                // Proceed with reservation
                if (reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets)) {
                    $message = "Tickets reserved successfully! Check your email for details";
                } else {
                    $message = "Error reserving tickets.";
                }
            }
            
        } else {
            // No existing reservations, proceed with reservation
            if (reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets)) {
                $message = "Tickets reserved successfully! Check your email for details";
            } else {
                $message = "Error reserving tickets.";
            }
        }

        // Close statements and connection
        $stmt_check->close();
        $conn->close();
    }
} else {
    $message = "Event ID not provided.";
}

// Function to reserve tickets
function reserveTickets($conn, $event_id, $user_id, $ticket_type, $num_tickets) {
    // Check if the event has available tickets
    $check_available_tickets = "SELECT max_attendees FROM events WHERE id = ?";
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
            $insert_reservation = "INSERT INTO reservations (event_id, user_id, ticket_type, num_tickets) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_reservation);
            $stmt_insert->bind_param("iisi", $event_id, $user_id, $ticket_type, $num_tickets);

            if ($stmt_insert->execute()) {
                // Send reservation confirmation email
                $user_email = getUserEmail($conn, $user_id);
                if ($user_email) {
                    $event_details = getEventDetails($conn, $event_id);
                    $message = "Your tickets have been successfully reserved for the {$event_details['name']}.<br>Below is your ticket details.<br><br>";
                    $message .= "Ticket Type: $ticket_type<br>";

                    $message .= "Number of Tickets: $num_tickets<br>";

                    $message .= "Ticket Price: {$event_details['ticket_price_' . strtolower($ticket_type)]}<br>";
                    
                    sendEmailNotification($user_email, 'Reservation Confirmation', $message);
                } else {
                    return false; // User email not found
                }
                return true; // Reservation successful
            } else {
                return false; // Error executing statement
            }
            // Close the statement
            $stmt_insert->close();
        } else {
            // Not enough tickets available
            return false;
        }
    } else {
        // Event not found
        return false;
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

    $query = "SELECT SUM(num_tickets) AS total_tickets FROM reservations WHERE event_id = ? AND ticket_type = ?";
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

// Function to get user's email from the database
function getUserEmail($conn, $user_id) {
    $query = "SELECT email FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row['email'];
    } else {
        return false;
    }
}

// Function to get event details from the database
function getEventDetails($conn, $event_id) {
    $query = "SELECT name, ticket_price_regular, ticket_price_vip FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return false;
    }
}

// Function to send email notification
function sendEmailNotification($to, $subject, $message) {
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'alvanowazecky@gmail.com';              // SMTP username
        $mail->Password   = 'bwyy kdmk chhm cnig';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        // Recipients
        $mail->setFrom('alvanowazecky@gmail.com', 'WAZECKY TICKET BOOKINGS');
        $mail->addAddress($to);                                     // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/style5.css">
    <style>
        .message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }

        .goback-button {
            display: block;
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }

        .goback-button:hover {
            background-color: #45a049;
        }
    </style>
    <title>BOOK TICKETS</title>
</head>
<body>
    <h1>RESERVE TICKETS</h1>
    <form action="#" method="post">
        <p style="font-style: italic; color: black; font-size: small;">Note: You can reserve a maximum of 5 tickets across Regular and VIP types.</p>
        <label for="ticket_type">Ticket Type:</label>
        <select id="ticket_type" name="ticket_type">
            <option value="regular">Regular</option>
            <option value="VIP">VIP</option>
        </select><br><br>
        <label for="num_tickets">Number of Tickets:</label>
        <input type="number" id="num_tickets" name="num_tickets" min="1" max="5" required><br><br>
        <button type="submit">Reserve Tickets</button>
    </form>
    <?php
    if (!empty($message)) {
        echo "<p class='message'>$message</p>";
    }
    ?>
   <br><button class="goback-button" onclick="goBack()">GO BACK</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>


