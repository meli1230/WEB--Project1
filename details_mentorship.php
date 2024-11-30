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
    die("<p style='color:red;'>Access denied. Please log in to access mentorship details.</p>");
}

$database = new Database();
$db = $database->getConnection(); // Get the database connection object

// Validate and sanitize mentorship ID
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:red;'>Invalid mentorship ID provided.</p>");
}

$mentorshipId = intval($_GET['id']); // Sanitize mentorship ID
$userId = intval($_SESSION['user_id']); // Ensure user ID is an integer

// Fetch mentorship details
$query = "SELECT m1.first_name AS mentor_first_name, 
                 m1.last_name AS mentor_last_name, 
                 m2.first_name AS mentee_first_name, 
                 m2.last_name AS mentee_last_name, 
                 mentorships.time_slot,
                 mentorships.member_id AS registered_member_id
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

// Fetch user role
$checkRoleQuery = "SELECT status FROM members WHERE id = ?";
$checkRoleStmt = $db->prepare($checkRoleQuery);
$checkRoleStmt->execute(array($userId));
$user = $checkRoleStmt->fetch(PDO::FETCH_ASSOC);

if (!$user || ($user['status'] !== 'member' && $user['status'] !== 'mentor')) {
    die("<p style='color:red;'>Access denied. Only mentors and members can view this page.</p>");
}

// Check if the user is already registered for this mentorship
$isRegistered = ($mentorship['registered_member_id'] == $userId);

