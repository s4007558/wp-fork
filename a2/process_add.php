<?php
session_start();
include 'includes/db_connect.inc';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $publication_year = intval($_POST['publication_year'] ?? date('Y'));
    $price = floatval($_POST['price'] ?? 0.0);
    $isbn = trim($_POST['isbn'] ?? '');
    $book_condition = trim($_POST['book_condition'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_path']['tmp_name'];
        $fileName = $_FILES['image_path']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExts)) {
            $newFileName = uniqid('cover_', true) . '.' . $fileExtension;
            $destPath = 'assets/images/covers/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $sql = "INSERT INTO books (title, author, genre, publication_year, isbn, description, book_condition, price, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssisssdss", $title, $author, $genre, $publication_year, $isbn, $description, $book_condition, $price, $newFileName, $status);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['message'] = "<div class='alert alert-success'>Book added successfully!</div>";
                } else {
                    $_SESSION['message'] = "<div class='alert alert-danger'>Database Error: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['message'] = "<div class='alert alert-danger'>Failed to move uploaded file. Check folder permissions.</div>";
            }
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>Invalid file extension. Only images allowed.</div>";
        }
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>File upload failed.</div>";
    }

    mysqli_close($conn);
    header("Location: add.php");
    exit();
}
?>