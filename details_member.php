<?php

include_once "config/database.php";
include_once "includes/header.php";

// Check if a valid member ID is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p style='color:red;'>No member ID provided.</p>";
    include_once "includes/footer.php";
    exit();
}

$memberId = $_GET['id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch the member's details
$query = "SELECT first_name, last_name, email, status, profession, company, expertise, linkedin_profile, studies 
          FROM members WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$memberId]);

if ($stmt->rowCount() == 0) {
    echo "<p style='color:red;'>Member not found.</p>";
    include_once "includes/footer.php";
    exit();
}

// Fetch the member data
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// Helper function to display "N/A" for missing values
function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : "N/A";
}

?>



<div class="details-container">
    <h2>Member Details</h2>
    <table class="details-table">
        <tr>
            <th>First Name:</th>
            <td><?php echo displayValue($member['first_name']); ?></td>
        </tr>
        <tr>
            <th>Last Name:</th>
            <td><?php echo displayValue($member['last_name']); ?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?php echo displayValue($member['email']); ?></td>
        </tr>
        <tr>
            <th>Account Type:</th>
            <td><?php echo displayValue($member['status']); ?></td>
        </tr>
        <tr>
            <th>Profession:</th>
            <td><?php echo displayValue($member['profession']); ?></td>
        </tr>
        <tr>
            <th>Company:</th>
            <td><?php echo displayValue($member['company']); ?></td>
        </tr>
        <tr>
            <th>Expertise:</th>
            <td><?php echo nl2br(displayValue($member['expertise'])); ?></td>
        </tr>
        <tr>
            <th>LinkedIn Profile:</th>
            <td>
                <?php
                $linkedin = displayValue($member['linkedin_profile']);
                echo $linkedin !== "N/A"
                    ? "<a href=\"$linkedin\" target=\"_blank\">$linkedin</a>"
                    : $linkedin;
                ?>
            </td>
        </tr>
        <tr>
            <th>Studies:</th>
            <td><?php echo displayValue($member['studies']); ?></td>
        </tr>
    </table>
    <a href="members.php" class="btn btn-primary">Back to Members List</a>
</div>

<?php include_once "includes/footer.php"; ?>
