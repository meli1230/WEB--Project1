<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Fetch the mentorship details to ensure the logged-in user is the mentor
$query = "SELECT * FROM mentorships WHERE id = ? AND mentor_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id'], $user_id]);
$mentorship = $stmt->fetch(PDO::FETCH_ASSOC);

// Update mentorship
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update mentorship details
    if (!empty($_POST['time_slot'])) {
        $query = "UPDATE mentorships SET time_slot = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['time_slot'], $_GET['id']]);
    }

    // Tasks
    if (!empty($_POST['tasks'])) {
        foreach ($_POST['tasks'] as $task_id => $task_description) {
            if (!empty($task_description)) {
                $query = "UPDATE mentorship_progress SET task_description = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$task_description, $task_id]);
            }
        }
    }

    // Add new tasks if provided
    if (!empty($_POST['new_tasks'])) {
        foreach ($_POST['new_tasks'] as $new_task_description) {
            if (!empty($new_task_description)) {
                $query = "INSERT INTO mentorship_progress (mentorship_id, task_description, is_completed) VALUES (?, ?, 0)";
                $stmt = $db->prepare($query);
                $stmt->execute([$_GET['id'], $new_task_description]);
            }
        }
    }

    // Task deletion
    if (!empty($_POST['delete_task_id'])) {
        foreach ($_POST['delete_task_id'] as $task_id) {
            $query = "DELETE FROM mentorship_progress WHERE id = ? AND mentorship_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$task_id, $_GET['id']]);
        }
    }

    // Redirect to the members card after updating
    if (!empty($_POST['update'])) {
        header("Location: mentorships.php"); // Replace 'mentorships.php' with the members card URL if different
        exit();
    }
    header("Location: edit_mentorship.php?id=" . $_GET['id']);
    exit();
}

// Fetch existing progress tasks for this mentorship
$query = "SELECT * FROM mentorship_progress WHERE mentorship_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$progress_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="form-container">
    <h2>Edit Mentorship</h2>
    <form method="POST">
        <div class="form-group">
            <label>Time Slot</label>
            <input type="datetime-local" name="time_slot" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($mentorship['time_slot']))); ?>" required>
        </div>

        <h3>Progress Tracking</h3>
        <?php foreach ($progress_tasks as $task): ?>
            <div class="form-group">
                <label>Task</label>
                <div class="d-flex align-items-center">
                    <input type="text" name="tasks[<?php echo $task['id']; ?>]" class="form-control" value="<?php echo htmlspecialchars($task['task_description']); ?>">
                    <button type="submit" name="delete_task_id[]" value="<?php echo $task['id']; ?>" class="btn btn-danger btn-sm ml-2">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>

        <h4>Add New Tasks</h4>
        <div id="new-tasks-container">
            <div class="form-group">
                <input type="text" name="new_tasks[]" class="form-control" placeholder="New task description">
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addNewTask()">Add Another Task</button>
        <br/><br/>
        <button type="submit" name="update" value="1" class="btn btn-primary">Update Mentorship</button>
    </form>
</div>

<script>
    // JavaScript to dynamically add new task inputs
    function addNewTask() {
        const container = document.getElementById('new-tasks-container'); //element where the tasks inputs will be appended
        const newTaskDiv = document.createElement('div');
        newTaskDiv.className = 'form-group';
        newTaskDiv.innerHTML = '<input type="text" name="new_tasks[]" class="form-control" placeholder="New task description">';
        container.appendChild(newTaskDiv); //appends the div, making it part of the DOM
    }
</script>

<?php include_once "includes/footer.php"; ?>
