<?php
session_start();
require 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $teacher_id = intval($_POST['approve_id']);
    $admin_id = $_SESSION['user_id'];
    
    // 1. Get Admin's Institute
    $stmtAdmin = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
    $stmtAdmin->bind_param("i", $admin_id);
    $stmtAdmin->execute();
    $admin_res = $stmtAdmin->get_result();
    $admin_data = $admin_res->fetch_assoc();
    $admin_institute = $admin_data['institute_id'];
    
    // 2. Verify Teacher belongs to same institute AND is pending
    $stmtCheck = $conn->prepare("SELECT id FROM users WHERE id = ? AND institute_id = ? AND role = 'teacher' AND status = 'pending'");
    $stmtCheck->bind_param("ii", $teacher_id, $admin_institute);
    $stmtCheck->execute();
    $check_res = $stmtCheck->get_result();
    
    if ($check_res->num_rows === 1) {
        // 3. Approve
        $stmtUpdate = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmtUpdate->bind_param("i", $teacher_id);
        $stmtUpdate->execute();
    }
}

header("Location: admin_dashboard.php");
exit();
?>
