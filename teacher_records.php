<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Records | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="teacher_dashboard.php" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                <h1 class="vds-h2">Class Records</h1>
                <p class="vds-text-muted">Manage and edit student grades manually.</p>
            </div>
        </div>

        <div class="vds-card p-4 mb-4">
            <form class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="vds-input" placeholder="Search by Student ID or Name...">
                </div>
                <div class="col-md-3">
                    <select class="vds-select">
                        <option selected>All Subjects</option>
                        <!-- Populate dynamically -->
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="vds-btn vds-btn-primary w-100">Search</button>
                </div>
            </form>
        </div>

        <div class="vds-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="vds-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student ID</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Placeholder Data -->
                        <tr>
                            <td class="ps-4 fw-bold">KLD-21-00123</td>
                            <td>Juan Dela Cruz</td>
                            <td>IT 101</td>
                            <td><span class="vds-pill vds-pill-pass">1.25</span></td>
                            <td>Passed</td>
                            <td class="text-end pe-4">
                                <button class="vds-btn vds-btn-secondary vds-btn-sm"><i class="bi bi-pencil"></i></button>
                                <button class="vds-btn vds-btn-danger vds-btn-sm"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer_dashboard.php'; ?>

</body>
</html>
