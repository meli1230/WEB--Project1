<?php
include_once "config/database.php";

session_start();

// Check if the user is logged in and has admin or mentor privileges
if (!isset($_SESSION['status']) || ($_SESSION['status'] !== 'admin' && $_SESSION['status'] !== 'mentor')) {
    // Redirect unauthorized users to an error page
    header("Location: error.php?message=AccessDenied");
    exit();
}

if (isset($_GET['id'])) { //check if the "id" parameter is set in the GET request
    $database = new Database(); //create new instance of the database class to establish connection
    $db = $database->getConnection(); //get the database connection object
    $query = "DELETE FROM members WHERE id = ?"; //sql query
    $stmt = $db->prepare($query); //prepare SQL query for execution
    $stmt->execute([$_GET['id']]); //execute the query, binding the "id" parameter from the GET request to the placeholder
}
header("Location: members.php"); //redirect to the members page
exit(); //stop further script execution after the redirect

?>