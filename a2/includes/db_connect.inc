<?php

// Detect whether the website is running locally or on RMIT Coreteaching
$host_name = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

if (
    $host_name === 'localhost' ||
    $host_name === '127.0.0.1' ||
    strpos($host_name, 'localhost') !== false
) {
    // Localhost database settings
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'bookverse';

} else {
    // RMIT server database settings
    // The script runs ON the RMIT server, so connect via localhost
    $host = 'jacob5';
    $user = 's4007558';
    $pass = 'Maher2003468';
    $db   = 's4007558';
}

// Connect to MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// If localhost is refused on the RMIT server, retry with 127.0.0.1 (TCP)
if (!$conn && $host === 'localhost') {
    $conn = mysqli_connect('127.0.0.1', $user, $pass, $db);
}

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set character encoding
mysqli_set_charset($conn, "utf8mb4");

?>