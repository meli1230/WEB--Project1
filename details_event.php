<?php
include_once "config/database.php";
include_once "includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
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
        $registerQuery = "INSERT INTO event_registrations (event_id, member_id, status) VALUES (?, ?, 'confirmed')";
        $registerStmt = $db->prepare($registerQuery);
        try {
            $registerStmt->execute([$eventId, $userId]);
            echo "<p style='color:green;'>You have successfully registered for this event.</p>";
            $remainingSlots--; // Decrease the remaining slots
            $isRegistered = true;
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
        $participantsStmt->execute([$eventId]);
        $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error canceling registration: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_submit'])) {
    $feedback = trim($_POST['feedback']);
    $rating = intval($_POST['rating']);
    if (!empty($feedback) && $rating > 0 && $rating <= 5) {
        $feedbackQuery = "INSERT INTO event_feedback (event_id, member_id, feedback, rating) VALUES (?, ?, ?, ?)";
        $feedbackStmt = $db->prepare($feedbackQuery);
        try {
            $feedbackStmt->execute([$eventId, $userId, $feedback, $rating]);
            echo "<p style='color:green;'>Thank you for your feedback!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error submitting feedback: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Please provide valid feedback and a rating between 1 and 5.</p>";
    }
}

// Fetch feedback and average rating
$feedbackQuery = "SELECT feedback, rating, CONCAT(members.first_name, ' ', members.last_name) AS full_name 
                  FROM event_feedback 
                  INNER JOIN members ON event_feedback.member_id = members.id 
                  WHERE event_feedback.event_id = ?";
$feedbackStmt = $db->prepare($feedbackQuery);
$feedbackStmt->execute([$eventId]);
$feedbackList = $feedbackStmt->fetchAll(PDO::FETCH_ASSOC);

$averageRatingQuery = "SELECT AVG(rating) AS average_rating FROM event_feedback WHERE event_id = ?";
$averageRatingStmt = $db->prepare($averageRatingQuery);
$averageRatingStmt->execute([$eventId]);
$averageRating = $averageRatingStmt->fetch(PDO::FETCH_ASSOC)['average_rating'];


?>

<div class="details-container">
    <h2>Event Details</h2>
    <table class="details-table">
        <tr><th>Title:</th><td><?php echo displayValue($event['title']); ?></td></tr>
        <tr><th>Description:</th><td><?php echo nl2br(displayValue($event['description'])); ?></td></tr>
        <tr><th>Event Date:</th><td><?php echo displayValue($event['event_date']); ?></td></tr>
        <tr><th>Location:</th><td><?php echo displayValue($event['location']); ?></td></tr>
        <tr><th>Event Type:</th><td><?php echo displayValue($event['event_type']); ?></td></tr>
        <tr><th>Maximum Participants:</th><td><?php echo displayValue($event['max_participants']); ?></td></tr>
        <tr><th>Remaining Slots:</th><td><?php echo $remainingSlots > 0 ? $remainingSlots : "No slots available"; ?></td></tr>
    </table>

    <div class="d-flex align-items-center">
        <?php if ($isRegistered): ?>
            <form method="POST">
                <button type="submit" name="cancel" class="btn btn-danger">Cancel Registration</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="register" class="btn btn-primary" <?php echo $remainingSlots <= 0 ? 'disabled' : ''; ?>>Register to Event</button>
            </form>
        <?php endif; ?>
        &nbsp;&nbsp; <!-- Add space between buttons -->
        <a href="events.php" class="btn btn-primary">Back to Events List</a>
    </div>


    <br><h3>Feedback and Ratings</h3>
    <?php if ($isRegistered): ?>
        <form method="POST">
            <textarea class= "custom-textbox feedback-textbox" name="feedback" placeholder="Type your feedback here"></textarea>
            <br/>
            <select class="custom-dropdown" name="rating" required>
                <option value="">Select Rating</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <br/>
            <br/>
            <button type="submit" class="btn btn-primary" name="feedback_submit">Submit Feedback</button>
        </form>
    <?php endif; ?>
    <br/>
    <?php if ($averageRating): ?>
        <p><strong>Average Rating:</strong> <?php echo round($averageRating, 2); ?> / 5</p>
    <?php endif; ?>
    <ul>
        <?php foreach ($feedbackList as $feedback): ?>
            <li><strong><?php echo $feedback['full_name']; ?>:</strong> <?php echo $feedback['feedback']; ?> (Rating: <?php echo $feedback['rating']; ?>)</li>
        <?php endforeach; ?>
    </ul>
</div>



<?php include_once "includes/footer.php"; ?>
