<?php
session_start();
require 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Get Admin Institute
$stmt = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$institute_id = $stmt->get_result()->fetch_assoc()['institute_id'];

// Fetch Pending Teachers
$stmtPending = $conn->prepare("SELECT id, full_name, email, school_id, created_at FROM users WHERE role = 'teacher' AND status = 'pending' AND institute_id = ?");
$stmtPending->bind_param("i", $institute_id);
$stmtPending->execute();
$pending_teachers = $stmtPending->get_result();

// Fetch Active Teachers
$stmtActive = $conn->prepare("SELECT id, full_name, email, school_id, created_at FROM users WHERE role = 'teacher' AND status = 'active' AND institute_id = ?");
$stmtActive->bind_param("i", $institute_id);
$stmtActive->execute();
$active_teachers = $stmtActive->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management | KLD Grade System</title>
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
                <h1 class="vds-h2">Faculty Management</h1>
                <p class="vds-text-muted">Approve pending registrations and manage active faculty.</p>
            </div>
            <button class="vds-btn vds-btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                <i class="bi bi-plus-lg me-2"></i>Add Faculty
            </button>
        </div>

        <!-- Pending Approvals -->
        <?php if ($pending_teachers->num_rows > 0): ?>
        <div class="mb-5">
            <h3 class="vds-h3 mb-3 text-warning"><i class="bi bi-hourglass-split me-2"></i>Pending Approvals</h3>
            <div class="vds-card p-0 overflow-hidden border-warning">
                <div class="table-responsive">
                    <table class="vds-table mb-0">
                        <thead style="background: #fffbeb;">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Email</th>
                                <th>ID Number</th>
                                <th>Date Registered</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($teacher = $pending_teachers->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td><span class="vds-pill vds-pill-warn"><?php echo htmlspecialchars($teacher['school_id']); ?></span></td>
                                <td class="text-muted small"><?php echo date('M d, Y', strtotime($teacher['created_at'])); ?></td>
                                <td class="text-end pe-4">
                                    <form action="approve_teacher.php" method="POST" class="d-inline">
                                        <input type="hidden" name="approve_id" value="<?php echo $teacher['id']; ?>">
                                        <button type="submit" class="vds-btn vds-btn-primary vds-btn-sm">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Active Faculty -->
        <div class="mb-5">
            <h3 class="vds-h3 mb-3">Active Faculty</h3>
            <div class="vds-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="vds-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Email</th>
                                <th>ID Number</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($active_teachers->num_rows > 0): ?>
                                <?php while($teacher = $active_teachers->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['school_id']); ?></td>
                                    <td><span class="vds-pill vds-pill-pass">Active</span></td>
                                    <td class="text-end pe-4">
                                        <button class="vds-btn vds-btn-secondary vds-btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center p-5 text-muted">No active faculty found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>
    
    <!-- Create Teacher Modal -->
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
                                    <input type="text" class="vds-input" name="middle_name">
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
                             <div class="col-12">
                                <div class="vds-form-group">
                                    <label class="vds-label">Employee ID (School ID)</label>
                                    <input type="text" class="vds-input" name="school_id" placeholder="T-2024-XXXX" required>
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

    <script>
        // Check for success/error parameters in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
             Swal.fire({
                icon: 'success',
                title: 'Success',
                text: urlParams.get('success'),
                confirmButtonColor: '#0D3B2E'
            });
        }
        if (urlParams.has('error')) {
             Swal.fire({
                icon: 'error',
                title: 'Error',
                text: urlParams.get('error'),
                confirmButtonColor: '#d33'
            });
        }
    </script>
</body>
</html>
