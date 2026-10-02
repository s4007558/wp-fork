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
    // 2. RMIT Coreteaching Production Settings
    // -------------------------------------------------------------
    $host = '131.170.5.135';
    $user = 'jupiter.csit.rmit.edu.au';
    $pass = 'Maher2003468';
    $db   = 's4007558';
}


// -------------------------------------------------------------
// Connect to the database
// -------------------------------------------------------------
$conn = mysqli_connect($host, $user, $pass, $db);


// -------------------------------------------------------------
// Check connection
// -------------------------------------------------------------
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


// -------------------------------------------------------------
// Set character encoding
// -------------------------------------------------------------
mysqli_set_charset($conn, "utf8mb4");

?>
```

**Important:** If `Maher2003468` is your real database password, change it after sharing it publicly. Also, if the IP connection produces a different error, send me that exact error — it will tell us whether the issue is the database host, credentials, or RMIT server access.
