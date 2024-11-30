<?php

include_once "config/database.php";
include_once "includes/header.php";

// Check if a valid job ID is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red;'>No job ID provided.</p>";
    include_once "includes/footer.php";
    exit();
}

$jobId = $_GET['id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch the job's details
$query = "SELECT title, company, type, experience_level 
          FROM jobs WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$jobId]);

if ($stmt->rowCount() == 0) {
    echo "<p style='color:red;'>Job not found.</p>";
    include_once "includes/footer.php";
    exit();
}

// Fetch the job data
$job = $stmt->fetch(PDO::FETCH_ASSOC);

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
}

?>

<div class="details-container">
    <h2>Job Details</h2>
    <table class="details-table">
        <tr>
            <th>Job Title:</th>
            <td><?php echo displayValue($job['title']); ?></td>
        </tr>
        <tr>
            <th>Company:</th>
            <td><?php echo displayValue($job['company']); ?></td>
        </tr>
        <tr>
            <th>Job Type:</th>
            <td><?php echo displayValue($job['type']); ?></td>
        </tr>
        <tr>
            <th>Experience Level:</th>
            <td><?php echo displayValue($job['experience_level']); ?></td>
        </tr>
    </table>
    <a href="jobs.php" class="btn btn-primary">Back to Jobs List</a>
</div>

<?php include_once "includes/footer.php"; ?>
