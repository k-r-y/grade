<?php
session_start();
require 'db_connect.php';

// Security Check: Only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle teacher registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $school_id   = trim($_POST['school_id']);
    $password    = $_POST['password'];
    $confirm     = $_POST['confirm_password'];
    $user_type   = 'teacher';

    if ($password !== $confirm) {
        header("Location: admin_faculty.php?error=" . urlencode("Passwords do not match"));
        exit();
    }

    // Check if email or school_id already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR school_id = ?");
    $check->bind_param("ss", $email, $school_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: admin_faculty.php?error=" . urlencode("Email or Employee ID already registered"));
        exit();
    }

    // Get Admin's Institute to assign to Teacher
    $admin_id = $_SESSION['user_id'];
    $stmtInst = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
    $stmtInst->bind_param("i", $admin_id);
    $stmtInst->execute();
    $institute_id = $stmtInst->get_result()->fetch_assoc()['institute_id'];

    $full_name = trim("$first_name $middle_name $last_name");
    $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into DB as Active and Verified
    $stmt = $conn->prepare("INSERT INTO users (school_id, full_name, email, password_hash, role, institute_id, is_verified, status) VALUES (?, ?, ?, ?, ?, ?, 1, 'active')");
    $stmt->bind_param("sssssi", $school_id, $full_name, $email, $hashed_pwd, $user_type, $institute_id);

    if ($stmt->execute()) {
        header("Location: admin_faculty.php?success=" . urlencode("Teacher account created successfully"));
        exit();
    } else {
        header("Location: admin_faculty.php?error=" . urlencode("Database error: " . $conn->error));
        exit();
    }
}
?>
