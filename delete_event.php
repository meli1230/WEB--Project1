<?php
include_once "config/database.php";

if (isset($_GET['id'])) { // Check if the "id" parameter is set in the GET request
    $database = new Database(); // Create a new instance of the database class to establish connection
    $db = $database->getConnection(); // Get the database connection object
    $query = "DELETE FROM events WHERE id = ?"; // SQL query to delete an event
    $stmt = $db->prepare($query); // Prepare the SQL query for execution
    $stmt->execute([$_GET['id']]); // Execute the query, binding the "id" parameter from the GET request to the placeholder
}
header("Location: events.php"); // Redirect to the events page
exit(); // Stop further script execution after the redirect
?>
