<?php
$page_title = "Add Book";
include 'includes/db_connect.inc';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $year = intval($_POST['publish_year']);
    $price = floatval($_POST['price']);
    $isbn = trim($_POST['isbn']);
    $condition = trim($_POST['book_condition']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);
    
    // Image Handling
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_path']['tmp_name'];
        $fileName = $_FILES['image_path']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExts)) {
            // Generate unique filename to prevent overwriting
            $newFileName = uniqid('cover_', true) . '.' . $fileExtension;
            $destPath = 'assets/images/covers/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Prepared Statement Insertion
                $sql = "INSERT INTO books (title, author, genre, publish_year, price, isbn, book_condition, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssidissss", $title, $author, $genre, $year, $price, $isbn, $condition, $description, $newFileName, $status);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='alert alert-success'>Book added successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Database error: " . mysqli_error($conn) . "</div>";
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = "<div class='alert alert-danger'>Error moving uploaded file.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid file type.</div>";
        }
    }
}

include 'includes/header.inc';
?>

<div class="container my-5 max-w-75">
    <h2 class="mb-4 d-flex align-items-center"><span class="material-icons me-2 text-info">add_box</span> Add New Book</h2>
    <?php echo $message; ?>
    
    <div class="card p-4 shadow-sm" style="background-color: #1e293b;">
        <form action="add.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">title</span> Book Title</label>
                <input type="text" name="title" class="form-control" required placeholder="Enter book title">
            </div>
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">person</span> Author Name</label>
                <input type="text" name="author" class="form-control" required placeholder="Enter author name">
            </div>

            <!-- Additional standard fields (Genre, Year, Price, ISBN, Condition) go here following similar layout as screenshot -->
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">description</span> Description</label>
                <textarea name="description" class="form-control" rows="4" required placeholder="Describe the book..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label"><span class="material-icons fs-6 me-1">image</span> Upload Cover Image</label>
                <input type="file" name="image_path" id="imagePath" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required>
                <img id="imagePreview" src="#" alt="Preview" class="mt-3 rounded shadow" style="display:none; max-width: 200px;">
            </div>

            <div class="mb-4">
                <label class="form-label"><span class="material-icons fs-6 me-1">check_circle</span> Availability Status</label>
                <select name="status" class="form-select">
                    <option value="Available">Available</option>
                    <option value="Reserved">Reserved</option>
                    <option value="Sold">Sold</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary-custom w-100 py-2 d-flex justify-content-center align-items-center">
                <span class="material-icons me-2">save</span> Add Book to Collection
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.inc'; ?>