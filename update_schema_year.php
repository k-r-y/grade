<?php
require 'db_connect.php';

// Add year_level column
$sql = "ALTER TABLE users ADD COLUMN year_level INT DEFAULT 1";
if ($conn->query($sql) === TRUE) {
    echo "Column year_level added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}
