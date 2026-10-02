```php
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

} else {

    // -------------------------------------------------------------
    // 2. RMIT Coreteaching / Production Settings
    // -------------------------------------------------------------
    $host = 'jacob5';
    $user = 'jupiter.csit.rmit.edu.au';
    $pass = 'Maher2003468';
    $db   = 's4007558';
}


// -------------------------------------------------------------
// Connect to database
// -------------------------------------------------------------
$conn = mysqli_connect($host, $user, $pass, $db);


// -------------------------------------------------------------
// Check connection
// -------------------------------------------------------------
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


// -------------------------------------------------------------
// Set UTF-8 character encoding
// -------------------------------------------------------------
mysqli_set_charset($conn, "utf8mb4");

?>
```

