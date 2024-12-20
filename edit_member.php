<?php

include_once "config/database.php";
include_once "includes/header.php";

$database = new Database(); //create a new instance of the database
$db = $database->getConnection(); //get the database connection object

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //check if the form is submitted using POST method
    $query = "UPDATE members 
                SET first_name=?, 
                    last_name=?, 
                    email=?, 
                    pswd = ?,
                    status = ?,
                    profession=?,
                    company=?, 
                    expertise=?, 
                    linkedin_profile=?,
                    studies=?
                WHERE id=?"; //sql query
                        //? -> using the placeholders ensures the query is protected against SQL injection, as values are bound securely; allows to update multiple columns
                                    // dynamically based on user input
    $stmt = $db->prepare($query); //prepare the SQL query for execution
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
        $_GET['id']]); //execute the query
    header("Location: members.php"); //redirect to the members page after execution
    exit(); //stop further execution
}

$query = "SELECT * FROM members WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="form-container"><h2>Edit Member</h2>
    <form method="POST">
        <div class="form-group">
            <label>First Name</label>
            <!--display member's first name using HTML-escaped values (prevents XSS = Cross-Site Scripting)-->
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($member['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pswd" class="form-control" value="<?php echo htmlspecialchars($member['pswd']); ?>" required>
        </div>
        <div class="form-group">
            <label>Account type</label>
            <br/>
            <select name="status" class="custom-dropdown" required>
                <option value="member" <?php echo $member['status'] === 'member' ? 'selected' : ''; ?>>Member</option>
                <option value="mentor" <?php echo $member['status'] === 'mentor' ? 'selected' : ''; ?>>Mentor</option>
            </select>
        </div>
        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control" value="<?php echo htmlspecialchars($member['profession']); ?>">
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($member['company']); ?>">
        </div>
        <div class="form-group">
            <label>Expertise</label>
            <textarea name="expertise" class="form-control"><?php echo htmlspecialchars($member['expertise']); ?> </textarea>
        </div>
        <div class="form-group">
            <label>LinkedIn Profile</label>
            <input type="url" name="linkedin_profile" class="form-control" value="<?php echo htmlspecialchars($member['linkedin_profile']); ?>">
        </div>
        <div class="form-group">
            <label>Studies</label>
            <input type="text" name="studies" class="form-control" value="<?php echo htmlspecialchars($member['studies']); ?>">
        </div>
        <br/>
        <button type="submit" class="btn btn-primary">Update Member</button>
    </form>
</div>
<?php include_once "includes/footer.php"; ?>