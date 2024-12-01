<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Fetch mentors
$mentorQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM members WHERE status = 'mentor'";
$mentorStmt = $db->prepare($mentorQuery);
$mentorStmt->execute();
$mentors = $mentorStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted using POST method
    $query = "UPDATE events 
              SET title = ?, 
                  description = ?, 
                  event_date = ?, 
                  location = ?, 
                  event_type = ?, 
                  max_participants = ?, 
                  created_by = ? 
              WHERE id = ?"; // SQL query to update the event
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['event_date'],
        $_POST['location'],
        $_POST['event_type'],
        $_POST['max_participants'],
        $_POST['created_by'],
        $_GET['id']
    ]); // Execute the query
    header("Location: events.php"); // Redirect to the events page after execution
    exit();
}

// Fetch the event to edit
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="form-container">
    <h2>Edit Event</h2>
    <form method="POST">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Event Date</label>
            <input type="datetime-local" name="event_date" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['event_date']))); ?>" required>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>" required>
        </div>
        <div class="form-group">
            <label>Event Type</label>
            <select name="event_type" class="custom-dropdown" required>
                <option value="workshop" <?php echo $event['event_type'] === 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                <option value="mentoring" <?php echo $event['event_type'] === 'mentoring' ? 'selected' : ''; ?>>Mentoring</option>
                <option value="networking" <?php echo $event['event_type'] === 'networking' ? 'selected' : ''; ?>>Networking</option>
                <option value="conference" <?php echo $event['event_type'] === 'conference' ? 'selected' : ''; ?>>Conference</option>
            </select>
        </div>
        <div class="form-group">
            <label>Maximum Participants</label>
            <input type="number" name="max_participants" class="form-control" value="<?php echo htmlspecialchars($event['max_participants']); ?>" min="1" required>
        </div>
        <div class="form-group">
            <label>Created By</label>
            <select name="created_by" class="custom-dropdown" required>
                <option value="">Select a Mentor</option>
                <?php foreach ($mentors as $mentor): ?>
                    <option value="<?php echo htmlspecialchars($mentor['id']); ?>" <?php echo $mentor['id'] == $event['created_by'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mentor['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Event</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>
