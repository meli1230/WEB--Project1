<?php
include_once "config/database.php";
include_once "includes/header.php";

// Ensure the user is logged in and fetch their ID
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id']; // Assuming the mentor's ID is stored in the session

$database = new Database();
$db = $database->getConnection();

// Verify if the logged-in user is a mentor
$query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM members WHERE id = ? AND status = 'mentor'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Access denied. You are not authorized to create mentorship slots.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert new mentorship slot
    $query = "INSERT INTO mentorships (
                     mentor_id, 
                     time_slot
                ) 
               VALUES (?, ?)";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([
            $user_id, // Automatically use the logged-in mentor's ID
            $_POST['time_slot']
        ]);
        header("Location: mentorships.php");
        exit();
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<div class="form-container">
    <h2>Create Mentorship Slot</h2>
    <p><strong>Mentor:</strong> <?php echo htmlspecialchars($mentor['name']); ?></p>
    <form method="POST">
        <div class="form-group">
            <label>Time Slot</label>
            <input type="datetime-local" name="time_slot" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Slot</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>