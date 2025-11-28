<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Fetch Grades
$stmt = $conn->prepare("SELECT * FROM grades WHERE student_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$grades = [];
$total_grade = 0;
$count = 0;

while ($row = $result->fetch_assoc()) {
    $grades[] = $row;
    $total_grade += $row['grade'];
    $count++;
}

$gpa = $count > 0 ? number_format($total_grade / $count, 2) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body>

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container" style="padding-top: 40px; padding-bottom: 60px;">
        
        <!-- Welcome Section -->
        <div class="vds-card mb-5" style="background: linear-gradient(135deg, var(--vds-forest), var(--vds-moss)); color: white;">
            <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="vds-h2" style="color: white;">Welcome, <?php echo htmlspecialchars($full_name); ?></h1>
                    <p class="vds-text-lead" style="color: rgba(255,255,255,0.9);">Here is your academic overview.</p>
                </div>
                <div class="text-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(5px);">
                    <span style="font-size: 0.9rem; opacity: 0.8;">Average Grade</span>
                    <div style="font-size: 2.5rem; font-weight: 700; line-height: 1;"><?php echo $gpa; ?></div>
                </div>
            </div>
        </div>

        <div class="vds-grid-3 mb-5">
            <!-- Quick Actions -->
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-journal-text vds-icon-lg mb-3" style="color: var(--vds-forest);"></i>
                <h3 class="vds-h3">My Grades</h3>
                <p class="vds-text-muted">View full grade history.</p>
                <a href="grades.php" class="vds-btn vds-btn-primary w-100 mt-2">View Details</a>
            </div>
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-person vds-icon-lg mb-3" style="color: var(--vds-moss);"></i>
                <h3 class="vds-h3">Profile</h3>
                <p class="vds-text-muted">Update your information.</p>
                <a href="profile.php" class="vds-btn vds-btn-secondary w-100 mt-2">Manage Profile</a>
            </div>
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-calendar-event vds-icon-lg mb-3" style="color: var(--vds-sage);"></i>
                <h3 class="vds-h3">Schedule</h3>
                <p class="vds-text-muted">View class schedule.</p>
                <button class="vds-btn vds-btn-secondary w-100 mt-2" disabled>Coming Soon</button>
            </div>
        </div>

        <!-- Recent Grades -->
        <h3 class="vds-h3 mb-4">Recent Grades</h3>
        <div class="vds-card">
            <div class="table-responsive">
                <table class="vds-table">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($grades) > 0): ?>
                            <?php foreach (array_slice($grades, 0, 5) as $grade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['subject_code']); ?></td>
                                    <td>
                                        <span class="vds-badge <?php echo ($grade['grade'] <= 3.0) ? 'vds-badge-success' : 'vds-badge-fail'; ?>">
                                            <?php echo htmlspecialchars($grade['grade']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($grade['remarks']); ?></td>
                                    <td style="color: var(--vds-text-muted);"><?php echo date('M d, Y', strtotime($grade['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center p-4 text-muted">No grades posted yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($grades) > 5): ?>
                <div class="p-3 text-center border-top">
                    <a href="grades.php" style="color: var(--vds-forest); font-weight: 600; text-decoration: none;">View All Grades</a>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>

</body>
</html>
