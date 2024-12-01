<?php
include_once "config/database.php";
include_once "includes/header.php";

// Determine page status (default is 'mentor')
$pageStatus = 'mentor'; // Default status
if (isset($_GET['status']) && in_array($_GET['status'], ['member', 'mentor'])) {
    $pageStatus = $_GET['status'];
}

$mentors_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; //current page number
if ($page < 1) $page = 1; // ensure the page number is valid
$offset = ($page - 1) * $mentors_per_page;

$database = new Database(); //create a new instance of the database class to establish a database connection
$db = $database->getConnection(); //get the database connection object

// Sorting logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : "";
$order_by = "created_at DESC"; // Default sorting
if ($sort == "name") {
    $order_by = "first_name ASC, last_name ASC";
} elseif ($sort == "date") {
    $order_by = "created_at DESC";
}

// Filtering logic
$professionFilter = isset($_GET['profession']) ? $_GET['profession'] : '';

// Search functionality
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

// Base query
$query = "SELECT * FROM members WHERE status = :status";

// Add profession filter to query
if ($professionFilter) {
    $query .= " AND profession = :profession";
}

// Add search criteria to query
if ($searchQuery) {
    $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR profession LIKE :search OR company LIKE :search)";
}

$query .= " ORDER BY $order_by LIMIT :offset, :mentors_per_page";

$stmt = $db->prepare($query);

// Bind parameters
$stmt->bindParam(":status", $pageStatus, PDO::PARAM_STR);
if ($professionFilter) {
    $stmt->bindParam(":profession", $professionFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $searchTerm = "%$searchQuery%";
    $stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":mentors_per_page", $mentors_per_page, PDO::PARAM_INT);

$stmt->execute();

// Get total mentors count for pagination
$countQuery = "SELECT COUNT(*) as total_mentors FROM members WHERE status = :status";

if ($professionFilter) {
    $countQuery .= " AND profession = :profession";
}
if ($searchQuery) {
    $countQuery .= " AND (first_name LIKE :search OR last_name LIKE :search OR profession LIKE :search OR company LIKE :search)";
}

$countStmt = $db->prepare($countQuery);
$countStmt->bindParam(":status", $pageStatus, PDO::PARAM_STR);
if ($professionFilter) {
    $countStmt->bindParam(":profession", $professionFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $countStmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$countStmt->execute();
$total_mentors = $countStmt->fetch(PDO::FETCH_ASSOC)['total_mentors'];
$total_pages = ceil($total_mentors / $mentors_per_page);

// Fetch distinct professions for filtering based on the page's status
$professionQuery = "SELECT DISTINCT profession FROM members WHERE status = :status ORDER BY profession ASC";
$professionStmt = $db->prepare($professionQuery);
$professionStmt->bindParam(':status', $pageStatus, PDO::PARAM_STR);
$professionStmt->execute();
?>

<h2><?php echo ucfirst($pageStatus); ?> Directory</h2>

<!-- Sorting options -->
<div class="sorting-options">
    <a href="?status=<?php echo htmlspecialchars($pageStatus); ?>&sort=name&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-primary">Sort by Name</a>
    <a href="?status=<?php echo htmlspecialchars($pageStatus); ?>&sort=date&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-primary">Sort by Date</a>
</div>
<br/>

<!-- Filter professions -->
<div class="filter-options">
    <form method="GET">
        <select name="profession" id="profession" class="custom-dropdown" onchange="this.form.submit()">
            <option value="">All Professions</option>
            <?php while ($row = $professionStmt->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo htmlspecialchars($row['profession']); ?>"
                    <?php echo $row['profession'] == $professionFilter ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['profession']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($pageStatus); ?>">
        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
        <input type="hidden" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>">
    </form>
</div>
<br/>

<!-- Search functionality -->
<div class="search-options">
    <form method="GET">
        <input type="text" class="custom-textbox" name="query" placeholder="Type to search..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($pageStatus); ?>">
        <input type="hidden" name="profession" value="<?php echo htmlspecialchars($professionFilter); ?>">
        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="mentors.php?status=<?php echo htmlspecialchars($pageStatus); ?>" class="btn btn-primary">Reset</a>
    </form>
</div>
<br/>

<!-- Mentors cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card member-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h5>
                    <p class="card-text">
                        <strong>Profession:</strong> <?php echo htmlspecialchars($row['profession']); ?>
                        <br>
                        <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                    <a href="details_member.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                    <?php if (isset($_SESSION['status']) && ($_SESSION['status'] === 'admin')): ?>
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                        <a href="delete_member.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?status=<?php echo htmlspecialchars($pageStatus); ?>&page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?status=<?php echo htmlspecialchars($pageStatus); ?>&page=<?php echo $i; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?status=<?php echo htmlspecialchars($pageStatus); ?>&page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Next</a>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
