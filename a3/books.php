<?php
/**
 * books.php - table of every book. Each row carries a data-status attribute and the
 * status filter is done in the browser with JavaScript (assets/js/scripts.js) - no reload.
 * The filter options are built from the status values that really exist in the database.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// All books with the seller's user name
$sql = "SELECT b.book_id, b.title, b.author, b.genre, b.publication_year, b.price, b.status, b.user_id, u.username
        FROM books b
        LEFT JOIN users u ON u.user_id = b.user_id
        ORDER BY b.book_id ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$books = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// The distinct status values used to build the filter drop-down
$stmt = mysqli_prepare($conn, "SELECT DISTINCT status FROM books ORDER BY status ASC");
mysqli_stmt_execute($stmt);
$statuses = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$page_title = 'Browse Books';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <h1 class="page-title"><span class="material-icons" aria-hidden="true">newspaper</span> All Books</h1>

        <div class="panel-light filter-panel" data-bs-theme="light">
            <label for="statusFilter" class="fw-bold me-2">Filter by Status:</label>
            <select id="statusFilter" class="form-select form-select-sm filter-select">
                <option value="all">Show All</option>
                <?php foreach ($statuses as $row): ?>
                    <option value="<?= e($row['status']) ?>"><?= e($row['status']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="panel-light table-panel" data-bs-theme="light">
            <div class="table-responsive">
                <table class="table books-table" id="booksTable">
                    <thead>
                        <tr>
                            <th scope="col"><span class="visually-hidden">Title</span></th>
                            <th scope="col"><span class="visually-hidden">Author</span></th>
                            <th scope="col"><span class="visually-hidden">Genre</span></th>
                            <th scope="col"><span class="visually-hidden">Year</span></th>
                            <th scope="col"><span class="visually-hidden">Price</span></th>
                            <th scope="col"><span class="visually-hidden">Status</span></th>
                            <th scope="col"><span class="visually-hidden">Seller</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr data-status="<?= e($book['status']) ?>">
                                <td><a href="details.php?id=<?= (int) $book['book_id'] ?>"><?= e($book['title']) ?></a></td>
                                <td><?= e($book['author']) ?></td>
                                <td><?= e($book['genre']) ?></td>
                                <td><?= e($book['publication_year']) ?></td>
                                <td>$<?= number_format((float) $book['price'], 2) ?></td>
                                <td><span class="status-badge <?= e(status_class($book['status'])) ?>"><?= e($book['status']) ?></span></td>
                                <td>
                                    <?php if ($book['user_id']): ?>
                                        <a href="seller.php?id=<?= (int) $book['user_id'] ?>"><?= e($book['username']) ?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p id="noResults" class="no-results" hidden>No books match this filter.</p>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
