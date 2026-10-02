<?php

// Determine which environment the website is running on
$host_name = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

if (
    $host_name === 'localhost' ||
    $host_name === '127.0.0.1' ||
    strpos($host_name, 'localhost') !== false
) {
    // Localhost settings
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'bookverse';

} else {
    // RMIT Coreteaching / Jacob 5 settings
    $host = 'jacob5';
    $user = 's4007558';
    $pass = 'Maher2003468';
    $db   = 's4007558';
}

// Connect to database
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set character encoding
mysqli_set_charset($conn, "utf8mb4");

?>