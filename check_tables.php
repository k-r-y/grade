<?php
require 'db_connect.php';

$tables = ['announcements', 'announcement_reads', 'announcement_recipients'];
foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows > 0) {
        echo "Table '$table' exists.\n";
    } else {
        echo "Table '$table' DOES NOT exist.\n";
    }
}
?>
