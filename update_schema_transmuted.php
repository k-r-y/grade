<?php
require 'db_connect.php';

// Helper: Transmute Grade (Copied from api.php to be self-contained)
function transmuteGrade($raw)
{
    if ($raw >= 97) return [1.00, 'Passed'];
    if ($raw >= 94) return [1.25, 'Passed'];
    if ($raw >= 91) return [1.50, 'Passed'];
    if ($raw >= 88) return [1.75, 'Passed'];
    if ($raw >= 85) return [2.00, 'Passed'];
    if ($raw >= 82) return [2.25, 'Passed'];
    if ($raw >= 79) return [2.50, 'Passed'];
    if ($raw >= 76) return [2.75, 'Passed'];
    if ($raw >= 70) return [3.00, 'Passed'];
    return [5.00, 'Failed'];
}

echo "Starting migration...\n";

// 1. Check if column exists
$result = $conn->query("SHOW COLUMNS FROM grades LIKE 'transmutated_grade'");
if ($result->num_rows == 0) {
    echo "Adding 'transmutated_grade' column...\n";
    if ($conn->query("ALTER TABLE grades ADD COLUMN transmutated_grade DECIMAL(5,2) AFTER raw_grade")) {
        echo "Column added successfully.\n";
    } else {
        die("Error adding column: " . $conn->error . "\n");
    }
} else {
    echo "Column 'transmutated_grade' already exists.\n";
}

// 2. Backfill Data
echo "Backfilling existing grades...\n";
$result = $conn->query("SELECT id, raw_grade FROM grades WHERE transmutated_grade IS NULL OR transmutated_grade = 0");
if ($result) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $raw = floatval($row['raw_grade']);
        list($transmuted, $remarks) = transmuteGrade($raw);

        $stmt = $conn->prepare("UPDATE grades SET transmutated_grade = ? WHERE id = ?");
        $stmt->bind_param("di", $transmuted, $row['id']);
        if ($stmt->execute()) {
            $count++;
        }
    }
    echo "Updated $count records.\n";
} else {
    echo "Error fetching records: " . $conn->error . "\n";
}

echo "Migration complete.\n";
