<?php
require 'db_connect.php';

$school_id = '2024-2-000574';
$stmt = $conn->prepare("SELECT * FROM users WHERE school_id = ?");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "User Found:\n";
    print_r($row);
} else {
    echo "User NOT Found with school_id: $school_id\n";
}
?>
