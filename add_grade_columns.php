<?php
require 'db_connect.php';

$sql = "ALTER TABLE grades 
        ADD COLUMN IF NOT EXISTS midterm DECIMAL(5,2) NULL AFTER subject_code,
        ADD COLUMN IF NOT EXISTS final DECIMAL(5,2) NULL AFTER midterm";

if ($conn->query($sql) === TRUE) {
    echo "Table 'grades' altered successfully. Added 'midterm' and 'final' columns.";
} else {
    echo "Error altering table: " . $conn->error;
}
?>
