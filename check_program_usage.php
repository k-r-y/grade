<?php
require 'db_connect.php';

$ids = [1, 2, 9, 11];
$tables = ['users', 'classes', 'student_subjects']; // Add other tables if known

foreach ($ids as $id) {
    echo "Checking usage for Program ID: $id\n";
    
    // Check users table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE program_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "  - Users: " . $row['count'] . "\n";

    // Check classes table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM classes WHERE program_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "  - Classes: " . $row['count'] . "\n";

    // Check announcement_recipients table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM announcement_recipients WHERE recipient_group = 'program' AND target_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "  - Announcement Recipients: " . $row['count'] . "\n";
}
?>
