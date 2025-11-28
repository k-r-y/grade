<?php
session_start();
require 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch Unique Subjects taught by this teacher from grades table
$stmt = $conn->prepare("SELECT DISTINCT subject_code, semester FROM grades WHERE teacher_id = ? ORDER BY semester DESC, subject_code ASC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subjects = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes | KLD Grade System</title>
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
                <h1 class="vds-h2">My Classes</h1>
                <p class="vds-text-muted">Subjects you have submitted grades for.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if ($subjects->num_rows > 0): ?>
                <?php while($subject = $subjects->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="vds-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="vds-icon-box" style="background: var(--vds-vapor); color: var(--vds-forest);">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <span class="vds-pill vds-pill-pass">Active</span>
                        </div>
                        <h3 class="vds-h4 mb-2"><?php echo htmlspecialchars($subject['subject_code']); ?></h3>
                        <p class="vds-text-muted small mb-4"><?php echo htmlspecialchars($subject['semester']); ?></p>
                        <button class="vds-btn vds-btn-secondary w-100">View Students</button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="vds-card p-5 text-center">
                        <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                        <h3 class="vds-h4 text-muted">No classes found</h3>
                        <p class="text-muted">You haven't uploaded any grades yet.</p>
                        <a href="teacher_dashboard.php#uploadSection" class="vds-btn vds-btn-primary mt-3">Upload Grades</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer_dashboard.php'; ?>

</body>
</html>
