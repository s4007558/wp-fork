<?php
$page_title = "Home";
include 'includes/db_connect.inc';
include 'includes/header.inc';

// Fetch the 4 most recently added books
$query = "SELECT book_id, title, author, genre, price, image_path FROM books ORDER BY id DESC LIMIT 4";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database Query Error: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!-- Carousel with 4 static images -->
<div id="heroCarousel" class="carousel slide border-bottom border-info border-3" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/covers/1.png" class="d-block w-100" alt="The Midnight Library" style="height: 420px; object-fit: cover; filter: brightness(0.65);">
            <div class="carousel-caption d-none d-md-block pb-4">
                <h2 class="font-righteous display-5 text-white">The Midnight Library</h2>
                <a href="details.php?id=1" class="btn btn-light rounded-pill px-4 fw-bold mt-2"><span class="material-icons align-middle me-1">visibility</span> View Details</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/images/covers/2.png" class="d-block w-100" alt="Project Hail Mary" style="height: 420px; object-fit: cover; filter: brightness(0.65);">
            <div class="carousel-caption d-none d-md-block pb-4">
                <h2 class="font-righteous display-5 text-white">Project Hail Mary</h2>
                <a href="details.php?id=2" class="btn btn-light rounded-pill px-4 fw-bold mt-2"><span class="material-icons align-middle me-1">visibility</span> View Details</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/images/covers/3.png" class="d-block w-100" alt="Dune" style="height: 420px; object-fit: cover; filter: brightness(0.65);">
            <div class="carousel-caption d-none d-md-block pb-4">
                <h2 class="font-righteous display-5 text-white">Dune</h2>
                <a href="details.php?id=3" class="btn btn-light rounded-pill px-4 fw-bold mt-2"><span class="material-icons align-middle me-1">visibility</span> View Details</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/images/covers/4.png" class="d-block w-100" alt="The Hobbit" style="height: 420px; object-fit: cover; filter: brightness(0.65);">
            <div class="carousel-caption d-none d-md-block pb-4">
                <h2 class="font-righteous display-5 text-white">The Hobbit</h2>
                <a href="details.php?id=4" class="btn btn-light rounded-pill px-4 fw-bold mt-2"><span class="material-icons align-middle me-1">visibility</span> View Details</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Dynamic Grid: Latest 4 Records -->
<div class="container my-5">
    <h3 class="mb-4 d-flex align-items-center font-righteous text-info"><span class="material-icons me-2">favorite</span> Featured Books</h3>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col">
                <div class="card h-100 bg-white text-dark shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/images/covers/<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top p-3" alt="<?php echo htmlspecialchars($row['title']); ?>" style="height: 280px; object-fit: contain;">
                    <div class="card-body d-flex flex-column px-4">
                        <h5 class="card-title font-righteous mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="card-text text-muted small mb-3"><?php echo htmlspecialchars($row['genre']); ?> &bull; <?php echo htmlspecialchars($row['author']); ?></p>
                        <p class="card-text fw-bold fs-5 mt-auto text-info">$<?php echo number_format($row['price'], 2); ?></p>
                        <a href="details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary-custom w-100 mt-3 rounded-pill d-flex justify-content-center align-items-center">
                            <span class="material-icons fs-6 me-2">visibility</span> View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($conn);
include 'includes/footer.inc'; 
?>