<?php
session_start();
$page_title = "Add Book";
include 'includes/header.inc';
?>

<div class="container my-5 max-w-75">
    <h2 class="mb-4 d-flex align-items-center font-righteous"><span class="material-icons me-2 text-info">add_box</span> Add New Book</h2>
    
    <?php 
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
    
    <div class="card p-4 shadow-sm" style="background-color: #1e293b;">
        <form action="process_add.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">title</span> Book Title</label>
                <input type="text" name="title" class="form-control" required placeholder="Enter book title">
            </div>
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">person</span> Author Name</label>
                <input type="text" name="author" class="form-control" required placeholder="Enter author name">
            </div>

            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">category</span> Genre</label>
                <select name="genre" class="form-select" required>
                    <option value="">Select a genre</option>
                    <option value="Fiction">Fiction</option>
                    <option value="Science Fiction">Science Fiction</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Dystopian">Dystopian</option>
                    <option value="Romance">Romance</option>
                    <option value="Memoir">Memoir</option>
                    <option value="Self-Help">Self-Help</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><span class="material-icons fs-6 me-1">calendar_today</span> Publication Year</label>
                    <input type="number" name="year" class="form-control" required value="<?php echo date('Y'); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><span class="material-icons fs-6 me-1">attach_money</span> Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required placeholder="19.99">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><span class="material-icons fs-6 me-1">qr_code</span> ISBN</label>
                    <input type="text" name="isbn" class="form-control" required placeholder="978-1-234567-89-0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><span class="material-icons fs-6 me-1">bookmark</span> Book Condition</label>
                    <select name="book_condition" class="form-select" required>
                        <option value="">Select condition</option>
                        <option value="New">New</option>
                        <option value="Like New">Like New</option>
                        <option value="Used">Used</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><span class="material-icons fs-6 me-1">description</span> Description</label>
                <textarea name="description" class="form-control" rows="4" required placeholder="Describe the book..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label"><span class="material-icons fs-6 me-1">image</span> Upload Cover Image</label>
                <input type="file" name="image_path" id="imagePath" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required>
                <img id="imagePreview" src="#" alt="Preview" class="mt-3 rounded shadow" style="display:none; max-width: 180px;">
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