<?php
require 'db_connect.php';

$ids = [1, 7, 2, 9];
$tables = ['users', 'programs', 'announcement_recipients'];

foreach ($ids as $id) {
    echo "Checking usage for Institute ID: $id\n";
    
    // Check users table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE institute_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    echo "  - Users: " . $row['count'] . "\n";

    // Check programs table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM programs WHERE institute_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    echo "  - Programs: " . $row['count'] . "\n";

}
?>
