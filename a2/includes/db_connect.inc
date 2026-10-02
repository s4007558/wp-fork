<?php
// Determine environment by checking the host header
$host_name = $_SERVER['HTTP_HOST'] ?? '';

if ($host_name === 'localhost' || $host_name === '127.0.0.1') {
    // Localhost XAMPP Settings
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'bookverse';
} else {
    // Coreteaching Jacob 5 Settings
    $host = 'jupiter.csit.rmit.edu.au';
    $user = 's4007558';
    $pass = 'Maher2003468'; //
    $db   = 's4007558';
}

// Establish procedural MySQLi connection
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Ensure UTF-8 character encoding
mysqli_set_charset($conn, "utf8mb4");
?>