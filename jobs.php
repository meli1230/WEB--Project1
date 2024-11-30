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
$selectedCompanies = isset($_GET['company']) ? $_GET['company'] : [];
$selectedTypes = isset($_GET['type']) ? $_GET['type'] : [];
$selectedExperienceLevels = isset($_GET['experience_level']) ? $_GET['experience_level'] : [];
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

// Base query
$query = "SELECT * FROM jobs WHERE 1=1";

// Add filters to query
if (!empty($selectedCompanies)) {
    $placeholders = implode(',', array_fill(0, count($selectedCompanies), '?'));
    $query .= " AND company IN ($placeholders)";
}
if (!empty($selectedTypes)) {
    $placeholders = implode(',', array_fill(0, count($selectedTypes), '?'));
    $query .= " AND type IN ($placeholders)";
}
if (!empty($selectedExperienceLevels)) {
    $placeholders = implode(',', array_fill(0, count($selectedExperienceLevels), '?'));
    $query .= " AND experience_level IN ($placeholders)";
}

// Add search criteria to query
if ($searchQuery) {
    $query .= " AND (title LIKE ? OR company LIKE ?)";
}

$query .= " LIMIT :offset, :jobs_per_page";

$stmt = $db->prepare($query);

// Bind parameters
$bindValues = [];
if (!empty($selectedCompanies)) {
    $bindValues = array_merge($bindValues, $selectedCompanies);
}
if (!empty($selectedTypes)) {
    $bindValues = array_merge($bindValues, $selectedTypes);
}
if (!empty($selectedExperienceLevels)) {
    $bindValues = array_merge($bindValues, $selectedExperienceLevels);
}
if ($searchQuery) {
    $bindValues[] = "%$searchQuery%";
    $bindValues[] = "%$searchQuery%";
}
foreach ($bindValues as $index => $value) {
    $stmt->bindValue($index + 1, $value, PDO::PARAM_STR);
}
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":jobs_per_page", $jobs_per_page, PDO::PARAM_INT);

$stmt->execute();

// Get distinct values for filters
$companyStmt = $db->query("SELECT DISTINCT company FROM jobs ORDER BY company ASC");
$typeStmt = $db->query("SELECT DISTINCT type FROM jobs ORDER BY type ASC");
$experienceLevelStmt = $db->query("SELECT DISTINCT experience_level FROM jobs ORDER BY experience_level ASC");

// Get total jobs count for pagination
$countQuery = "SELECT COUNT(*) as total_jobs FROM jobs WHERE 1=1";

if (!empty($selectedCompanies)) {
    $placeholders = implode(',', array_fill(0, count($selectedCompanies), '?'));
    $countQuery .= " AND company IN ($placeholders)";
}
if (!empty($selectedTypes)) {
    $placeholders = implode(',', array_fill(0, count($selectedTypes), '?'));
    $countQuery .= " AND type IN ($placeholders)";
}
if (!empty($selectedExperienceLevels)) {
    $placeholders = implode(',', array_fill(0, count($selectedExperienceLevels), '?'));
    $countQuery .= " AND experience_level IN ($placeholders)";
}
if ($searchQuery) {
    $countQuery .= " AND (title LIKE ? OR company LIKE ?)";
}

$countStmt = $db->prepare($countQuery);
foreach ($bindValues as $index => $value) {
    $countStmt->bindValue($index + 1, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$total_jobs = $countStmt->fetch(PDO::FETCH_ASSOC)['total_jobs'];
$total_pages = ceil($total_jobs / $jobs_per_page);
?>

<h2>Jobs Board</h2>

<!-- Advanced Filters -->
<br/>
<div class="filter-options">
    <form method="GET">
        <div class="filter-grid">
            <!-- Filter by Company -->
            <div class="filter-column">
                <h5>Filter by Company</h5>
                <?php while ($row = $companyStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <label>
                        <input type="checkbox" class="custom-checkbox" name="company[]" value="<?php echo htmlspecialchars($row['company']); ?>"
                            <?php echo in_array($row['company'], $selectedCompanies) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($row['company']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>

            <!-- Filter by Job Type -->
            <div class="filter-column">
                <h5>Filter by Job Type</h5>
                <?php while ($row = $typeStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <label>
                        <input type="checkbox" class="custom-checkbox" name="type[]" value="<?php echo htmlspecialchars($row['type']); ?>"
                            <?php echo in_array($row['type'], $selectedTypes) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($row['type']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>

            <!-- Filter by Experience Level -->
            <div class="filter-column">
                <h5>Filter by Experience Level</h5>
                <?php while ($row = $experienceLevelStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <label>
                        <input type="checkbox" class="custom-checkbox" name="experience_level[]" value="<?php echo htmlspecialchars($row['experience_level']); ?>"
                            <?php echo in_array($row['experience_level'], $selectedExperienceLevels) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($row['experience_level']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Search button for filtering -->
        <div class="filter-button">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="jobs.php" class="btn btn-secondary">Reset Filters</a>
        </div>
    </form>
</div>

<br/>
<br/>

<!-- Job cards -->
<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card job-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">
                        <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                        <br>
                        <strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?>
                        <br>
                        <strong>Experience Level:</strong> <?php echo htmlspecialchars($row['experience_level']); ?>
                    </p>
                    <a href="details_job.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Details</a>
                    <a href="edit_job.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                    <a href="delete_job.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<br/>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="btn btn-secondary">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="btn btn-secondary">Next</a>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
