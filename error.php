<?php

include_once "config/database.php";
include_once "includes/header.php";
?>

<div class="container mt-5">
    <div class="alert alert-danger text-center">
        <h1>Access Denied</h1>
        <p>You do not have the necessary permissions to access this page.</p>
        <a href="index.php" class="btn btn-primary">Go to Home</a>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
