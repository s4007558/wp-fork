<?php
/**
 * db_connect.inc - the ONE database connection used by every page.
 *
 * The file detects where the site is running and picks the right settings:
 *   - localhost / 127.0.0.1 (XAMPP, MAMP, Laragon, php -S ...)  -> local "bookverse" database
 *   - anything else (jupiter / saturn / titan Coreteaching)      -> Jacob 5, database named after your student ID
 *
 * MySQLi PROCEDURAL style is used everywhere in this site.
 * $conn is the connection that every page uses with mysqli_prepare(...).
 */

// While developing, MySQLi errors are thrown as exceptions (mysqli_sql_exception)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ===================== EDIT THESE VALUES (3 lines) =====================
$student_id      = 's4007558';             // RMIT student ID
$jacob5_password = 'Maher2003468';            // Jacob 5 (MySQL) password
$jacob5_host     = 'talsprddb02.int.its.rmit.edu.au';   // Jacob 5 host - check it against "Developing and deploying with Git"
// =======================================================================

// Local development settings (XAMPP default = user root, empty password; MAMP uses root/root)
$local_host     = 'localhost';
$local_user     = 'root';
$local_password = '';
$local_database = 'bookverse';

// ---- Where are we running? ------------------------------------------------
$server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
$is_local = in_array($server_name, array('localhost', '127.0.0.1', '::1'), true)
    || substr($server_name, -6) === '.local'
    || PHP_SAPI === 'cli';

if ($is_local) {
    $db_host = $local_host;
    $db_user = $local_user;
    $db_pass = $local_password;
    $db_name = $local_database;
} else {
    $db_host = $jacob5_host;
    $db_user = $student_id;                 // Jacob 5 user name = student ID
    $db_pass = $jacob5_password;
    $db_name = $student_id;                 // Jacob 5 database name = student ID
}

// ---- Error reporting: detailed on localhost, quiet on the live server ----------
error_reporting(E_ALL);
ini_set('display_errors', $is_local ? '1' : '0');
ini_set('log_errors', '1');

// ---- Connect ------------------------------------------------------------------
try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $ex) {
    error_log('BookVerse DB connection failed: ' . $ex->getMessage());
    http_response_code(500);
    if ($is_local) {
        exit('Database connection failed: ' . htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
    exit('Sorry, the site is temporarily unavailable.');
}
