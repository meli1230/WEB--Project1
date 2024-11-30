<?php
include_once "config/database.php";

if (isset($_GET['id'])) { // Check if the "id" parameter is set in the GET request
    $database = new Database(); // Create a new instance of the database class to establish a connection
    $db = $database->getConnection(); // Get the database connection object
    $query = "DELETE FROM jobs WHERE id = ?"; // SQL query to delete a job based on its id
    $stmt = $db->prepare($query); // Prepare SQL query for execution
    $stmt->execute([$_GET['id']]); // Execute the query, binding the "id" parameter from the GET request to the placeholder
}
header("Location: jobs.php"); // Redirect to the jobs page
exit(); // Stop further script execution after the redirect
?>
