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

// Base query
$query = "SELECT * FROM events ORDER BY $order_by LIMIT :offset, :events_per_page";
$stmt = $db->prepare($query);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":events_per_page", $events_per_page, PDO::PARAM_INT);
$stmt->execute();

// Get total events count for pagination
$countQuery = "SELECT COUNT(*) as total_events FROM events";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$total_events = $countStmt->fetch(PDO::FETCH_ASSOC)['total_events'];
$total_pages = ceil($total_events / $events_per_page);
?>

<h2>Events Directory</h2>
<br/>

<!-- Event cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card event-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">
                        <strong>Date and Time:</strong> <?php echo htmlspecialchars($row['event_date']); ?>
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
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
