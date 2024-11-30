<?php

include_once "config/database.php";
include_once "includes/header.php";

// Check if a valid event ID is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red;'>No event ID provided.</p>";
    include_once "includes/footer.php";
    exit();
}

$eventId = $_GET['id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch the event's details
$query = "SELECT title, description, event_date, location, event_type, max_participants, created_by, created_at 
          FROM events WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$eventId]);

if ($stmt->rowCount() == 0) {
    echo "<p style='color:red;'>Event not found.</p>";
    include_once "includes/footer.php";
    exit();
}

// Fetch the event data
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
}

// Fetch the name of the creator (mentor) if available
$creatorName = "N/A";
if ($event['created_by']) {
    $creatorQuery = "SELECT CONCAT(first_name, ' ', last_name) AS name FROM members WHERE id = ?";
    $creatorStmt = $db->prepare($creatorQuery);
    $creatorStmt->execute([$event['created_by']]);
    $creator = $creatorStmt->fetch(PDO::FETCH_ASSOC);
    if ($creator) {
        $creatorName = htmlspecialchars($creator['name']);
    }
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
            <th>Created By:</th>
            <td><?php echo $creatorName; ?></td>
        </tr>
        <tr>
            <th>Created At:</th>
            <td><?php echo displayValue($event['created_at']); ?></td>
        </tr>
    </table>
    <a href="events.php" class="btn btn-primary">Back to Events List</a>
</div>

<?php include_once "includes/footer.php"; ?>
