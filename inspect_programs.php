<?php
require 'db_connect.php';

$result = $conn->query("SELECT * FROM programs ORDER BY name");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Code: " . $row['code'] . "\n";
}
?>
