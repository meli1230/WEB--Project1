<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!--encoding type; enables the representation of text in any language-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--viewport controls how the page is displayed on mobile and responsive devices-->
    <!--initial-scale=1.0 -> sets the default zoom level to 100% (no zoom) when the page loads-->
    <title>Women Techpower Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!--defines the bootstrap style from the source-->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Left Section -->
        <div class="navbar-left d-flex align-items-center">
            <button id="dark-mode-toggler" class="btn btn-darkmode">Dark Mode</button>
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="attachments/logo.png" alt="Logo" class="navbar-logo">
                Women Techpower Platform
            </a>
        </div>

        <!-- Toggler Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Navbar -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Center Section -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="members.php">Members</a></li>
                <li class="nav-item"><a class="nav-link" href="mentors.php">Mentors</a></li>
                <li class="nav-item"><a class="nav-link" href="jobs.php">Jobs</a></li>
                <li class="nav-item"><a class="nav-link" href="add_job.php">Add Job</a></li>
                <li class="nav-item"><a class="nav-link" href="resource_hub.php">Resource Hub</a></li>
                <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="add_event.php">Add Event</a></li>
                <li class="nav-item"><a class="nav-link" href="mentorships.php">Mentorship</a></li>
                <li class="nav-item"><a class="nav-link" href="add_mentorship.php">Add Mentorship Slot</a></li>
            </ul>

            <!-- Right Section -->
            <ul class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="add_member.php">Register</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="account_details.php">Account</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <!-- Welcome Text Now Inside the Right Section -->
                    <span class="navbar-text text-light ml-3">
                        Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!
                    </span>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<div class="container mt-4"> <!--mt4 = margin-top of 4 units-->
