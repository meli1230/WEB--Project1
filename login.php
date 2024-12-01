<?php
include_once "config/database.php";
include_once "includes/header.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){ //check if the form is submitted using POST method
    $email = $_POST['email'];
    $pswd = $_POST['password'];

    $database = new Database();//create a new instance of the database
    $db = $database->getConnection(); //get the database connection object

    $query = "SELECT id, pswd, first_name, last_name, status FROM members WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($pswd, $user['pswd'])) {
        //start session for the user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['status'] = $user['status'];

        header("Location: members.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

?>

    <div class="form-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pswd">Password:</label>
                <input type="password" name="pswd" id="pswd" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

<?php include_once "includes/footer.php"; ?>