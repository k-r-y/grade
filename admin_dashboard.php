<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Admin';

require 'db_connect.php';

// Fetch Admin's Institute
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$admin_data = $res->fetch_assoc();
$institute_id = $admin_data['institute_id'];

// Fetch pending teachers for this institute
$stmtPending = $conn->prepare("SELECT id, full_name, email, school_id, created_at FROM users WHERE role = 'teacher' AND status = 'pending' AND institute_id = ?");
$stmtPending->bind_param("i", $institute_id);
$stmtPending->execute();
$pending_teachers = $stmtPending->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--vds-forest), #0f4c3a);
            color: white;
            border-radius: 24px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(13, 59, 46, 0.15);
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .action-card {
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            border-color: var(--vds-sage);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        
        <!-- Welcome Section -->
        <div class="dashboard-header mb-5 fade-in-up">
            <div class="position-relative" style="z-index: 2;">
                <span class="vds-pill mb-3" style="background: rgba(255,255,255,0.2); color: white; border: none;">Administration</span>
                <h1 class="vds-h1 mb-2" style="color: white;">Welcome, <?php echo htmlspecialchars($first_name); ?></h1>
                <p class="vds-text-lead mb-0" style="color: rgba(255,255,255,0.8);">Manage users, settings, and system configurations.</p>
            </div>
        </div>

        <!-- Actions Grid -->
        <div class="row g-4 mb-5">
            <!-- Manage Teachers -->
            <div class="col-md-4">
                <div class="vds-card p-4 action-card text-center">
                    <div class="icon-box mx-auto" style="background: var(--vds-vapor); color: var(--vds-forest);">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h3 class="vds-h3">Faculty Management</h3>
                    <p class="vds-text-muted mb-4">Approve requests and manage teacher accounts.</p>
                    <a href="admin_faculty.php" class="vds-btn vds-btn-primary w-100">Manage Faculty</a>
                </div>
            </div>

            <!-- Manage Students -->
            <div class="col-md-4">
                <div class="vds-card p-4 action-card text-center">
                    <div class="icon-box mx-auto" style="background: #dcfce7; color: #15803d;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="vds-h3">Manage Students</h3>
                    <p class="vds-text-muted mb-4">View student lists and update profiles.</p>
                    <a href="admin_students.php" class="vds-btn vds-btn-secondary w-100">View Students</a>
                </div>
            </div>

            <!-- System Settings -->
            <div class="col-md-4">
                <div class="vds-card p-4 action-card text-center">
                    <div class="icon-box mx-auto" style="background: #fef3c7; color: #b45309;">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <h3 class="vds-h3">System Settings</h3>
                    <p class="vds-text-muted mb-4">Configure grade periods and announcements.</p>
                    <a href="admin_settings.php" class="vds-btn vds-btn-secondary w-100">Open Settings</a>
                </div>
            </div>
        </div>

        <!-- Recent Notices -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="vds-h3 mb-0">System Updates</h3>
        </div>

        <div class="vds-card p-4">
            <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom">
                <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1rem; background: var(--vds-vapor); color: var(--vds-forest);">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <h5 class="vds-h4 mb-1">Teacher Registration Module Updated</h5>
                    <p class="vds-text-muted mb-0 small">The teacher registration process has been streamlined with new validation rules.</p>
                </div>
            </div>
            <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom">
                <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1rem; background: #dcfce7; color: #15803d;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h5 class="vds-h4 mb-1">System Maintenance Scheduled</h5>
                    <p class="vds-text-muted mb-0 small">Routine maintenance will occur on Nov 20, 2025. Expect brief downtime.</p>
                </div>
            </div>
            <div class="d-flex align-items-start gap-3">
                <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1rem; background: #fee2e2; color: #b91c1c;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h5 class="vds-h4 mb-1">Database Backup Reminder</h5>
                    <p class="vds-text-muted mb-0 small">Please ensure regular database backups are performed to prevent data loss.</p>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>

    <!-- CREATE TEACHER MODAL -->
    <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header border-0 p-4" style="background: var(--vds-forest); color: white;">
                    <h5 class="modal-title fw-bold">Create Teacher Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="process_create_teacher.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="vds-form-group">
                                    <label class="vds-label">First Name</label>
                                    <input type="text" class="vds-input" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vds-form-group">
                                    <label class="vds-label">Middle Name</label>
                                    <input type="text" class="vds-input" name="middle_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vds-form-group">
                                    <label class="vds-label">Last Name</label>
                                    <input type="text" class="vds-input" name="last_name" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="vds-form-group">
                                    <label class="vds-label">Email Address</label>
                                    <input type="email" class="vds-input" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">Password</label>
                                    <input type="password" class="vds-input" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">Confirm Password</label>
                                    <input type="password" class="vds-input" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user_type" value="teacher">
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="vds-btn vds-btn-secondary flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="vds-btn vds-btn-primary flex-grow-1">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
