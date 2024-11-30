<?php
// Include database configuration
include_once "config/database.php";

// Get the requested file identifier (link) from the URL
$fileIdentifier = isset($_GET['file']) ? $_GET['file'] : null;

if (!$fileIdentifier) {
    die("Invalid request. File not specified.");
}

// Create a database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Query the database to retrieve the file path
    $query = "SELECT link FROM downloadables WHERE link = :fileIdentifier";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":fileIdentifier", $fileIdentifier, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the file path from the database
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !isset($row['link'])) {
        die("File not found in the database.");
    }

    $filePath = $row['link']; // Relative file path stored in the database
    $absolutePath = realpath($filePath); // Resolve the absolute path

    // Validate the file path to prevent directory traversal attacks
    if ($absolutePath && file_exists($absolutePath)) {
        // Set headers to force file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . filesize($absolutePath));
        header('Pragma: public');

        // Clear output buffer and send the file for download
        flush();
        readfile($absolutePath);
        exit;
    } else {
        die("File not found on the server.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
