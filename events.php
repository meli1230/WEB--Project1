<?php
include_once "config/database.php";
include_once "includes/header.php";

// Pagination variables
$events_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $events_per_page;

$database = new Database();
$db = $database->getConnection();

// Sorting logic
$order_by = "event_date ASC"; // Sort by event date (soonest first)

// Filtering logic
$locationFilter = isset($_GET['location']) ? strtolower(trim($_GET['location'])) : '';

// Base query
$query = "SELECT * FROM events";
if ($locationFilter === 'online') {
    $query .= " WHERE LOWER(location) = 'online'";
} elseif ($locationFilter === 'offline') {
    $query .= " WHERE LOWER(location) != 'online'";
}
$query .= " ORDER BY $order_by LIMIT :offset, :events_per_page";

$stmt = $db->prepare($query);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":events_per_page", $events_per_page, PDO::PARAM_INT);
$stmt->execute();

// Get total events count for pagination
$countQuery = "SELECT COUNT(*) as total_events FROM events";
if ($locationFilter === 'online') {
    $countQuery .= " WHERE LOWER(location) = 'online'";
} elseif ($locationFilter === 'offline') {
    $countQuery .= " WHERE LOWER(location) != 'online'";
}
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$total_events = $countStmt->fetch(PDO::FETCH_ASSOC)['total_events'];
$total_pages = ceil($total_events / $events_per_page);
?>

<h2>Events Directory</h2>
<br/>

<!-- Filter by location -->
<div class="filter-options">
    <form method="GET">
        <label for="location">Filter by Location:</label>
        <select name="location" id="location" class="custom-dropdown" onchange="this.form.submit()">
            <option value="" <?php echo $locationFilter === '' ? 'selected' : ''; ?>>All Locations</option>
            <option value="online" <?php echo $locationFilter === 'online' ? 'selected' : ''; ?>>Online</option>
            <option value="offline" <?php echo $locationFilter === 'offline' ? 'selected' : ''; ?>>Offline</option>
        </select>
    </form>
</div>
<br/>

<!-- Event cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card member-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">
                        <strong>Date and Time:</strong> <?php echo htmlspecialchars($row['event_date']); ?><br>
                        <strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?>
                    </p>
                    <a href="details_event.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                    <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                    <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<br/>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?location=<?php echo htmlspecialchars($locationFilter); ?>&page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?location=<?php echo htmlspecialchars($locationFilter); ?>&page=<?php echo $i; ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?location=<?php echo htmlspecialchars($locationFilter); ?>&page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
