<?php
require 'db_connect.php';

$student_ids = ['2024-2-000579', '2024-2-000574'];

echo "Checking grades for students: " . implode(', ', $student_ids) . "\n\n";

foreach ($student_ids as $sid) {
    // Get User ID first
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE school_id = ?");
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        echo "Student: {$user['full_name']} (ID: {$user['id']})\n";

        $stmtGrade = $conn->prepare("SELECT * FROM grades WHERE student_id = ?");
        $stmtGrade->bind_param("i", $user['id']);
        $stmtGrade->execute();
        $res = $stmtGrade->get_result();

        while ($row = $res->fetch_assoc()) {
            echo "  Class ID: {$row['class_id']}\n";
            echo "  Raw Grade: {$row['raw_grade']}\n";
            echo "  Transmutated Grade (New Col): {$row['transmutated_grade']}\n";
            echo "  Semestral Grade (Old Col): {$row['grade']}\n";
            echo "  Midterm: {$row['midterm']}\n";
            echo "  Final: {$row['final']}\n";
            echo "  Remarks: {$row['remarks']}\n";
            echo "  Updated At: {$row['updated_at']}\n";
            echo "  -------------------\n";
        }
    } else {
        echo "Student $sid not found.\n";
    }
}
