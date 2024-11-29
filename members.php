<?php
include_once "config/database.php";
include_once "includes/header.php";

$members_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; //current page number
if ($page < 1) $page = 1; // ensure the page number is valid
$offset = ($page - 1) * $members_per_page;

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
$query = "SELECT * FROM members WHERE status = 'member'";

// Add profession filter to query
if ($professionFilter) {
    $query .= " AND profession = :profession";
}

// Add search criteria to query
if ($searchQuery) {
    $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR profession LIKE :search OR company LIKE :search)";
}

$query .= " ORDER BY $order_by LIMIT :offset, :members_per_page";

$stmt = $db->prepare($query);

// Bind parameters
if ($professionFilter) {
    $stmt->bindParam(":profession", $professionFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $searchTerm = "%$searchQuery%";
    $stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":members_per_page", $members_per_page, PDO::PARAM_INT);

$stmt->execute();

// Get total members count for pagination
$countQuery = "SELECT COUNT(*) as total_members FROM members WHERE status = 'member'";

if ($professionFilter) {
    $countQuery .= " AND profession = :profession";
}
if ($searchQuery) {
    $countQuery .= " AND (first_name LIKE :search OR last_name LIKE :search OR profession LIKE :search OR company LIKE :search)";
}

$countStmt = $db->prepare($countQuery);
if ($professionFilter) {
    $countStmt->bindParam(":profession", $professionFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $countStmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$countStmt->execute();
$total_members = $countStmt->fetch(PDO::FETCH_ASSOC)['total_members'];
$total_pages = ceil($total_members / $members_per_page);

// Fetch distinct professions for filtering
$professionQuery = "SELECT DISTINCT profession FROM members ORDER BY profession ASC";
$professionStmt = $db->prepare($professionQuery);
$professionStmt->execute();
?>

    <h2>Members Directory</h2>

    <!-- Sorting options -->
    <div class="sorting-options">
        <a href="?sort=name&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-primary">Sort by Name</a>
        <a href="?sort=date&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-primary">Sort by Date</a>
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
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <input type="hidden" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>">
        </form>
    </div>
    <br/>

    <!-- Search functionality -->
    <div class="search-options">
        <form method="GET">
            <input type="text" class="custom-textbox" name="query" placeholder="Type to search..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <input type="hidden" name="profession" value="<?php echo htmlspecialchars($professionFilter); ?>">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="members.php" class="btn btn-primary">Reset</a>
        </form>
    </div>
    <br/>

    <!-- Member cards -->
    <div class="row"> <!--row container for displaying member's cards-->
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?> <!--loop through each row fetched from the database-->
            <div class="col-md-4"> <!--create column for each member card-->
                <div class="card member-card"> <!--card element to display individual member details-->
                    <div class="card-body"> <!--main content of the card-->
                        <!--display member's full name using HTML-escaped values (prevents XSS = Cross-Site Scripting)-->
                        <h5 class="card-title"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h5>
                        <p class="card-text">
                            <!--display the member's profession and company-->
                            <strong>Profession:</strong> <?php echo htmlspecialchars($row['profession']); ?>
                            <br>
                            <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                        </p>
                        <!--link to member_details, passing their ID in the URL-->
                        <a href="details_member.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                        <!--link to edit_member, passing their ID in the URL-->
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                        <!--link to delete_member, passing their ID in the URL, with a confirmation dialog ('Are you sure?')-->
                        <a href="delete_member.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&profession=<?php echo htmlspecialchars($professionFilter); ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Next</a>
        <?php endif; ?>
    </div>
<?php include_once "includes/footer.php"; ?>