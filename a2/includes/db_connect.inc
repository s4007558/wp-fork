<?php

// Detect whether the website is running locally or on RMIT Coreteaching
$host_name = $_SERVER['HTTP_HOST'] ?? '';

if (
    $host_name === 'localhost' ||
    $host_name === '127.0.0.1' ||
    str_contains($host_name, 'localhost')
) {
    // Localhost database settings
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'bookverse';
} else {
    // RMIT Coreteaching / Jacob 5 database settings
    $host = 'jacob5';
    $user = 's4007558';
    $pass = '';
    $db   = 's4007558';
}

// Connect to MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set character encoding
mysqli_set_charset($conn, "utf8mb4");

?>
