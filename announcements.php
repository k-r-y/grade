<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | KLD Grade System</title>
    <link rel="icon" type="image/png" href="assets/logo2.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        .announcement-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .announcement-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .priority-high { border-left-color: #ef4444; }
        .priority-medium { border-left-color: #f59e0b; }
        .priority-normal { border-left-color: var(--vds-forest); }
    </style>
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <span class="vds-pill mb-2" style="background: var(--vds-sage); color: var(--vds-forest);">News & Updates</span>
                <h1 class="vds-h2">Announcements</h1>
            </div>
            <div class="d-flex gap-2">
                <button class="vds-btn vds-btn-secondary btn-sm">Filter by Date</button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Announcement 1 -->
            <div class="col-12">
                <div class="vds-card p-4 announcement-card priority-high">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="vds-pill vds-pill-fail">Important</span>
                            <span class="vds-text-muted small"><i class="bi bi-clock me-1"></i>Posted 2 hours ago</span>
                        </div>
                        <i class="bi bi-pin-angle-fill text-danger"></i>
                    </div>
                    <h3 class="vds-h3 mb-2">Grade Submission for 2nd Semester Open</h3>
                    <p class="vds-text-lead mb-0">The portal is now open for grade encoding. Please ensure all grades are submitted before the deadline on December 15, 2025.</p>
                </div>
            </div>

            <!-- Announcement 2 -->
            <div class="col-12">
                <div class="vds-card p-4 announcement-card priority-medium">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="vds-pill vds-pill-warn">Maintenance</span>
                            <span class="vds-text-muted small"><i class="bi bi-clock me-1"></i>Nov 10, 2025</span>
                        </div>
                    </div>
                    <h3 class="vds-h3 mb-2">Scheduled System Maintenance</h3>
                    <p class="vds-text-muted mb-0">The system will undergo routine maintenance on November 15, 2025, from 10:00 PM to 2:00 AM. Access may be intermittent during this period.</p>
                </div>
            </div>

            <!-- Announcement 3 -->
            <div class="col-12">
                <div class="vds-card p-4 announcement-card priority-normal">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="vds-pill vds-pill-pass">Policy Update</span>
                            <span class="vds-text-muted small"><i class="bi bi-clock me-1"></i>Oct 28, 2025</span>
                        </div>
                    </div>
                    <h3 class="vds-h3 mb-2">New Grading Policies for Next Semester</h3>
                    <p class="vds-text-muted mb-0">Please review the updated student handbook regarding the new grading scale and retention policies effective next academic year.</p>
                </div>
            </div>

            <!-- Announcement 4 -->
            <div class="col-12">
                <div class="vds-card p-4 announcement-card priority-normal">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="vds-pill" style="background: #e0f2fe; color: #0284c7;">Reminder</span>
                            <span class="vds-text-muted small"><i class="bi bi-clock me-1"></i>Oct 15, 2025</span>
                        </div>
                    </div>
                    <h3 class="vds-h3 mb-2">Profile Update Required</h3>
                    <p class="vds-text-muted mb-0">All students are required to update their contact information and emergency contact details in the profile section.</p>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
