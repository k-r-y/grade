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
</head>
<body>

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container" style="padding-top: 40px; padding-bottom: 60px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="vds-h2">My Grades</h1>
            <button class="vds-btn vds-btn-secondary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print
            </button>
        </div>

        <div class="vds-card">
            <div class="table-responsive">
                <table class="vds-table">
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Subject Code</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['academic_year']); ?></td>
                                    <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td>
                                        <span class="vds-badge <?php echo ($row['grade'] <= 3.0) ? 'vds-badge-success' : 'vds-badge-fail'; ?>">
                                            <?php echo htmlspecialchars($row['grade']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td style="color: var(--vds-text-muted);"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center p-5 text-muted">
                                    <i class="bi bi-folder2-open display-4 d-block mb-3"></i>
                                    No grades found.
                                </td>
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
