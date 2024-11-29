<?php
include_once "config/database.php";
include_once "includes/header.php";

$members_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; //current page number
if ($page < 1) $page = 1; // ensure the page number is valid
$offset = ($page - 1) * $members_per_page;

$database = new Database(); //create a new instance of the database class to establish a database connection
$db = $database->getConnection(); //get the database connection object

// Get total number of members for pagination
$countQuery = "SELECT COUNT(*) as total_members FROM members";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$total_members = $countStmt->fetch(PDO::FETCH_ASSOC)['total_members'];
$total_pages = ceil($total_members / $members_per_page); // calculate total pages

$query = "SELECT * FROM members ORDER BY created_at DESC LIMIT :offset, :members_per_page"; //sql query
$stmt = $db->prepare($query); //prepare the SQL query for execution
    //1. SQL sent to the database engine
    //2. Parsing and syntax checking
    //3. Query optimization (creates an execution plan for the query
    //4. Precompilation (safely inserts actual values in to the placeholders, avoiding SQL injection risks
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':members_per_page', $members_per_page, PDO::PARAM_INT);
$stmt->execute(); //execute the sql query

?>

<h2>Members Directory</h2>

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

                    <!--link to edit_member, passing their ID in the URL-->
                    <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btnprimary">Edit</a>
                    <!--link to delete_member, passing their ID in the URL, with a confirmation dialog ('Are you sure?')-->
                    <a href="delete_member.php?id=<?php echo $row['id']; ?>" class="btn btndanger" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            </div>
        </div>
    <?php endwhile;
    ?>
</div>

<!--Pagination-->
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