<?php
require 'db_connect.php';

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Truncate tables
$tables = [
    'users',
    'programs',
    'institutes',
    'classes',
    'enrollments',
    'grades',
    'announcements',
    'announcement_reads',
    'announcement_recipients'
];

foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
    echo "Truncated $table<br>";
}

// 1. Insert Institutes
$institutes = [
    ['name' => 'Institute of Computing and Digital Innovation', 'code' => 'ICDI'],
    ['name' => 'Institute of Engineering', 'code' => 'IOE'],
    ['name' => 'Institute of Nursing', 'code' => 'ION'],
    ['name' => 'Institute of Midwifery', 'code' => 'IOM'],
    ['name' => 'Institute of Liberal Arts', 'code' => 'IBS'], // Assuming IBS based on admin email
    ['name' => 'Institute of Science and Mathematics', 'code' => 'IOSM']
];

$instMap = []; // code => id

$stmtInst = $conn->prepare("INSERT INTO institutes (name, code) VALUES (?, ?)");
foreach ($institutes as $inst) {
    $stmtInst->bind_param("ss", $inst['name'], $inst['code']);
    $stmtInst->execute();
    $instMap[$inst['code']] = $conn->insert_id;
    echo "Inserted Institute: {$inst['name']} ({$inst['code']})<br>";
}

// 2. Insert Programs
// Mapping Programs to Institutes
$programs = [
    ['name' => 'Bachelor of Science in Information Systems', 'code' => 'BSIS', 'inst' => 'ICDI'],
    ['name' => 'Bachelor of Science in Computer Science', 'code' => 'BSCS', 'inst' => 'ICDI'],
    ['name' => 'Bachelor of Science in Nursing', 'code' => 'BSN', 'inst' => 'ION'],
    ['name' => 'Bachelor of Science in Midwifery', 'code' => 'BSM', 'inst' => 'IOM'],
    ['name' => 'Bachelor of Science in Civil Engineering', 'code' => 'BSCE', 'inst' => 'IOE'],
    ['name' => 'Bachelor of Science in Life Science', 'code' => 'BSLS', 'inst' => 'IOSM'],
    ['name' => 'Bachelor of Science in Social Works', 'code' => 'BSSW', 'inst' => 'IBS'],
    ['name' => 'Bachelor of Science in Psychology', 'code' => 'BSP', 'inst' => 'IBS'] // Assuming BSP is under IBS
];

$stmtProg = $conn->prepare("INSERT INTO programs (institute_id, name, code) VALUES (?, ?, ?)");
foreach ($programs as $prog) {
    $instId = $instMap[$prog['inst']];
    $stmtProg->bind_param("iss", $instId, $prog['name'], $prog['code']);
    $stmtProg->execute();
    echo "Inserted Program: {$prog['name']} ({$prog['code']})<br>";
}

// 3. Insert Admins
$admins = [
    ['email' => 'admin_icdi@kld.edu.ph', 'inst' => 'ICDI'],
    ['email' => 'admin_ioe@kld.edu.ph', 'inst' => 'IOE'],
    ['email' => 'admin_ion@kld.edu.ph', 'inst' => 'ION'],
    ['email' => 'admin_iom@kld.edu.ph', 'inst' => 'IOM'],
    ['email' => 'admin_ibs@kld.edu.ph', 'inst' => 'IBS'],
    ['email' => 'admin_iosm@kld.edu.ph', 'inst' => 'IOSM']
];

$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';
$is_verified = 1;

$stmtUser = $conn->prepare("INSERT INTO users (school_id, full_name, email, password_hash, role, institute_id, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($admins as $index => $admin) {
    $instId = $instMap[$admin['inst']];
    $name = "Admin " . $admin['inst'];
    $schoolId = "ADMIN-" . str_pad($index + 1, 4, '0', STR_PAD_LEFT); // Generate dummy school ID
    
    $stmtUser->bind_param("sssssis", $schoolId, $name, $admin['email'], $password, $role, $instId, $is_verified);
    $stmtUser->execute();
    echo "Inserted Admin: $name ({$admin['email']})<br>";
}

// Enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "Database reset and seeded successfully.";
?>
