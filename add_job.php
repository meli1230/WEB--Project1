<?php

include_once "config/database.php";
include_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted using POST method
    $database = new Database(); // Create a new instance of the database
    $db = $database->getConnection(); // Get the database connection object

    // Insert new job posting
    $query = "INSERT INTO jobs (
                     title, 
                     company, 
                     type, 
                     experience_level) 
               VALUES (?, ?, ?, ?)"; // SQL query

    $stmt = $db->prepare($query); // Prepare the SQL query for execution
    $stmt->execute([
        $_POST['title'],
        $_POST['company'],
        $_POST['type'],
        $_POST['experience_level']
    ]); // Execute the query

    header("Location: jobs.php"); // Redirect to the jobs page after execution
    exit(); // Stop further execution
}

?>

<div class="form-container">
    <h2>Add Job</h2>
    <form method="POST">
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Job Type</label>
            <br/>
            <select name="type" class="custom-dropdown" required>
                <option value="fulltime">Full-time</option>
                <option value="parttime">Part-time</option>
                <option value="internship">Internship</option>
            </select>
        </div>
        <div class="form-group">
            <label>Experience Level</label>
            <br/>
            <select name="experience_level" class="custom-dropdown" required>
                <option value="junior">Junior</option>
                <option value="senior">Senior</option>
                <option value="expert">Expert</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Job</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>
