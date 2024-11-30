<?php
include_once "config/database.php";

if (isset($_GET['id'])) { // Check if the "id" parameter is set in the GET request
    $database = new Database(); // Create a new instance of the database class to establish connection
    $db = $database->getConnection(); // Get the database connection object

    // Prepare the SQL query to delete a mentorship
    $query = "DELETE FROM mentorships WHERE id = ?";
    $stmt = $db->prepare($query); // Prepare the SQL query for execution

    try {
        $stmt->execute([$_GET['id']]); // Execute the query, binding the "id" parameter from the GET request to the placeholder
        header("Location: mentorships.php?message=Mentorship deleted successfully"); // Redirect to the mentorships page with a success message
    } catch (PDOException $e) {
        // Redirect with an error message in case of failure
        header("Location: mentorships.php?error=Unable to delete mentorship. " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: mentorships.php?error=No mentorship ID provided."); // Redirect with an error message if ID is not provided
}
exit(); // Stop further script execution after the redirect
?>
