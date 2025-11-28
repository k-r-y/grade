<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic History | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="student_dashboard.php" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                <h1 class="vds-h2">Academic History</h1>
                <p class="vds-text-muted">Your complete grade transcript.</p>
            </div>
            <a href="print_grades.php" target="_blank" class="vds-btn vds-btn-secondary">
                <i class="bi bi-printer me-2"></i>Print Grades
            </a>
        </div>

        <!-- Timeline / Grouped by Semester (Mockup) -->
        <div class="mb-5">
            <h3 class="vds-h4 mb-3" style="color: var(--vds-forest);">1st Semester, A.Y. 2024-2025</h3>
            <div class="vds-card p-0 overflow-hidden">
                <table class="vds-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Subject Code</th>
                            <th>Description</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fetch and Loop Grades Here -->
                        <tr>
                            <td class="ps-4 fw-bold">IT 101</td>
                            <td>Introduction to Computing</td>
                            <td><span class="vds-pill vds-pill-pass">1.50</span></td>
                            <td>Passed</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>

</body>
</html>
