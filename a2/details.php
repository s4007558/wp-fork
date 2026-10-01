<?php
$page_title = "Book Details";
include 'includes/db_connect.inc';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: books.php");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM books WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    header("Location: books.php");
    exit();
}

$yearVal = $book['year'] ?? ($book['publish_year'] ?? ($book['publication_year'] ?? 'N/A'));

include 'includes/header.inc';
?>

<div class="container my-5">
    <div class="row gx-5">
        <div class="col-md-5 mb-4">
            <img src="assets/images/covers/<?php echo htmlspecialchars($book['image_path']); ?>" class="img-fluid rounded-4 shadow w-100" alt="<?php echo htmlspecialchars($book['title']); ?>" style="max-height: 520px; object-fit: cover;">
        </div>
        <div class="col-md-7">
            <h1 class="font-righteous display-5 mb-1"><?php echo htmlspecialchars($book['title']); ?></h1>
            <p class="text-secondary fs-5 mb-3">by <?php echo htmlspecialchars($book['author']); ?></p>
            
            <?php 
            $status = strtolower($book['status']);
            $badgeClass = $status === 'available' ? 'bg-success' : ($status === 'reserved' ? 'bg-warning text-dark' : 'bg-secondary');
            ?>
            <span class="badge rounded-pill <?php echo $badgeClass; ?> px-3 py-2 mb-4 fs-6"><?php echo htmlspecialchars(ucfirst($book['status'])); ?></span>

            <div class="card bg-white text-dark p-4 border-0 rounded-4 shadow-sm mb-4">
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-4 fw-bold">Genre:</div>
                    <div class="col-8"><?php echo htmlspecialchars($book['genre']); ?></div>
                </div>
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-4 fw-bold">Publication Year:</div>
                    <div class="col-8"><?php echo htmlspecialchars($yearVal); ?></div>
                </div>
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-4 fw-bold">ISBN:</div>
                    <div class="col-8"><?php echo htmlspecialchars($book['isbn'] ?? ''); ?></div>
                </div>
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-4 fw-bold">Condition:</div>
                    <div class="col-8"><?php echo htmlspecialchars($book['book_condition'] ?? ''); ?></div>
                </div>
                <div class="row pb-2">
                    <div class="col-4 fw-bold align-self-center">Price:</div>
                    <div class="col-8 text-info fw-bold fs-4">$<?php echo number_format($book['price'], 2); ?></div>
                </div>
            </div>

            <h4 class="font-righteous mb-3">Description</h4>
            <p class="lh-lg"><?php echo nl2br(htmlspecialchars($book['description'] ?? '')); ?></p>
            
            <div class="mt-4 d-flex gap-3">
                <a href="books.php" class="btn btn-secondary rounded-pill px-4 d-flex align-items-center"><span class="material-icons me-2">arrow_back</span> Back to Books</a>
                <a href="add.php" class="btn btn-primary-custom rounded-pill px-4 d-flex align-items-center"><span class="material-icons me-2">add</span> Add Similar Book</a>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($conn);
include 'includes/footer.inc'; 
?>