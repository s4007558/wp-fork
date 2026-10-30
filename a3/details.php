<?php
/**
 * details.php?id=<book_id> - one book. The record is read with a prepared statement.
 * Edit / Delete buttons are shown to the OWNER only (decided on the server from the session).
 * Delete asks for confirmation in a Bootstrap modal, then posts to process_delete.php.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

$book_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$book = null;
if ($book_id) {
    $sql = "SELECT b.*, u.username
            FROM books b
            LEFT JOIN users u ON u.user_id = b.user_id
            WHERE b.book_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $book_id);
    mysqli_stmt_execute($stmt);
    $book = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}
if (!$book) {
    set_flash('danger', 'That book could not be found.');
    redirect('books.php');
}

// Owner = logged in AND the book's user_id equals the user id stored in the session
$is_owner = isset($_SESSION['user_id']) && $book['user_id'] !== null
    && (int) $book['user_id'] === (int) $_SESSION['user_id'];

$page_title = $book['title'];
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <div class="row g-4">
            <div class="col-md-4">
                <img src="<?= e(cover_url($book['image_path'])) ?>" class="detail-cover" alt="Cover of <?= e($book['title']) ?>">
            </div>
            <div class="col-md-8">
                <h1 class="detail-title"><?= e($book['title']) ?></h1>
                <p class="detail-author">by <?= e($book['author']) ?></p>
                <p><span class="status-badge status-badge-lg <?= e(status_class($book['status'])) ?>"><?= e($book['status']) ?></span></p>

                <div class="panel-light detail-panel" data-bs-theme="light">
                    <dl class="row mb-0">
                        <dt class="col-6">Genre:</dt>
                        <dd class="col-6"><?= e($book['genre']) ?></dd>
                        <dt class="col-6">Publication Year:</dt>
                        <dd class="col-6"><?= e($book['publication_year']) ?></dd>
                        <dt class="col-6">ISBN:</dt>
                        <dd class="col-6"><?= e($book['isbn']) ?></dd>
                        <dt class="col-6">Condition:</dt>
                        <dd class="col-6"><?= e($book['book_condition']) ?></dd>
                        <dt class="col-6">Price:</dt>
                        <dd class="col-6 detail-price">$<?= number_format((float) $book['price'], 2) ?></dd>
                        <dt class="col-6">Seller:</dt>
                        <dd class="col-6">
                            <?php if ($book['user_id']): ?>
                                <a href="seller.php?id=<?= (int) $book['user_id'] ?>"><?= e($book['username']) ?></a>
                            <?php else: ?>
                                Unknown
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>

                <h2 class="detail-subheading">Description:</h2>
                <p><?= nl2br(e($book['description'])) ?></p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="books.php" class="btn btn-slate">Back</a>
                    <?php if ($is_owner): ?>
                        <a href="edit.php?id=<?= (int) $book['book_id'] ?>" class="btn btn-warning">Edit</a>
                        <button type="button" class="btn btn-danger" id="deleteBtn" aria-haspopup="dialog" aria-controls="deleteModal">Delete</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_owner): ?>
    <!-- Delete confirmation modal: opened by scripts.js when the owner clicks "Delete" -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="process_delete.php" id="deleteForm">
                    <div class="modal-header modal-header-primary">
                        <h2 class="modal-title h5" id="deleteModalLabel">Delete book?</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong><?= e($book['title']) ?></strong>? This action cannot be undone.
                        <input type="hidden" name="book_id" value="<?= (int) $book['book_id'] ?>">
                        <?= csrf_field() ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-slate" id="cancelDelete" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmDelete">Delete book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php include 'includes/footer.inc'; ?>
