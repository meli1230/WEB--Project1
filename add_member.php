<?php

include_once "config/database.php";
include_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //check if the form is submitted using POST method
    $database = new Database(); //create a new instance of the database
    $db = $database->getConnection(); //get the database connection object

    $emailQuery = "SELECT id FROM members WHERE email = ?";
    $emailStmt = $db->prepare($emailQuery);
    $emailStmt->execute([$_POST['email']]);

    //Check if email already exists
    if($emailStmt->rowCount() > 0){
        $error = "Email already exists. Please use a different email.";
    } else {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    //Insert new user
    $query = "INSERT INTO members (
                     first_name, 
                     last_name, 
                     email, 
                     pswd,
                     status,
                     profession, 
                     company, 
                     expertise, 
                     linkedin_profile,
                     profile_picture) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //sql query
                        // ? -> placeholders that are part of a prepared statement in SQL; the values are safely inserted in the database at runtime
    $stmt = $db->prepare($query); //prepare the SQL query for execution
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $hashedPassword,
        $_POST['status'],
        $_POST['profession'],
        $_POST['company'],
        $_POST['expertise'],
        $_POST['linkedin_profile'],
        $_POST['profile_picture']]); //execute the query
    header("Location: login.php"); //redirect to the members page after execution
    exit(); //stop further execution
}

?>


<div class="form-container">
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" enctype="multipart/form-data"> <!--enctype necessary for file uploads-->
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pswd" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Account type</label>
            <select name="status" class="custom-dropdown" required>
                <option value="member" selected>Member</option>
                <option value="mentor">Mentor</option>
            </select>
        </div>
        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control">
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control">
        </div>
        <div class="form-group">
            <label>Expertise</label>
            <textarea name="expertise" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>LinkedIn Profile</label>
            <input type="url" name="linkedin_profile" class="form-control">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control" accept="image/*"
        </div>
        <button type="submit" class="btn btn-primary">Add Member</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>