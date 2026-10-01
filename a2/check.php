<?php
include 'includes/db_connect.inc';
$res = mysqli_query($conn, "DESCRIBE books");
echo "<h2>Columns in 'books' table:</h2><ul>";
while($row = mysqli_fetch_assoc($res)) {
    echo "<li><strong>" . htmlspecialchars($row['Field']) . "</strong></li>";
}
echo "</ul>";
?>