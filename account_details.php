<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database(); // Create a new database instance
$db = $database->getConnection(); // Get the database connection object


// Ensure the user is logged in and fetch their `id` from the session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session during login

// Handle form submission to update the user's details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "UPDATE members 
              SET first_name=?, 
                  last_name=?, 
                  email=?, 
                  pswd=?, 
                  status=?, 
                  profession=?, 
                  company=?, 
                  expertise=?, 
                  linkedin_profile=?, 
                  studies=? 
              WHERE id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['pswd'],
        $_POST['status'],
        $_POST['profession'],
        $_POST['company'],
        $_POST['expertise'],
        $_POST['linkedin_profile'],
        $_POST['studies'],
        $user_id
    ]);

    // Redirect or display success message
    echo "<p>Your account details have been updated successfully!</p>";
}

// Fetch the user's current details
$query = "SELECT * FROM members WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch account recommendations (users with the same profession)
$recommendations = [];
if (!empty($user['profession'])) {
    $query = "SELECT first_name, last_name FROM members WHERE profession = ? AND id != ? LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute([$user['profession'], $user_id]);
    $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="form-container">
    <h2>Account Details</h2>
    <form method="POST">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pswd" class="form-control" value="<?php echo htmlspecialchars($user['pswd']); ?>" required>
        </div>
        <div class="form-group">
            <label>Account Type</label>
            <br />
            <select name="status" class="custom-dropdown" required>
                <option value="member" <?php echo $user['status'] === 'member' ? 'selected' : ''; ?>>Member</option>
                <option value="mentor" <?php echo $user['status'] === 'mentor' ? 'selected' : ''; ?>>Mentor</option>
            </select>
        </div>
        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control" value="<?php echo htmlspecialchars($user['profession']); ?>">
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($user['company']); ?>">
        </div>
        <div class="form-group">
            <label>Expertise</label>
            <textarea name="expertise" class="form-control"><?php echo htmlspecialchars($user['expertise']); ?></textarea>
        </div>
        <div class="form-group">
            <label>LinkedIn Profile</label>
            <input type="url" name="linkedin_profile" class="form-control" value="<?php echo htmlspecialchars($user['linkedin_profile']); ?>">
        </div>
        <div class="form-group">
            <label>Studies</label>
            <input type="text" name="studies" class="form-control" value="<?php echo htmlspecialchars($user['studies']); ?>">
        </div>
        <br />
        <button type="submit" class="btn btn-primary">Update Account</button>
    </form>
</div>


<!--Account recommendations-->
<div class="form-container">
    <h3>Account Recommendations</h3>
    <?php if (count($recommendations) > 0): ?>
        <ul>
            <?php foreach ($recommendations as $recommendation): ?>
                <li><?php echo htmlspecialchars($recommendation['first_name'] . ' ' . $recommendation['last_name']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recommendations available at the moment.</p>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
