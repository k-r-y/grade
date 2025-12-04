<?php
require 'db_connect.php';

// Delete Institute ID 7 (ICDI Duplicate)
echo "Deleting Institute ID 7...\n";
$stmt = $conn->prepare("DELETE FROM institutes WHERE id = 7");
if ($stmt->execute()) {
    echo " - Deleted Institute ID 7.\n";
} else {
    echo " - Error deleting Institute ID 7: " . $conn->error . "\n";
}

// Delete Institute ID 9 (IOE Duplicate)
echo "Deleting Institute ID 9...\n";
$stmt = $conn->prepare("DELETE FROM institutes WHERE id = 9");
if ($stmt->execute()) {
    echo " - Deleted Institute ID 9.\n";
} else {
    echo " - Error deleting Institute ID 9: " . $conn->error . "\n";
}

echo "Done.\n";
?>
