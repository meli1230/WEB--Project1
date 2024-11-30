<?php

include_once "config/database.php";
include_once "includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<p style='color:red;'>Access denied. Please log in to access mentorship details.</p>");
}

$database = new Database();
$db = $database->getConnection(); // Get the database connection object

// Validate and sanitize mentorship ID
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:red;'>Invalid mentorship ID provided.</p>");
}

$mentorshipId = intval($_GET['id']); // Sanitize mentorship ID

// Fetch the mentorship's details
$query = "SELECT m1.first_name AS mentor_first_name, 
                 m1.last_name AS mentor_last_name, 
                 m2.first_name AS mentee_first_name, 
                 m2.last_name AS mentee_last_name, 
                 mentorships.time_slot 
          FROM mentorships 
          LEFT JOIN members m1 ON mentorships.mentor_id = m1.id 
          LEFT JOIN members m2 ON mentorships.member_id = m2.id 
          WHERE mentorships.id = ?";
$stmt = $db->prepare($query);
$stmt->execute(array($mentorshipId));

if ($stmt->rowCount() === 0) {
    die("<p style='color:red;'>Mentorship not found.</p>");
}

$mentorship = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle task completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_ids'])) {
    foreach ($_POST['task_ids'] as $taskId) {
        $updateQuery = "UPDATE mentorship_progress SET is_completed = 1 WHERE id = ? AND mentorship_id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute(array($taskId, $mentorshipId));
    }
    echo "<p style='color:green;'>Tasks updated successfully.</p>";
}

// Fetch tasks for this mentorship
$tasksQuery = "SELECT id, task_description, is_completed 
               FROM mentorship_progress 
               WHERE mentorship_id = ?";
$tasksStmt = $db->prepare($tasksQuery);
$tasksStmt->execute(array($mentorshipId));
$tasks = $tasksStmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
}

?>

<div class="details-container">
    <h2>Mentorship Details</h2>
    <table class="details-table">
        <tr>
            <th>Mentor:</th>
            <td><?php echo displayValue($mentorship['mentor_first_name'] . ' ' . $mentorship['mentor_last_name']); ?></td>
        </tr>
        <tr>
            <th>Mentee:</th>
            <td><?php echo displayValue($mentorship['mentee_first_name'] . ' ' . $mentorship['mentee_last_name']); ?></td>
        </tr>
        <tr>
            <th>Time Slot:</th>
            <td><?php echo displayValue($mentorship['time_slot']); ?></td>
        </tr>
    </table>

    <br/>

    <!-- Task List -->
    <div class="tasks-section">
        <h3>Progress Tracking</h3>
        <?php if (count($tasks) > 0): ?>
            <form method="POST">
                <ul>
                    <?php foreach ($tasks as $task): ?>
                        <li>
                            <label>
                                <input type="checkbox" name="task_ids[]" value="<?php echo htmlspecialchars($task['id']); ?>" <?php echo $task['is_completed'] ? 'checked disabled' : ''; ?>>
                                <?php echo htmlspecialchars($task['task_description']); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-secondary" <?php echo empty(array_filter($tasks, function ($task) { return !$task['is_completed']; })) ? 'disabled' : '';
                ?>>Mark as Completed</button>
            </form>
        <?php else: ?>
            <p>No tasks assigned yet.</p>
        <?php endif; ?>
    </div>

    <br/>
    <a href="mentorships.php" class="btn btn-primary">Back to Mentorship List</a>
</div>

<?php include_once "includes/footer.php"; ?>
