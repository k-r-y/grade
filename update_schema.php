<?php
require 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'settings' created successfully.\n";
    
    // Seed default values
    $defaults = [
        'current_academic_year' => '2024-2025',
        'current_semester' => '1st Sem'
    ];
    
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = setting_value");
    
    foreach ($defaults as $key => $val) {
        $stmt->bind_param("ss", $key, $val);
        $stmt->execute();
    }
    
    echo "Default settings seeded.\n";
    
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>
