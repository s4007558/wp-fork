<?php
// Determine environment dynamically by checking the host header
$host_name = $_SERVER['HTTP_HOST'] ?? '';

if ($host_name === 'localhost' || $host_name === '127.0.0.1') {
    // -------------------------------------------------------------
    // 1. Localhost XAMPP Settings
    // -------------------------------------------------------------
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'bookverse';

    $conn = mysqli_connect($host, $user, $pass, $db);

} else {
    // -------------------------------------------------------------
    // 2. Coreteaching (Titan / Jupiter) Production Settings
    // -------------------------------------------------------------
    $user = 'jupiter.csit.rmit.edu.au';
    $pass = 'Maher2003468'; //
    $db   = 's4007558';

    // Primary attempt: Internal short hostname 'jacob5'
    $conn = @mysqli_connect('jacob5', $user, $pass, $db);

    // Fallback 1: Direct internal IP address (bypasses DNS getaddrinfo errors)
    if (!$conn) {
        $conn = @mysqli_connect('131.170.5.135', $user, $pass, $db);
    }

    // Fallback 2: Fully qualified domain name
    if (!$conn) {
        $conn = @mysqli_connect('jacob5.csit.rmit.edu.au', $user, $pass, $db);
    }
}

// Check final connection outcome
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Enforce standard utf8mb4 character encoding
mysqli_set_charset($conn, "utf8mb4");
?>