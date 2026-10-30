<?php
/**
 * index.php - home page: carousel of the latest cover images + grid of the 4 latest books.
 * Both come from the database (prepared statement, newest first).
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// The 4 newest books that are still available, together with the seller's user name
$sql = "SELECT b.book_id, b.title, b.author, b.genre, b.price, b.image_path, b.user_id, u.username
        FROM books b
        LEFT JOIN users u ON u.user_id = b.user_id
        WHERE b.status = ?
        ORDER BY b.created_at DESC, b.book_id DESC
        LIMIT 4";
$stmt = mysqli_prepare($conn, $sql);
$available = 'Available';
mysqli_stmt_bind_param($stmt, 's', $available);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$latest = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$page_title = 'Home';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <h1 class="visually-hidden">BookVerse - online book marketplace</h1>

    <?php if ($latest): ?>
    <div id="latestCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" aria-label="Latest book covers">
        <div class="carousel-inner">
            <?php foreach ($latest as $i => $book): ?>
                <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                    <img src="<?= e(cover_url($book['image_path'])) ?>" class="d-block w-100" alt="Cover of <?= e($book['title']) ?>">
                    <div class="carousel-caption">
                        <h2><?= e($book['title']) ?></h2>
                        <a class="btn btn-light btn-sm" href="details.php?id=<?= (int) $book['book_id'] ?>">
                            <span class="material-icons" aria-hidden="true">visibility</span> View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#latestCarousel" data-bs-slide="prev">
            <span class="material-icons" aria-hidden="true">chevron_left</span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#latestCarousel" data-bs-slide="next">
            <span class="material-icons" aria-hidden="true">chevron_right</span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="container page-section">
        <h2 class="page-title"><span class="material-icons" aria-hidden="true">favorite</span> Recently Added Books</h2>

        <?php if (!$latest): ?>
            <p>No books are available yet. <a href="register.php">Register</a> and add the first one!</p>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php foreach ($latest as $book): ?>
                <div class="col">
                    <div class="card book-card h-100">
                        <img src="<?= e(cover_url($book['image_path'])) ?>" class="card-img-top" alt="Cover of <?= e($book['title']) ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?= e($book['title']) ?></h3>
                            <p class="book-meta"><?= e($book['genre']) ?> &bull; <?= e($book['author']) ?></p>
                            <p class="book-price">$<?= number_format((float) $book['price'], 2) ?></p>
                            <?php if ($book['user_id']): ?>
                                <p class="book-seller"><a href="seller.php?id=<?= (int) $book['user_id'] ?>"><?= e($book['username']) ?></a></p>
                            <?php endif; ?>
                            <a href="details.php?id=<?= (int) $book['book_id'] ?>" class="btn btn-primary btn-sm">
                                <span class="material-icons" aria-hidden="true">visibility</span> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
