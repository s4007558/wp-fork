<?php
$host = $_SERVER['HTTP_HOST'];
$is_localhost = in_array($host, ['localhost', '127.0.0.1', '::1']);

if ($is_localhost) {
    $db_host = "localhost";
    $db_user = "root"; // Default XAMPP/MAMP user
    $db_pass = "";     // Default XAMPP/MAMP password
    $db_name = "bookverse";
} else {
    // Coreteaching / Jacob 5 environment
    $db_host = "jupiter.csit.rmit.edu.au";
    $db_user = "s4007558"; // Update with your Student ID
    $db_pass = "Maher2003468"; // Update with your DB Password
    $db_name = "s4007558"; // Update with your Student ID
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>