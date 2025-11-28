<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch All Grades
$stmt = $conn->prepare("SELECT * FROM grades WHERE student_id = ? ORDER BY academic_year DESC, semester DESC, created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .vds-card { box-shadow: none !important; border: 1px solid #ccc !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="vds-bg-vapor">

    <div class="no-print">
        <?php include 'navbar_dashboard.php'; ?>
    </div>

    <div class="vds-container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-5 no-print">
            <div>
                <span class="vds-pill mb-2" style="background: var(--vds-sage); color: var(--vds-forest);">Academic Records</span>
                <h1 class="vds-h2">My Grades</h1>
            </div>
            <button class="vds-btn vds-btn-secondary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>

        <div class="vds-card p-0 overflow-hidden">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h3 class="vds-h3 mb-0">Grade History</h3>
                <span class="vds-text-muted small">Generated on <?php echo date('M d, Y'); ?></span>
            </div>
            <div class="table-responsive">
                <table class="vds-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Academic Year</th>
                            <th>Semester</th>
                            <th>Subject Code</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th class="text-end pe-4">Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><?php echo htmlspecialchars($row['academic_year']); ?></td>
                                    <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                    <td class="fw-bold" style="color: var(--vds-forest);"><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td>
                                        <?php 
                                            $gradeVal = floatval($row['grade']);
                                            $badgeClass = 'vds-pill-pass';
                                            if ($gradeVal > 3.0) $badgeClass = 'vds-pill-fail';
                                            elseif ($gradeVal >= 2.5) $badgeClass = 'vds-pill-warn';
                                        ?>
                                        <span class="vds-pill <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($row['grade']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td class="text-end pe-4 text-muted"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center p-5">
                                    <div class="text-muted">
                                        <i class="bi bi-folder2-open display-4 d-block mb-3" style="opacity: 0.3;"></i>
                                        No grades found in your record.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="no-print">
        <?php include 'footer_dashboard.php'; ?>
    </div>

</body>
</html>
