<?php
include_once "config/database.php";
include_once "includes/header.php";

$resourceType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Number of resources per page
$resources_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
if ($page < 1) $page = 1; // Ensure page number is valid
$offset = ($page - 1) * $resources_per_page;

$database = new Database(); // Create a new instance of the database class
$db = $database->getConnection(); // Get the database connection object

// Table mapping based on resource type
$tableMap = [
    'tutorials' => 'tutorials',
    'videos' => 'video_materials',
    'podcasts' => 'podcasts',
    'downloads' => 'downloadables'
];

// Search functionality
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($resourceType === 'all') {
    // Build a UNION query for all tables
    $queries = [];
    foreach ($tableMap as $key => $tableName) {
        $subQuery = "SELECT title, link, '$key' AS resource_type FROM $tableName WHERE 1=1"; //key is a constant string in the query result for every row
        if ($searchQuery) {
            $subQuery .= " AND (title LIKE :search)";
        }
        $queries[] = $subQuery;
    }
    $query = implode(" UNION ", $queries) . " ORDER BY title LIMIT :offset, :resources_per_page";
} else {
    $table = isset($tableMap[$resourceType]) ? $tableMap[$resourceType] : null;
    if ($table) {
        $query = "SELECT title, link, '$resourceType' AS resource_type FROM $table WHERE 1=1";
        if ($searchQuery) {
            $query .= " AND (title LIKE :search)";
        }
        $query .= " ORDER BY id DESC LIMIT :offset, :resources_per_page";
    }
}


$stmt = $db->prepare($query);

// Bind parameters
if ($searchQuery) {
    $searchTerm = "%$searchQuery%";
    $stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":resources_per_page", $resources_per_page, PDO::PARAM_INT);

$stmt->execute();

// Get total resource count for pagination
if ($resourceType === 'all') {
    $countQueries = [];
    foreach ($tableMap as $key => $tableName) {
        $countSubQuery = "SELECT COUNT(*) AS total_resources FROM $tableName WHERE 1=1";
        if ($searchQuery) {
            $countSubQuery .= " AND (title LIKE :search)";
        }
        $countQueries[] = $countSubQuery;
    }
    $countQuery = implode(" UNION ALL ", $countQueries);
    $countStmt = $db->prepare("SELECT SUM(total_resources) AS total_resources FROM ($countQuery) AS total_counts");
} else {
    $countQuery = "SELECT COUNT(*) as total_resources FROM $table WHERE 1=1";
    if ($searchQuery) {
        $countQuery .= " AND (title LIKE :search)";
    }
    $countStmt = $db->prepare($countQuery);
}

if ($searchQuery) {
    $countStmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
}
$countStmt->execute();
$total_resources = $countStmt->fetch(PDO::FETCH_ASSOC)['total_resources'];
$total_pages = ceil($total_resources / $resources_per_page);
?>

<h2>Resource Hub</h2>

<!-- Filter options -->
<div class="filter-options">
    <form method="GET">
        <select name="type" id="type" class="custom-dropdown" onchange="this.form.submit()">
            <option value="all" <?php echo $resourceType === 'all' ? 'selected' : ''; ?>>All Resources</option>
            <option value="tutorials" <?php echo $resourceType === 'tutorials' ? 'selected' : ''; ?>>Articles and Tutorials</option>
            <option value="videos" <?php echo $resourceType === 'videos' ? 'selected' : ''; ?>>Video Materials</option>
            <option value="podcasts" <?php echo $resourceType === 'podcasts' ? 'selected' : ''; ?>>Podcasts</option>
            <option value="downloads" <?php echo $resourceType === 'downloads' ? 'selected' : ''; ?>>Downloadable Resources</option>
        </select>
        <input type="hidden" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>">
    </form>
</div>
<br/>

<!-- Search functionality -->
<div class="search-options">
    <form method="GET">
        <input type="text" class="custom-textbox" name="query" placeholder="Search resources..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($resourceType); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="resource_hub.php" class="btn btn-primary">Reset</a>
    </form>
</div>
<br/>

<!--Cards-->
<div class="row">
    <?php if ($stmt): ?>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-4">
                <div class="card member-card resource-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <?php if (isset($row['resource_type']) && $row['resource_type'] === 'downloads'): ?>
                            <!-- Download button for downloadables -->
                            <a href="download.php?file=<?php echo urlencode($row['link']); ?>" class="btn btnprimary">Download</a>
                        <?php else: ?>
                            <!-- View button for other resources -->
                            <a href="<?php echo htmlspecialchars($row['link']); ?>" class="btn btnprimary" target="_blank">View</a>
                        <?php endif; ?>
                    </div>
                </div>
                <br/>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No resources found.</p>
    <?php endif; ?>
</div>

<br/>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?type=<?php echo htmlspecialchars($resourceType); ?>&page=<?php echo $page - 1; ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?type=<?php echo htmlspecialchars($resourceType); ?>&page=<?php echo $i; ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-<?php echo $i == $page ? 'primary' : 'secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?type=<?php echo htmlspecialchars($resourceType); ?>&page=<?php echo $page + 1; ?>&query=<?php echo htmlspecialchars($searchQuery); ?>" class="btn btn-secondary">Next</a>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>
