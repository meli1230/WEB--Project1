<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Fetch mentors for the "Created By" dropdown
$mentorQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM members WHERE status = 'mentor'";
$mentorStmt = $db->prepare($mentorQuery);
$mentorStmt->execute();
$mentors = $mentorStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert new event
    $query = "INSERT INTO events (
                     title, 
                     description, 
                     event_date, 
                     location, 
                     event_type, 
                     max_participants, 
                     created_by
                ) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['event_date'],
        $_POST['location'],
        $_POST['event_type'],
        $_POST['max_participants'],
        $_POST['created_by']
    ]);
    header("Location: events.php");
    exit();
}
?>

<div class="form-container">
    <h2>Add Event</h2>
    <form method="POST">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Event Date</label>
            <input type="datetime-local" name="event_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Event Type</label>
            <br/>
            <select name="event_type" class="custom-dropdown" required>
                <option value="workshop" selected>Workshop</option>
                <option value="mentoring">Mentoring</option>
                <option value="networking">Networking</option>
                <option value="conference">Conference</option>
            </select>
        </div>
        <div class="form-group">
            <label>Maximum Participants</label>
            <input type="number" name="max_participants" class="form-control" min="1" required>
        </div>
        <div class="form-group">
            <label>Created By</label>
            <br/>
            <select name="created_by" class="custom-dropdown" required>
                <option value="">Select a Mentor</option>
                <?php foreach ($mentors as $mentor): ?>
                    <option value="<?php echo htmlspecialchars($mentor['id']); ?>">
                        <?php echo htmlspecialchars($mentor['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Event</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>
