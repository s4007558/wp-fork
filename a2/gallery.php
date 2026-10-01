<?php
$page_title = "Gallery";
include 'includes/db_connect.inc';
include 'includes/header.inc';

// GROUP BY image_path ensures every unique cover photo is only rendered once
$query = "SELECT MIN(book_id) AS book_id, title, author, publication_year, image_path 
          FROM books 
          WHERE image_path IS NOT NULL AND image_path != '' 
          GROUP BY image_path 
          ORDER BY book_id ASC";

$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database Error in gallery.php: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container my-5">
    <h2 class="mb-4 d-flex align-items-center font-righteous">
        <span class="material-icons me-2 text-info">collections</span> Book Cover Gallery
    </h2>
    
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col">
                <div class="card h-100 bg-transparent border-0 shadow-sm">
                    <img src="assets/images/covers/<?php echo htmlspecialchars($row['image_path']); ?>" 
                         class="card-img-top rounded gallery-img shadow cursor-pointer" 
                         alt="<?php echo htmlspecialchars($row['title']); ?>"
                         data-bs-toggle="modal" 
                         data-bs-target="#imageModal"
                         data-title="<?php echo htmlspecialchars($row['title']); ?>"
                         data-author="<?php echo htmlspecialchars($row['author'] ?? ''); ?>"
                         data-year="<?php echo htmlspecialchars($row['publication_year'] ?? ''); ?>"
                         data-image="assets/images/covers/<?php echo htmlspecialchars($row['image_path']); ?>"
                         style="object-fit: cover; height: 230px; cursor: pointer;">
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Bootstrap Modal Matching Specification -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-light border-0 shadow-lg">
            <div class="modal-header border-bottom-0 py-3" style="background-color: #0d9488;">
                <h5 class="modal-title font-elms text-white fw-bold" id="imageModalLabel">Book Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4" style="background-color: #0b1329;">
                <!-- Full Image Preview -->
                <div class="d-flex justify-content-center mb-3">
                    <img src="" id="modalImage" class="img-fluid rounded shadow" alt="Cover Preview" style="max-height: 420px; object-fit: contain;">
                </div>
                <!-- Book Metadata -->
                <div class="p-3 rounded text-start" style="background-color: #1e293b;">
                    <h4 id="modalTitle" class="font-righteous text-white mb-1"></h4>
                    <p id="modalAuthor" class="text-info mb-1"></p>
                    <p id="modalYear" class="text-secondary small mb-0"></p>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-between" style="background-color: #0f172a;">
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