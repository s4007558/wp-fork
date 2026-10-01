<?php
$page_title = "Gallery";
include 'includes/db_connect.inc';
include 'includes/header.inc';

// Updated to pull author and publish_year (Change publish_year if your DB column is different!)
$query = "SELECT DISTINCT title, image_path, author, publish_year FROM books WHERE image_path IS NOT NULL AND image_path != '' ORDER BY title ASC";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database Error: " . mysqli_error($conn) . " - Check your column names!");
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container my-5">
    <h2 class="mb-4 d-flex align-items-center font-righteous">
        <span class="material-icons me-2 text-info">collections</span> Book Cover Gallery
    </h2>
    
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col">
                <div class="card h-100 bg-transparent border-0 shadow-sm gallery-item">
                    <img src="assets/images/covers/<?php echo htmlspecialchars($row['image_path']); ?>" 
                         class="card-img-top rounded gallery-img cursor-pointer shadow" 
                         alt="<?php echo htmlspecialchars($row['title']); ?>"
                         data-bs-toggle="modal" 
                         data-bs-target="#imageModal"
                         data-title="<?php echo htmlspecialchars($row['title']); ?>"
                         data-author="<?php echo htmlspecialchars($row['author'] ?? 'Unknown'); ?>"
                         data-year="<?php echo htmlspecialchars($row['publish_year'] ?? 'N/A'); ?>"
                         style="object-fit: cover; height: 250px; cursor: pointer;">
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Bootstrap Modal with Author and Year layout -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-light border-info">
            <div class="modal-header border-bottom-0 bg-teal" style="background-color: #0d9488;">
                <h5 class="modal-title font-elms fw-bold" id="imageModalLabel">Book Cover</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImage" class="img-fluid w-100" alt="Full size cover">
                <div class="p-3" style="background-color: #1e293b;">
                    <h5 id="modalAuthor" class="mb-1 text-info font-righteous"></h5>
                    <p id="modalYear" class="mb-0 text-muted"></p>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-end" style="background-color: #0f172a;">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($conn);
include 'includes/footer.inc'; 
?>