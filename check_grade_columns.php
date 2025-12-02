<?php
require 'db_connect.php';

$result = $conn->query("SHOW COLUMNS FROM grades");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

echo "Columns in 'grades' table:\n";
print_r($columns);

if (in_array('midterm', $columns) && in_array('final', $columns)) {
    echo "\nSUCCESS: 'midterm' and 'final' columns exist.\n";
} else {
    echo "\nFAILURE: Missing columns.\n";
}
?>
