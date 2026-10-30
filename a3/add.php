<?php
/**
 * add.php - add a new book (logged-in users only). The form posts to this same page.
 * Steps: login check -> CSRF check -> validate -> optional image upload (unique file name)
 *        -> INSERT with a prepared statement -> flash message -> details page.
 * The form field names are the same as the database column names.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// Protected page: anonymous visitors are sent to the login page
if (!isset($_SESSION['user_id'])) {
    set_flash('warning', 'Please log in to add a book.');
    redirect('login.php');
}
$user_id = (int) $_SESSION['user_id'];

// Values shown in the form (kept after an error so the user does not have to retype)
$old = array(
    'title' => '', 'author' => '', 'genre' => '', 'publication_year' => '', 'price' => '',
    'isbn' => '', 'book_condition' => '', 'description' => '', 'status' => 'Available',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $field => $default) {
        $old[$field] = trim((string) (isset($_POST[$field]) ? $_POST[$field] : $default));
    }

    // ---- Validation (server side) ----
    $errors = array();
    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please submit the form again.';
    }
    if ($old['title'] === '' || mb_strlen($old['title']) > 255) {
        $errors[] = 'Title is required (maximum 255 characters).';
    }
    if ($old['author'] === '' || mb_strlen($old['author']) > 255) {
        $errors[] = 'Author is required (maximum 255 characters).';
    }
    if (!in_array($old['genre'], BOOK_GENRES, true)) {
        $errors[] = 'Please choose a genre.';
    }
    if ($old['description'] === '') {
        $errors[] = 'Description is required.';
    }
    if (!in_array($old['book_condition'], BOOK_CONDITIONS, true)) {
        $errors[] = 'Please choose the book condition.';
    }
    if (!in_array($old['status'], BOOK_STATUSES, true)) {
        $errors[] = 'Please choose a valid status.';
    }
    if ($old['price'] !== '' && (!is_numeric($old['price']) || $old['price'] < 0 || $old['price'] > 999999.99)) {
        $errors[] = 'Price must be a number between 0 and 999999.99.';
    }
    $year = null;
    if ($old['publication_year'] !== '') {
        $year = filter_var($old['publication_year'], FILTER_VALIDATE_INT,
            array('options' => array('min_range' => 1000, 'max_range' => (int) date('Y') + 1)));
        if ($year === false) {
            $errors[] = 'Publication year must be a valid year.';
            $year = null;
        }
    }
    if (strlen($old['isbn']) > 20) {
        $errors[] = 'ISBN is too long (maximum 20 characters).';
    }

    // ---- Optional cover image: check it, then save it under a new unique name ----
    $has_image = image_was_uploaded('image_path');
    if ($has_image && !$errors) {
        $image_error = check_image_upload('image_path');
        if ($image_error !== null) {
            $errors[] = $image_error;
        }
    }
    $image_name = null;
    if ($has_image && !$errors) {
        $image_name = save_cover_image('image_path');
        if ($image_name === null) {
            $errors[] = 'The image could not be saved on the server (check the covers folder permissions).';
        }
    }

    // ---- Save the record ----
    if (!$errors) {
        $price = $old['price'] === '' ? 0.0 : (float) $old['price'];
        $isbn = $old['isbn'] !== '' ? $old['isbn'] : null;

        try {
            $sql = "INSERT INTO books
                        (user_id, title, author, genre, publication_year, isbn, description,
                         book_condition, price, image_path, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isssisssdss',
                $user_id, $old['title'], $old['author'], $old['genre'], $year, $isbn,
                $old['description'], $old['book_condition'], $price, $image_name, $old['status']);
            mysqli_stmt_execute($stmt);
            $new_id = mysqli_insert_id($conn);                 // book_id is AUTO_INCREMENT
            mysqli_stmt_close($stmt);

            set_flash('success', 'Your book was added successfully.');
            redirect('details.php?id=' . $new_id);
        } catch (mysqli_sql_exception $ex) {
            error_log('add.php insert failed: ' . $ex->getMessage());
            delete_cover_image($image_name);                   // do not leave an orphan file behind
            $errors[] = 'The book could not be saved. Please try again.';
        }
    }

    set_flash('danger', implode("\n", $errors));
}

$page_title = 'Add Book';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <div class="form-narrow">
            <h1 class="page-title"><span class="material-icons" aria-hidden="true">add_box</span> Add New Book</h1>

            <form method="post" action="add.php" enctype="multipart/form-data" class="panel-dark form-panel needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="title" class="form-label"><span class="material-icons" aria-hidden="true">title</span> Book Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter book title"
                           value="<?= e($old['title']) ?>" required maxlength="255">
                    <div class="invalid-feedback">Please enter the book title.</div>
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label"><span class="material-icons" aria-hidden="true">person</span> Author Name</label>
                    <input type="text" class="form-control" id="author" name="author" placeholder="Enter author name"
                           value="<?= e($old['author']) ?>" required maxlength="255">
                    <div class="invalid-feedback">Please enter the author.</div>
                </div>

                <div class="mb-3">
                    <label for="genre" class="form-label"><span class="material-icons" aria-hidden="true">category</span> Genre</label>
                    <select class="form-select" id="genre" name="genre" required>
                        <option value="">Select a genre</option>
                        <?php foreach (BOOK_GENRES as $genre): ?>
                            <option value="<?= e($genre) ?>"<?= $old['genre'] === $genre ? ' selected' : '' ?>><?= e($genre) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please choose a genre.</div>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="publication_year" class="form-label"><span class="material-icons" aria-hidden="true">calendar_today</span> Publication Year</label>
                        <input type="number" class="form-control" id="publication_year" name="publication_year"
                               placeholder="2024" value="<?= e($old['publication_year']) ?>" min="1000" max="<?= (int) date('Y') + 1 ?>">
                        <div class="invalid-feedback">Enter a valid year.</div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="price" class="form-label"><span class="material-icons" aria-hidden="true">attach_money</span> Price ($)</label>
                        <input type="number" class="form-control" id="price" name="price" placeholder="19.99"
                               value="<?= e($old['price']) ?>" step="0.01" min="0">
                        <div class="invalid-feedback">The price cannot be negative.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" placeholder="978-1-234567-89-0"
                               value="<?= e($old['isbn']) ?>" maxlength="20">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="book_condition" class="form-label"><span class="material-icons" aria-hidden="true">assignment</span> Book Condition</label>
                        <select class="form-select" id="book_condition" name="book_condition" required>
                            <option value="">Select condition</option>
                            <?php foreach (BOOK_CONDITIONS as $condition): ?>
                                <option value="<?= e($condition) ?>"<?= $old['book_condition'] === $condition ? ' selected' : '' ?>><?= e($condition) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please choose the condition.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label"><span class="material-icons" aria-hidden="true">description</span> Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                              placeholder="Describe the book..." required><?= e($old['description']) ?></textarea>
                    <div class="invalid-feedback">Please describe the book.</div>
                </div>

                <div class="mb-3">
                    <label for="image_path" class="form-label"><span class="material-icons" aria-hidden="true">image</span> Upload Cover Image</label>
                    <input type="file" class="form-control" id="image_path" name="image_path"
                           accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                           aria-describedby="imageHelp imageError">
                    <div class="form-text" id="imageHelp">Optional. jpg, jpeg, png, gif or webp, up to 5 MB.</div>
                    <div class="invalid-feedback" id="imageError"></div>
                    <img id="imagePreview" class="image-preview" src="assets/images/placeholder.svg" alt="Preview of the selected cover" hidden>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label"><span class="material-icons" aria-hidden="true">check_circle</span> Availability Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach (BOOK_STATUSES as $status): ?>
                            <option value="<?= e($status) ?>"<?= $old['status'] === $status ? ' selected' : '' ?>><?= e($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agree" name="agree" value="1">
                    <label class="form-check-label" for="agree">I agree that this book information is accurate and complete</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <span class="material-icons" aria-hidden="true">save</span> Add Book to Collection
                </button>
            </form>
        </div>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
