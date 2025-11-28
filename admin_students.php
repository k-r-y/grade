<?php
session_start();
require 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$first_name = $_SESSION['full_name'];

// Get Admin Institute
$stmt = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$institute_id = $stmt->get_result()->fetch_assoc()['institute_id'];

// Fetch Students
$stmtStudents = $conn->prepare("SELECT full_name, email, school_id, program_id, created_at FROM users WHERE role = 'student' AND institute_id = ?");
$stmtStudents->bind_param("i", $institute_id);
$stmtStudents->execute();
$students = $stmtStudents->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="admin_dashboard.php" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                <h1 class="vds-h2">Manage Students</h1>
                <p class="vds-text-muted">View and manage students in your institute.</p>
            </div>
        </div>

        <div class="vds-card overflow-hidden">
            <div class="table-responsive">
                <table class="vds-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students->num_rows > 0): ?>
                            <?php while($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold" style="color: var(--vds-forest);"><?php echo htmlspecialchars($student['school_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td class="text-muted"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center p-5 text-muted">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer_dashboard.php'; ?>

</body>
</html>
