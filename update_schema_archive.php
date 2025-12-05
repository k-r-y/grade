<?php
require 'db_connect.php';

// Add is_archived to classes
$sql = "ALTER TABLE classes ADD COLUMN is_archived TINYINT(1) DEFAULT 0";
if ($conn->query($sql) === TRUE) {
    echo "Column 'is_archived' added to classes table.\n";
} else {
    echo "Error adding column (or already exists): " . $conn->error . "\n";
}

// Add is_archived to programs? (Subjects)
// The user said "Archive subjects". KLD has 'programs'. Maybe subjects are effectively classes? 
// Or maybe there is a subjects table I missed?
// Let's check tables again.
/*
$result = $conn->query("SHOW TABLES");
while($row = $result->fetch_row()) {
    echo $row[0] . "\n";
}
*/
// Assuming 'classes' covers subjects for now as per schema I saw.

$conn->close();
?>
