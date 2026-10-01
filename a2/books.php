<?php
$page_title = "Browse Books";
include 'includes/db_connect.inc';
include 'includes/header.inc';

// Fetch all books
$query = "SELECT * FROM books ORDER BY title ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container my-5">
    <h2 class="mb-4 d-flex align-items-center"><span class="material-icons me-2 text-info">library_books</span> All Books</h2>
    
    <!-- Filter -->
    <div class="bg-white text-dark p-3 rounded mb-4 d-flex align-items-center w-100">
        <label for="statusFilter" class="fw-bold me-3">Filter by Status:</label>
        <select id="statusFilter" class="form-select w-auto">
            <option value="all">Show All</option>
            <option value="available">Available</option>
            <option value="reserved">Reserved</option>
            <option value="sold">Sold</option>
        </select>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded p-3">
        <table class="table table-borderless align-middle text-dark w-100">
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    // Determine badge color
                    $status = strtolower($row['status']);
                    $badgeClass = $status === 'available' ? 'bg-success' : ($status === 'reserved' ? 'bg-warning text-dark' : 'bg-secondary');
                ?>
                <tr data-status="<?php echo htmlspecialchars($status); ?>" class="border-bottom">
                    <td class="py-3"><a href="details.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark fw-medium"><?php echo htmlspecialchars($row['title']); ?></a></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td><?php echo htmlspecialchars($row['publish_year']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><span class="badge rounded-pill <?php echo $badgeClass; ?> px-3 py-2"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></span></td>
                </tr>
                <?php endline; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($conn);
include 'includes/footer.inc'; 
?>