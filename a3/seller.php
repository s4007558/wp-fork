<?php
/**
 * seller.php?id=<user_id> - profile card and every book listed by that seller.
 * No login needed. The id comes from the query string and is used in prepared statements only.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

$seller_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$seller = null;
if ($seller_id) {
    $stmt = mysqli_prepare($conn, "SELECT user_id, username, location, bio FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $seller_id);
    mysqli_stmt_execute($stmt);
    $seller = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}
if (!$seller) {
    set_flash('danger', 'That seller could not be found.');
    redirect('books.php');
}

// This seller's books (filtered by the owner's user_id)
$stmt = mysqli_prepare($conn, "SELECT book_id, title, price, image_path FROM books WHERE user_id = ? ORDER BY book_id ASC");
mysqli_stmt_bind_param($stmt, 'i', $seller['user_id']);
mysqli_stmt_execute($stmt);
$books = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$page_title = 'Books by ' . $seller['username'];
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="card seller-card">
                    <h1><?= e($seller['username']) ?></h1>
                    <?php if ($seller['location']): ?>
                        <p class="seller-location"><?= e($seller['location']) ?></p>
                    <?php endif; ?>
                    <?php if ($seller['bio']): ?>
                        <p class="seller-bio"><?= e($seller['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-9">
                <h2 class="seller-heading">Books by <?= e($seller['username']) ?></h2>
                <?php if (!$books): ?>
                    <p>This seller has no books listed yet.</p>
                <?php endif; ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
                    <?php foreach ($books as $book): ?>
                        <div class="col">
                            <div class="card book-card h-100">
                                <img src="<?= e(cover_url($book['image_path'])) ?>" class="card-img-top" alt="Cover of <?= e($book['title']) ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><?= e($book['title']) ?></h3>
                                    <p class="book-price">$<?= number_format((float) $book['price'], 2) ?></p>
                                    <a href="details.php?id=<?= (int) $book['book_id'] ?>" class="btn btn-primary btn-sm">View</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
