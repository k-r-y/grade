<?php
require 'includes/session_config.php';
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institute Settings | KLD Grade System</title>
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
                <h1 class="vds-h2">Institute Settings</h1>
                <p class="vds-text-muted">Configure academic periods and system variables.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="vds-card p-4">
                    <h3 class="vds-h4 mb-4">Academic Year & Semester</h3>
                    <form id="settingsForm">
                        <div class="vds-form-group">
                            <label class="vds-label">Current Academic Year</label>
                            <input type="text" class="vds-input" name="current_academic_year" id="current_academic_year" placeholder="e.g. 2024-2025" required>
                        </div>
                        <div class="vds-form-group">
                            <label class="vds-label">Current Semester</label>
                            <select class="vds-select" name="current_semester" id="current_semester">
                                <option value="1st Sem">1st Sem</option>
                                <option value="2nd Sem">2nd Sem</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <button type="submit" class="vds-btn vds-btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="vds-card p-4">
                    <h3 class="vds-h4 mb-4">System Maintenance</h3>
                    <div class="vds-form-group">
                        <label class="vds-label">Registration Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="regSwitch" checked>
                            <label class="form-check-label" for="regSwitch">Allow new student registrations</label>
                        </div>
                    </div>
                    <div class="vds-form-group">
                        <label class="vds-label">Grade Submission</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="gradeSwitch" checked>
                            <label class="form-check-label" for="gradeSwitch">Allow teachers to submit grades</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer_dashboard.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const form = document.getElementById('settingsForm');
            const yearInput = document.getElementById('current_academic_year');
            const semInput = document.getElementById('current_semester');

            // Load Settings
            try {
                const res = await fetch('api.php?action=get_settings');
                const data = await res.json();
                if (data.success && data.settings) {
                    if (data.settings.current_academic_year) yearInput.value = data.settings.current_academic_year;
                    if (data.settings.current_semester) semInput.value = data.settings.current_semester;
                }
            } catch (err) {
                console.error('Failed to load settings', err);
            }

            // Save Settings
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button');
                btn.disabled = true;
                btn.innerHTML = 'Saving...';

                try {
                    const res = await fetch('api.php?action=update_settings', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                            settings: {
                                current_academic_year: yearInput.value,
                                current_semester: semInput.value
                            }
                        })
                    });
                    const data = await res.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            confirmButtonColor: '#0D3B2E'
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Error', text: data.message});
                    }
                } catch (err) {
                    Swal.fire({icon: 'error', title: 'Error', text: 'System Error'});
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Save Changes';
                }
            });
        });
    </script>
</body>
</html>
