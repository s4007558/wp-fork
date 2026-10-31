<?php
/**
 * gallery.php - grid of every cover image. Clicking an image opens a Bootstrap modal
 * (the modal behaviour lives in assets/js/scripts.js). Image names come from the database;
 * the files themselves are in assets/images/covers/.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

$sql = "SELECT book_id, title, author, image_path
        FROM books
        WHERE image_path IS NOT NULL AND image_path <> ''
        ORDER BY book_id ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$books = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$page_title = 'Gallery';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <h1 class="page-title"><span class="material-icons" aria-hidden="true">image_search</span> Book Cover Gallery</h1>

        <?php if (!$books): ?>
            <p>There are no cover images yet.</p>
        <?php endif; ?>

        <div class="row g-3 gallery-grid">
            <?php foreach ($books as $book): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-auto">
                    <button type="button" class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal"
                            data-full="<?= e(cover_url($book['image_path'])) ?>"
                            data-title="<?= e($book['title']) ?>" data-author="<?= e($book['author']) ?>">
                        <img src="<?= e(cover_url($book['image_path'])) ?>" class="gallery-img" alt="Cover of <?= e($book['title']) ?>">
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- One modal shared by all images; scripts.js fills in the picture and the title -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="imageModalLabel">Book cover</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="assets/images/placeholder.svg" id="modalImage" class="modal-cover" alt="Selected book cover">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-slate" id="prevImage">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextImage">Next</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
