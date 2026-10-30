<?php
/**
 * search.php - keyword search in title, author and description (prepared statement).
 * The keyword is read from the query string: search.php?keyword=...
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// Read the keyword (q= and search= are accepted too), trim it and limit its length
$keyword = '';
foreach (array('keyword', 'q', 'search') as $name) {
    if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $keyword = trim($_GET[$name]);
        break;
    }
}
$keyword = mb_substr($keyword, 0, 100);
$results = array();

if ($keyword !== '') {
    // Escape the LIKE wildcards (% and _) the user typed, then wrap in % so it matches anywhere
    $like = '%' . str_replace(array('!', '%', '_'), array('!!', '!%', '!_'), $keyword) . '%';
    $sql = "SELECT book_id, title, author, image_path
            FROM books
            WHERE title LIKE ? ESCAPE '!' OR author LIKE ? ESCAPE '!' OR description LIKE ? ESCAPE '!'
            ORDER BY title ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $results = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

$page_title = 'Search';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <h1 class="page-title"><span class="material-icons" aria-hidden="true">search</span> Search Books</h1>

        <div class="panel-dark search-panel">
            <form method="get" action="search.php" class="search-form" role="search">
                <label for="keyword" class="visually-hidden">Search by title, author or description</label>
                <input type="search" class="form-control" id="keyword" name="keyword" value="<?= e($keyword) ?>"
                       placeholder="Search by title, author or description" maxlength="100">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <?php if ($keyword !== ''): ?>
            <p class="search-summary" role="status">
                <?= count($results) ?> result<?= count($results) === 1 ? '' : 's' ?> for &ldquo;<?= e($keyword) ?>&rdquo;
            </p>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php foreach ($results as $book): ?>
                <div class="col">
                    <div class="card book-card h-100">
                        <img src="<?= e(cover_url($book['image_path'])) ?>" class="card-img-top" alt="Cover of <?= e($book['title']) ?>">
                        <div class="card-body">
                            <h2 class="card-title"><?= e($book['title']) ?></h2>
                            <p class="book-meta"><?= e($book['author']) ?></p>
                            <a href="details.php?id=<?= (int) $book['book_id'] ?>" class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
