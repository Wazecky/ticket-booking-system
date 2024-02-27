<?php
// Add session validation to ensure user is logged in as admin
include_once 'session_validation.php';

// Include database connection
include_once '../includes/db_connection.php';

// Check if event ID is provided via GET parameter
if(isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Start a transaction
    $conn->begin_transaction();

    // Prepare SQL statement to delete associated reservations
    $delete_reservations_sql = "DELETE FROM Reservations WHERE event_id = ?";
    $stmt_delete_reservations = $conn->prepare($delete_reservations_sql);
    $stmt_delete_reservations->bind_param("i", $event_id);

    // Execute the statement to delete reservations
    $delete_reservations_success = $stmt_delete_reservations->execute();

    // Prepare SQL statement to delete event from the database
    $delete_event_sql = "DELETE FROM Events WHERE id = ?";
    $stmt_delete_event = $conn->prepare($delete_event_sql);
    $stmt_delete_event->bind_param("i", $event_id);

    // Execute the statement to delete event if reservations were successfully deleted
    if ($delete_reservations_success && $stmt_delete_event->execute()) {
        // Commit the transaction
        $conn->commit();
        // Store success message in session
        $_SESSION['event_deleted'] = true;
    } else {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        // Store error message in session
        $_SESSION['event_deleted'] = false;
    }

    // Close statements and connection
    $stmt_delete_reservations->close();
    $stmt_delete_event->close();
    $conn->close();

    // Redirect back to dashboard.php
    header("Location: dashboard.php");
    exit;
} else {
    echo "Event ID not provided!";
    exit;
}
?>
