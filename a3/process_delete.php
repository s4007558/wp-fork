<?php
/**
 * process_delete.php - deletes one book (database record AND its image file).
 * Rules: POST only, the visitor must be logged in, the CSRF token must be valid and the
 * logged-in user must OWN the book (checked in PHP and again inside the SQL).
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// 1. Must be logged in
if (!isset($_SESSION['user_id'])) {
    set_flash('warning', 'Please log in to delete a book.');
    redirect('login.php');
}

// 2. Only a form POST may delete (a link or the address bar must never delete anything)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Books can only be deleted from the book details page.');
    redirect('books.php');
}

// 3. CSRF token must match the one stored in the session
if (!csrf_is_valid()) {
    set_flash('danger', 'Your session expired. Please try again.');
    redirect('books.php');
}

$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
$user_id = (int) $_SESSION['user_id'];

// 4. Look the book up, but ONLY if it belongs to the logged-in user (ownership check)
$stmt = mysqli_prepare($conn, "SELECT image_path FROM books WHERE book_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $book_id, $user_id);
mysqli_stmt_execute($stmt);
$book = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$book) {
    set_flash('danger', 'You can only delete your own books.');
    redirect('books.php');
}

// 5. Delete the row - user_id is in the WHERE clause again, so only this one owned row can go
$stmt = mysqli_prepare($conn, "DELETE FROM books WHERE book_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $book_id, $user_id);
mysqli_stmt_execute($stmt);
$deleted = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

// 6. Remove the image file from assets/images/covers/ and tell the user what happened
if ($deleted > 0) {
    delete_cover_image($book['image_path']);
    set_flash('success', 'The book was deleted.');
} else {
    set_flash('danger', 'The book could not be deleted.');
}
redirect('books.php');