// Handle registration (only for mentees, and only if no one else is registered)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && $user['status'] === 'member') {
    if (!empty($mentorship['registered_member_id'])) {
        echo "<p style='color:red;'>This mentorship is already taken by another mentee.</p>";
    } else {
        $registerQuery = "UPDATE mentorships SET member_id = ? WHERE id = ?";
        $registerStmt = $db->prepare($registerQuery);
        try {
            $registerStmt->execute(array($userId, $mentorshipId));
            echo "<p style='color:green;'>You have successfully registered for this mentorship.</p>";
            $isRegistered = true;
            $mentorship['registered_member_id'] = $userId; // Update mentorship state
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error registering for the mentorship: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Handle task completion (only for registered mentees)
if ($isRegistered && $user['status'] === 'member' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_ids'])) {
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

// Fetch and display feedback
$feedbackQuery = "SELECT feedback, rating, CONCAT(members.first_name, ' ', members.last_name) AS full_name 
                  FROM mentorship_feedback 
                  INNER JOIN members ON mentorship_feedback.member_id = members.id 
                  WHERE mentorship_feedback.mentorship_id = ?";
$feedbackStmt = $db->prepare($feedbackQuery);
$feedbackStmt->execute(array($mentorshipId));
$feedbackList = $feedbackStmt->fetchAll(PDO::FETCH_ASSOC);

$averageRatingQuery = "SELECT AVG(rating) AS average_rating FROM mentorship_feedback WHERE mentorship_id = ?";
$averageRatingStmt = $db->prepare($averageRatingQuery);
$averageRatingStmt->execute(array($mentorshipId));
$averageRating = $averageRatingStmt->fetch(PDO::FETCH_ASSOC)['average_rating'];

// Cancel mentorship registration (only for registered mentees)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_mentorship'])) {
    if ($isRegistered && $user['status'] === 'member') { // Ensure user is the registered mentee
        $cancelQuery = "UPDATE mentorships SET member_id = NULL WHERE id = ? AND member_id = ?";
        $cancelStmt = $db->prepare($cancelQuery);
        try {
            $cancelStmt->execute(array($mentorshipId, $userId));
            echo "<p style='color:orange;'>You have successfully canceled your mentorship registration.</p>";
            $isRegistered = false; // Update the registration status
            $mentorship['registered_member_id'] = null; // Update mentorship state
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error canceling mentorship: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>You are not authorized to cancel this mentorship.</p>";
    }
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_submit'])) {
    $feedback = trim($_POST['feedback']);
    $rating = intval($_POST['rating']);

    // Validate feedback and rating
    if (empty($feedback) || $rating < 1 || $rating > 5) {
        echo "<p style='color:red;'>Please provide valid feedback and a rating between 1 and 5.</p>";
    } else {
        // Insert feedback into the database
        $feedbackQuery = "INSERT INTO mentorship_feedback (mentorship_id, member_id, feedback, rating) 
                          VALUES (?, ?, ?, ?)";
        $feedbackStmt = $db->prepare($feedbackQuery);
        try {
            $feedbackStmt->execute([$mentorshipId, $userId, $feedback, $rating]);
            echo "<p style='color:green;'>Thank you for your feedback!</p>";

            // Update the feedback list and average rating dynamically
            $feedbackList[] = [
                'full_name' => $_SESSION['full_name'], // Assuming user's full name is in the session
                'feedback' => $feedback,
                'rating' => $rating
            ];

            $averageRatingQuery = "SELECT AVG(rating) AS average_rating FROM mentorship_feedback WHERE mentorship_id = ?";
            $averageRatingStmt = $db->prepare($averageRatingQuery);
            $averageRatingStmt->execute([$mentorshipId]);
            $averageRating = $averageRatingStmt->fetch(PDO::FETCH_ASSOC)['average_rating'];
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error submitting feedback: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
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

    <!-- Registration and Cancel buttons -->
    <?php if (!$isRegistered && $user['status'] === 'member' && empty($mentorship['registered_member_id'])): ?>
        <!-- Show Register button if not registered -->
        <div class="registration-section">
            <form method="POST">
                <button type="submit" name="register" class="btn btn-primary">Register for Mentorship</button>
            </form>
        </div>
    <?php elseif ($isRegistered && $user['status'] === 'member'): ?>
        <!-- Show Cancel Registration button if registered -->
        <div class="cancel-mentorship-section" style="margin-top: 20px;">
            <form method="POST">
                <button type="submit" name="cancel_mentorship" class="btn btn-danger">Cancel Registration</button>
            </form>
        </div>
    <?php endif; ?>

    <br/>
    <a href="mentorships.php" class="btn btn-primary">Back to Mentorship List</a>

    <!-- Task List (only for registered mentees) -->
    <?php if ($isRegistered && $user['status'] === 'member'): ?>
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
                    <button type="submit" class="btn btn-secondary" <?php echo empty(array_filter($tasks, function ($task) { return !$task['is_completed']; })) ? 'disabled' : ''; ?>>Mark as Completed</button>
                </form>
            <?php else: ?>
                <p>No tasks assigned yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<br><h3>Feedback and Ratings</h3>

<!-- Feedback Form -->
<?php if ($isRegistered): ?>
    <form method="POST">
        <textarea class="custom-textbox feedback-textbox" name="feedback" placeholder="Type your feedback here" required></textarea>
        <br/>
        <select class="custom-dropdown" name="rating" required>
            <option value="">Select Rating</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        <br/><br/>
        <button type="submit" class="btn btn-primary" name="feedback_submit">Submit Feedback</button>
    </form>
<?php else: ?>
    <p style="color:red;">You must be registered for this mentorship to submit feedback.</p>
<?php endif; ?>

<!-- Display Average Rating -->
<?php if ($averageRating): ?>
    <p><strong>Average Rating:</strong> <?php echo round($averageRating, 2); ?> / 5</p>
<?php endif; ?>

<!-- Display Feedback List -->
<ul>
    <?php foreach ($feedbackList as $feedback): ?>
        <li>
            <strong><?php echo htmlspecialchars($feedback['full_name']); ?>:</strong>
            <?php echo htmlspecialchars($feedback['feedback']); ?>
            (Rating: <?php echo htmlspecialchars($feedback['rating']); ?>)
        </li>
    <?php endforeach; ?>
</ul>

<?php include_once "includes/footer.php"; ?>
