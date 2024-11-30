<?php

include_once "config/database.php";
include_once "includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<p style='color:red;'>Access denied. Please log in to register for events.</p>");
}

$database = new Database();
$db = $database->getConnection(); // Get the database connection object

// Validate and sanitize event ID
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:red;'>Invalid event ID provided.</p>");
}

$eventId = intval($_GET['id']); // Sanitize event ID

// Fetch the event's details
$query = "SELECT title, description, event_date, location, event_type, max_participants 
          FROM events WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$eventId]);

if ($stmt->rowCount() === 0) {
    die("<p style='color:red;'>Event not found.</p>");
}

$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the current number of participants
$countQuery = "SELECT COUNT(*) AS current_participants FROM event_registrations WHERE event_id = ?";
$countStmt = $db->prepare($countQuery);
$countStmt->execute([$eventId]);
$currentParticipants = $countStmt->fetch(PDO::FETCH_ASSOC)['current_participants'];

// Calculate remaining slots
$remainingSlots = $event['max_participants'] - $currentParticipants;

// Fetch the list of registered participants
$participantsQuery = "SELECT CONCAT(members.first_name, ' ', members.last_name) AS full_name 
                      FROM event_registrations 
                      INNER JOIN members ON event_registrations.member_id = members.id 
                      WHERE event_registrations.event_id = ?";
$participantsStmt = $db->prepare($participantsQuery);
$participantsStmt->execute([$eventId]);
$participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);

$userId = $_SESSION['user_id'];

// Check if the user is registered
$checkQuery = "SELECT * FROM event_registrations WHERE event_id = ? AND member_id = ?";
$checkStmt = $db->prepare($checkQuery);
$checkStmt->execute([$eventId, $userId]);
$isRegistered = $checkStmt->rowCount() > 0;

// Handle the "Register to Event" button click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if ($remainingSlots <= 0) {
        echo "<p style='color:red;'>No more slots available for this event.</p>";
    } else {
        // Register for the event
        $registerQuery = "INSERT INTO event_registrations (event_id, member_id, status) VALUES (?, ?, 'confirmed')";
        $registerStmt = $db->prepare($registerQuery);

        try {
            $registerStmt->execute([$eventId, $userId]);
            echo "<p style='color:green;'>You have successfully registered for this event.</p>";
            $remainingSlots--; // Decrease the remaining slots
            $isRegistered = true;
            // Refresh the list of participants
            $participantsStmt->execute([$eventId]);
            $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error registering for the event: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Handle the "Cancel Registration" button click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $cancelQuery = "DELETE FROM event_registrations WHERE event_id = ? AND member_id = ?";
    $cancelStmt = $db->prepare($cancelQuery);

    try {
        $cancelStmt->execute([$eventId, $userId]);
        echo "<p style='color:orange;'>You have successfully canceled your registration for this event.</p>";
        $remainingSlots++; // Increase the remaining slots
        $isRegistered = false;
        // Refresh the list of participants
        $participantsStmt->execute([$eventId]);
        $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error canceling registration: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
}

?>

<div class="details-container">
    <h2>Event Details</h2>
    <table class="details-table">
        <tr>
            <th>Title:</th>
            <td><?php echo displayValue($event['title']); ?></td>
        </tr>
        <tr>
            <th>Description:</th>
            <td><?php echo nl2br(displayValue($event['description'])); ?></td>
        </tr>
        <tr>
            <th>Event Date:</th>
            <td><?php echo displayValue($event['event_date']); ?></td>
        </tr>
        <tr>
            <th>Location:</th>
            <td><?php echo displayValue($event['location']); ?></td>
        </tr>
        <tr>
            <th>Event Type:</th>
            <td><?php echo displayValue($event['event_type']); ?></td>
        </tr>
        <tr>
            <th>Maximum Participants:</th>
            <td><?php echo displayValue($event['max_participants']); ?></td>
        </tr>
        <tr>
            <th>Remaining Slots:</th>
            <td><?php echo $remainingSlots > 0 ? $remainingSlots : "No slots available"; ?></td>
        </tr>
    </table>

    <!-- Register to Event and Cancel Registration Buttons -->
    <div class="d-flex align-items-center">
        <?php if ($isRegistered): ?>
            <form method="POST">
                <button type="submit" name="cancel" class="btn btn-danger">Cancel Registration</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="register" class="btn btn-primary" <?php echo $remainingSlots <= 0 ? 'disabled' : ''; ?>>
                    Register to Event
                </button>
            </form>
        <?php endif; ?>
        &nbsp;&nbsp; <!-- Add space between buttons -->
        <a href="events.php" class="btn btn-primary">Back to Events List</a>
    </div>

    <br/>
    <br/>
    <!-- List of Registered Participants -->
    <div class="participants-section">
        <h3>Registered Participants:</h3>
        <?php if (count($participants) > 0): ?>
            <ul>
                <?php foreach ($participants as $participant): ?>
                    <li><?php echo htmlspecialchars($participant['full_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No participants registered yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
