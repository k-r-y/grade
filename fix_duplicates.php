<?php
require 'db_connect.php';

// 1. Migrate users from BSIS ID 1 to BSIS ID 9
echo "Migrating users from Program ID 1 to 9...\n";
$stmt = $conn->prepare("UPDATE users SET program_id = 9 WHERE program_id = 1");
if ($stmt->execute()) {
    echo " - Migrated " . $stmt->affected_rows . " users.\n";
} else {
    echo " - Error migrating users: " . $conn->error . "\n";
}

// 2. Delete Program ID 1 (BSIS Duplicate)
echo "Deleting Program ID 1...\n";
$stmt = $conn->prepare("DELETE FROM programs WHERE id = 1");
if ($stmt->execute()) {
    echo " - Deleted Program ID 1.\n";
} else {
    echo " - Error deleting Program ID 1: " . $conn->error . "\n";
}

// 3. Delete Program ID 11 (BSCS Duplicate)
echo "Deleting Program ID 11...\n";
$stmt = $conn->prepare("DELETE FROM programs WHERE id = 11");
if ($stmt->execute()) {
    echo " - Deleted Program ID 11.\n";
} else {
    echo " - Error deleting Program ID 11: " . $conn->error . "\n";
}

echo "Done.\n";
?>
