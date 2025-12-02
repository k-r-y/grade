<?php
require 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS announcement_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    student_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_recipient (announcement_id, student_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'announcement_recipients' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
