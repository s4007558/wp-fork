<?php
$page_title = "Browse Books";
include 'includes/db_connect.inc';
include 'includes/header.inc';

// 1. Fetch dynamic status values from the database for the filter dropdown
$statusQuery = "SELECT DISTINCT status FROM books WHERE status IS NOT NULL AND status != '' ORDER BY status ASC";
$statusStmt = mysqli_prepare($conn, $statusQuery);
mysqli_stmt_execute($statusStmt);
$statusResult = mysqli_stmt_get_result($statusStmt);

// 2. Fetch books grouped by title to prevent duplicate rows from displaying
$query = "SELECT MIN(book_id) AS book_id, title, author, genre, publication_year, price, status 
          FROM books 
          GROUP BY title, author 
          ORDER BY book_id ASC";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database Error in books.php: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container my-5">
    <h2 class="mb-4 d-flex align-items-center font-righteous text-white">
        <span class="material-icons me-2 text-info">library_books</span> All Books
    </h2>
    
    <!-- Filter Section -->
    <div class="bg-white text-dark p-3 rounded mb-4 d-flex align-items-center w-100 shadow-sm">
        <label for="statusFilter" class="fw-bold me-3 font-elms">Filter by Status:</label>
        <select id="statusFilter" class="form-select w-auto">
            <option value="all">Show All</option>
            <?php while ($statusRow = mysqli_fetch_assoc($statusResult)): ?>
                <option value="<?php echo htmlspecialchars(strtolower($statusRow['status'])); ?>">
                    <?php echo htmlspecialchars(ucfirst($statusRow['status'])); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded p-3 shadow-sm overflow-auto">
        <table class="table table-borderless align-middle text-dark w-100 font-elms" style="min-width: 800px;">
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $status = strtolower($row['status']);
                    $badgeClass = $status === 'available' ? 'bg-success' : ($status === 'reserved' ? 'bg-warning text-dark' : 'bg-secondary');
                ?>
                <tr data-status="<?php echo htmlspecialchars($status); ?>" class="border-bottom">
                    <td class="py-3">
                        <a href="details.php?id=<?php echo $row['book_id']; ?>" class="text-decoration-none text-dark fw-bold">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td><?php echo htmlspecialchars($row['publication_year']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <span class="badge rounded-pill <?php echo $badgeClass; ?> px-3 py-2">
                            <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_stmt_close($statusStmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
include 'includes/footer.inc'; 
?>