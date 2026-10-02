<?php
// TEMPORARY diagnostic. Fill in the 3 values below, upload to a2/, open in browser,
// paste the output to Claude, then DELETE this file (it contains your password).
$user = 's4007558';
$pass = 'Maher2003468';
$db   = 's4007558';

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain');

echo "PHP defaults configured by the server:\n";
foreach (['mysqli.default_host', 'mysqli.default_port', 'mysqli.default_socket', 'mysqli.default_user'] as $k) {
    echo "  $k = " . var_export(ini_get($k), true) . "\n";
}
echo "  this server hostname: " . php_uname('n') . "\n\n";

$hosts = [
    'jupiter.csit.rmit.edu.au',
    'jacob5',
    'jacob5.csit.rmit.edu.au',
    'jacob.csit.rmit.edu.au',
    'jacob',
    'localhost',
    '127.0.0.1',
];
$ports = [3306, 3307, 3308];
$defPort = (int)ini_get('mysqli.default_port');
if ($defPort && !in_array($defPort, $ports)) { $ports[] = $defPort; }

echo "Trying each host/port (3 second timeout each):\n";
foreach ($hosts as $h) {
    $ip = gethostbyname($h);
    echo "\n[$h] resolves to: " . ($ip === $h && !filter_var($h, FILTER_VALIDATE_IP) ? 'NOT FOUND' : $ip) . "\n";
    foreach ($ports as $p) {
        $c = mysqli_init();
        mysqli_options($c, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $ok = @mysqli_real_connect($c, $h, $user, $pass, $db, $p);
        echo "   port $p: " . ($ok ? "*** CONNECTED ***" : "failed (" . mysqli_connect_errno() . ") " . mysqli_connect_error()) . "\n";
        if ($ok) { mysqli_close($c); }
    }
}

echo "\nTrying PHP's built-in defaults (no host given):\n";
$c = mysqli_init();
mysqli_options($c, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
$ok = @mysqli_real_connect($c, null, $user, $pass, $db);
echo "   " . ($ok ? "*** CONNECTED ***" : "failed (" . mysqli_connect_errno() . ") " . mysqli_connect_error()) . "\n";
