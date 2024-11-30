<?php

include_once "config/database.php";
include_once "includes/header.php";

$database = new Database(); // Create a new instance of the database
$db = $database->getConnection(); // Get the database connection object

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted using POST method
    $query = "UPDATE jobs 
                SET title=?, 
                    company=?, 
                    type=?, 
                    experience_level=?
                WHERE id=?"; // SQL query to update job details
    $stmt = $db->prepare($query); // Prepare the SQL query for execution
    $stmt->execute([
        $_POST['title'],
        $_POST['company'],
        $_POST['type'],
        $_POST['experience_level'],
        $_GET['id']
    ]); // Execute the query with form data
    header("Location: jobs.php"); // Redirect to the jobs page after execution
    exit(); // Stop further execution
}

// Fetch the job details for editing
$query = "SELECT * FROM jobs WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="form-container">
    <h2>Edit Job</h2>
    <form method="POST">
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($job['company']); ?>" required>
        </div>
        <div class="form-group">
            <label>Job Type</label>
            <br/>
            <select name="type" class="custom-dropdown" required>
                <option value="fulltime" <?php echo $job['type'] === 'fulltime' ? 'selected' : ''; ?>>Full-time</option>
                <option value="parttime" <?php echo $job['type'] === 'parttime' ? 'selected' : ''; ?>>Part-time</option>
                <option value="internship" <?php echo $job['type'] === 'internship' ? 'selected' : ''; ?>>Internship</option>
            </select>
        </div>
        <div class="form-group">
            <label>Experience Level</label>
            <br/>
            <select name="experience_level" class="custom-dropdown" required>
                <option value="junior" <?php echo $job['experience_level'] === 'junior' ? 'selected' : ''; ?>>Junior</option>
                <option value="senior" <?php echo $job['experience_level'] === 'senior' ? 'selected' : ''; ?>>Senior</option>
                <option value="expert" <?php echo $job['experience_level'] === 'expert' ? 'selected' : ''; ?>>Expert</option>
            </select>
        </div>
        <br/>
        <button type="submit" class="btn btn-primary">Update Job</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>
