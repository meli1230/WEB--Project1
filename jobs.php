<?php
include_once "config/database.php";
include_once "includes/header.php";

// Page configuration
$jobs_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $jobs_per_page;

$database = new Database();
$db = $database->getConnection();

// Filtering logic
$companyFilter = isset($_GET['company']) ? $_GET['company'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$experienceLevelFilter = isset($_GET['experience_level']) ? $_GET['experience_level'] : '';

// Search functionality
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

$query = "SELECT * FROM jobs WHERE 1=1";

// Add filters to query
if ($companyFilter) {
    $query .= " AND company = :company";
}
if ($typeFilter) {
    $query .= " AND type = :type";
}
if ($experienceLevelFilter) {
    $query .= " AND experience_level = :experience_level";
}

// Add search criteria to query
if ($searchQuery) {
    $query .= " AND (title LIKE :search OR company LIKE :search)";
}

$query .= " LIMIT :offset, :jobs_per_page";

$stmt = $db->prepare($query);

// Bind parameters
if ($companyFilter) {
    $stmt->bindParam(":company", $companyFilter, PDO::PARAM_STR);
}
if ($typeFilter) {
    $stmt->bindParam(":type", $typeFilter, PDO::PARAM_STR);
}
if ($experienceLevelFilter) {
    $stmt->bindParam(":experience_level", $experienceLevelFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $searchTerm = "%$searchQuery%"; //% = wildcard symbol for matching the search
    $stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":jobs_per_page", $jobs_per_page, PDO::PARAM_INT);

$stmt->execute();

// Get total jobs count for pagination
$countQuery = "SELECT COUNT(*) as total_jobs FROM jobs WHERE 1=1";

if ($companyFilter) {
    $countQuery .= " AND company = :company";
}
if ($typeFilter) {
    $countQuery .= " AND type = :type";
}
if ($experienceLevelFilter) {
    $countQuery .= " AND experience_level = :experience_level";
}
if ($searchQuery) {
    $countQuery .= " AND (title LIKE :search OR company LIKE :search)";
}

$countStmt = $db->prepare($countQuery);
if ($companyFilter) {
    $countStmt->bindParam(":company", $companyFilter, PDO::PARAM_STR);
}
if ($typeFilter) {
    $countStmt->bindParam(":type", $typeFilter, PDO::PARAM_STR);
}
if ($experienceLevelFilter) {
    $countStmt->bindParam(":experience_level", $experienceLevelFilter, PDO::PARAM_STR);
}
if ($searchQuery) {
    $countStmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$countStmt->execute();
$total_jobs = $countStmt->fetch(PDO::FETCH_ASSOC)['total_jobs'];
$total_pages = ceil($total_jobs / $jobs_per_page); //ceil -> round up to the nearest integer

?>

<h2>Jobs Board</h2>

<!-- Filter options -->
<div class="filter-options">
    <form method="GET">
        <select name="type" class="custom-dropdown" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="fulltime" <?php echo $typeFilter == 'fulltime' ? 'selected' : ''; ?>>Full-time</option>
            <option value="parttime" <?php echo $typeFilter == 'parttime' ? 'selected' : ''; ?>>Part-time</option>
            <option value="internship" <?php echo $typeFilter == 'internship' ? 'selected' : ''; ?>>Internship</option>
        </select>
        <select name="experience_level" class="custom-dropdown" onchange="this.form.submit()">
            <option value="">All Levels</option>
            <option value="junior" <?php echo $experienceLevelFilter == 'junior' ? 'selected' : ''; ?>>Junior</option>
            <option value="senior" <?php echo $experienceLevelFilter == 'senior' ? 'selected' : ''; ?>>Senior</option>
            <option value="expert" <?php echo $experienceLevelFilter == 'expert' ? 'selected' : ''; ?>>Expert</option>
        </select>
        <input type="hidden" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>">
    </form>
</div>
<br/>

<!-- Search functionality -->
<div class="search-options">
    <form method="GET">
        <input type="text" class="custom-textbox" name="query" placeholder="Search jobs..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="jobs.php" class="btn btn-primary">Reset</a>
    </form>
</div>
<br/>

<!-- Job cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card member-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">
                        <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                        <br>
                        <strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?>
                    </p>
                    <a href="details_job.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                    <?php if (isset($_SESSION['status']) && ($_SESSION['status'] === 'admin' || $_SESSION['status'] === 'mentor')): ?>
                        <a href="edit_job.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                        <a href="delete_job.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
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
