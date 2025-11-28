<?php
require 'db_connect.php';

// Add status column
try {
    $conn->query("ALTER TABLE users ADD COLUMN status ENUM('active', 'pending') DEFAULT 'active'");
    echo "Added status column.\n";
} catch (Exception $e) {
    echo "Status column might already exist or error: " . $e->getMessage() . "\n";
}

// Add institute_id column
try {
    $conn->query("ALTER TABLE users ADD COLUMN institute_id INT DEFAULT NULL");
    $conn->query("ALTER TABLE users ADD FOREIGN KEY (institute_id) REFERENCES institutes(id)");
    echo "Added institute_id column.\n";
} catch (Exception $e) {
    echo "institute_id column might already exist or error: " . $e->getMessage() . "\n";
}

// Seed Admins
$password = password_hash('admin123', PASSWORD_DEFAULT);
$admins = [
    ['ADMIN-ICDI', 'Head of ICDI', 'admin.icdi@kld.edu.ph', 1],
    ['ADMIN-IBM', 'Head of IBM', 'admin.ibm@kld.edu.ph', 2],
    ['ADMIN-IOE', 'Head of IOE', 'admin.ioe@kld.edu.ph', 3]
];

foreach ($admins as $admin) {
    $stmt = $conn->prepare("INSERT INTO users (school_id, full_name, email, password_hash, role, institute_id, is_verified, status) VALUES (?, ?, ?, ?, 'admin', ?, 1, 'active') ON DUPLICATE KEY UPDATE role='admin', status='active'");
    $stmt->bind_param("ssssi", $admin[0], $admin[1], $admin[2], $password, $admin[3]);
    if ($stmt->execute()) {
        echo "Seeded " . $admin[1] . "\n";
    } else {
        echo "Failed to seed " . $admin[1] . ": " . $stmt->error . "\n";
    }
}
echo "Migration complete.\n";
?>
