<?php
require 'db_connect.php';

$class_id = 10;
$stmt = $conn->prepare("SELECT * FROM grades WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

echo "Grades for Class $class_id:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
