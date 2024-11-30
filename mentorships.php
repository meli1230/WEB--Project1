<?php
include_once "config/database.php";
include_once "includes/header.php";

// Pagination variables
$mentorships_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $mentorships_per_page;

$database = new Database();
$db = $database->getConnection();

// Fetch mentorships
$query = "SELECT m1.first_name AS mentor_first_name, 
                 m1.last_name AS mentor_last_name, 
                 m2.first_name AS member_first_name, 
                 m2.last_name AS member_last_name, 
                 mentorships.time_slot, 
                 mentorships.id 
          FROM mentorships 
          JOIN members m1 ON mentorships.mentor_id = m1.id 
          LEFT JOIN members m2 ON mentorships.member_id = m2.id -- Use LEFT JOIN to include NULL member_id
          ORDER BY mentorships.time_slot ASC 
          LIMIT :offset, :mentorships_per_page";

$stmt = $db->prepare($query);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":mentorships_per_page", $mentorships_per_page, PDO::PARAM_INT);
$stmt->execute();

// Get total mentorship count for pagination
$countQuery = "SELECT COUNT(*) as total_mentorships FROM mentorships";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$total_mentorships = $countStmt->fetch(PDO::FETCH_ASSOC)['total_mentorships'];
$total_pages = ceil($total_mentorships / $mentorships_per_page);
?>

<h2>Mentorship Directory</h2>
<br/>

<!-- Mentorship cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card member-card">
                <div class="card-body">
                    <h5 class="card-title">
                        Mentorship Session by <?php echo htmlspecialchars($row['mentor_first_name'] . ' ' . $row['mentor_last_name']); ?>
                    </h5>
                    <p class="card-text">
                        <strong>Time Slot:</strong> <?php echo htmlspecialchars($row['time_slot']); ?><br>
                        <strong>Claimed By:</strong>
                        <?php
                        echo isset($row['member_first_name']) ?
                            htmlspecialchars($row['member_first_name'] . ' ' . $row['member_last_name']) :
                            "Available";
                        ?>
                    </p>
                    <a href="details_mentorship.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                    <a href="edit_mentorship.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                    <a href="delete_mentorship.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure you want to delete this mentorship session?')">Delete</a>
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
