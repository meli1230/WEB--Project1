<?php

include_once "config/database.php";
include_once "includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

$database = new Database();
$db = $database->getConnection(); // Get the database connection object

$jobId = $_GET['id'];

// Fetch the job's details
$query = "SELECT title, company, type, experience_level FROM jobs WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$jobId]);

$job = $stmt->fetch(PDO::FETCH_ASSOC);

// Apply to Job button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $userId = $_SESSION['user_id'];

    // Check if the user has already applied to this job
    $checkQuery = "SELECT * FROM job_applications WHERE job_id = ? AND member_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$jobId, $userId]);

    if ($checkStmt->rowCount() > 0) {
        echo "<p style='color:orange;'>You have already applied to this job.</p>";
    } else {
        // Insert into job_applications table
        $applyQuery = "INSERT INTO job_applications (job_id, member_id) VALUES (?, ?)";
        $applyStmt = $db->prepare($applyQuery);

        try {
            $applyStmt->execute([$jobId, $userId]);
            echo "<p style='color:green;'>You have successfully applied to this job.</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error applying to the job: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Fetch the list of applicants
$applicantsQuery = "SELECT CONCAT(members.first_name, ' ', members.last_name) AS full_name 
                    FROM job_applications 
                    INNER JOIN members ON job_applications.member_id = members.id 
                    WHERE job_applications.job_id = ?";
$applicantsStmt = $db->prepare($applicantsQuery);
$applicantsStmt->execute([$jobId]);
$applicants = $applicantsStmt->fetchAll(PDO::FETCH_ASSOC);

// Display "N/A" for missing values
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

    <!-- Apply to Job Button & Back to Jobs List -->
    <div class="d-flex align-items-center">
        <form method="POST">
            <button type="submit" name="apply" class="btn btn-primary">Apply to Job</button>
        </form>
        &nbsp;&nbsp; <!-- Add space between buttons -->
        <a href="jobs.php" class="btn btn-primary">Back to Jobs List</a>
    </div>

    <br/>
    <br/>
    <!-- List of Applicants -->
    <div class="applicants-section">
        <h3>Applied to this job:</h3>
        <?php if (count($applicants) > 0): ?>
            <ul>
                <?php foreach ($applicants as $applicant): ?>
                    <li><?php echo htmlspecialchars($applicant['full_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No applicants yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
