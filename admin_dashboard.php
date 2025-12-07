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
        /* Specific Dashboard Overrides */
        .dashboard-header {
            background: var(--vds-forest);
            color: white;
            border-radius: var(--vds-radius-lg);
            padding: 3rem;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 4px 20px rgba(13, 59, 46, 0.1);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.02em;
            color: var(--vds-forest);
        }

        .stat-label {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--vds-text-muted);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .chart-container {
            position: relative;
            height: 240px;
            width: 100%;
        }

        .action-card {
            border: 1px solid rgba(13, 59, 46, 0.1);
            transition: all 0.2s ease;
            height: 100%;
            background: #fff;
        }

        .action-card:hover {
            transform: translateY(-2px);
            border-color: var(--vds-forest);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .update-item {
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .update-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">

        <!-- Minimal Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-end mb-5">
            <div class="position-relative" style="z-index: 2;">
                <span class="badge bg-white text-success mb-2">Administration</span>
                <h1 class="display-5 fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($first_name); ?>.</h1>
                <p class="lead mb-0 opacity-75">Here's what's happening in your institute today.</p>
            </div>
            <div class="text-end d-none d-md-block position-relative" style="z-index: 2;">
                <p class="mb-0 opacity-75 small"><?php echo date('l, F j, Y'); ?></p>
            </div>
        </div>

        <!-- Analytics Filters -->
        <div class="vds-card p-4 mb-5">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="vds-label">Program</label>
                    <select id="filterProgram" class="vds-select">
                        <option value="">All Programs</option>
                        <!-- Populated by JS -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="vds-label">Year Level</label>
                    <select id="filterYear" class="vds-select">
                        <option value="">All Years</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="vds-label">Academic Year</label>
                    <select id="filterAcad" class="vds-select">
                        <option value="">All Semesters</option>
                        <option value="1st Sem 2024-2025">1st Sem 2024-2025</option>
                        <option value="2nd Sem 2024-2025">2nd Sem 2024-2025</option>
                        <option value="Summer 2025">Summer 2025</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="vds-btn vds-btn-primary w-100" onclick="fetchAnalytics()">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Cards (Quick Stats) -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="vds-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="stat-label">Total Students</div>
                            <i class="bi bi-people text-muted"></i>
                        </div>
                        <div class="stat-value" id="totalStudents">-</div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i> Active</small>
                        <small class="text-muted ms-1">across all programs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vds-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="stat-label">Grade Completion</div>
                            <i class="bi bi-file-earmark-check text-muted"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <div class="stat-value" id="gradeCompletion">-</div>
                            <span class="text-muted fs-5">%</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px; border-radius: 3px;">
                            <div class="progress-bar bg-success" id="completionBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Overall submission rate</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vds-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="stat-label">Faculty Count</div>
                            <i class="bi bi-person-badge text-muted"></i>
                        </div>
                        <div class="stat-value" id="totalTeachers">-</div>
                    </div>
                    <div class="mt-3">
                        <a href="admin_faculty.php" class="vds-btn vds-btn-secondary vds-btn-sm w-100 justify-content-between">
                            Manage Faculty <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Left Column: Charts -->
            <div class="col-lg-8">
                <div class="vds-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="vds-h3 mb-0">Academic Performance</h3>
                        <div class="vds-pill" style="background: var(--vds-vapor);">Avg. GWA by Program</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="vds-card p-4 h-100">
                            <h5 class="vds-h5 mb-3">Student Distribution</h5>
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="studentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="vds-card p-4 h-100">
                            <h5 class="vds-h5 mb-3">Pass / Fail Ratio</h5>
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="passFailChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Quick Actions & Updates -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="mb-4">
                    <h5 class="vds-label mb-3">Quick Actions</h5>
                    <div class="d-grid gap-3">
                        <a href="admin_faculty.php" class="vds-card p-3 action-card d-flex align-items-center gap-3 text-decoration-none">
                            <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1.2rem; background: var(--vds-sage); color: var(--vds-forest);">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Add Faculty</h6>
                                <small class="text-muted">Register new teachers</small>
                            </div>
                        </a>
                        <a href="admin_students.php" class="vds-card p-3 action-card d-flex align-items-center gap-3 text-decoration-none">
                            <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1.2rem; background: var(--vds-sage); color: var(--vds-forest);">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Student Masterlist</h6>
                                <small class="text-muted">View all enrollments</small>
                            </div>
                        </a>
                        <a href="admin_settings.php" class="vds-card p-3 action-card d-flex align-items-center gap-3 text-decoration-none">
                            <div class="icon-box mb-0" style="width: 40px; height: 40px; font-size: 1.2rem; background: var(--vds-sage); color: var(--vds-forest);">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">System Settings</h6>
                                <small class="text-muted">Configure grading periods</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- System Updates Feed -->
                <div class="vds-card p-4">
                    <h5 class="vds-h5 mb-3">System Updates</h5>

                    <div class="update-item d-flex gap-3">
                        <div class="mt-1" style="color: var(--vds-forest);"><i class="bi bi-info-circle-fill"></i></div>
                        <div>
                            <p class="mb-1 fw-bold small">Registration Module</p>
                            <p class="text-muted small mb-0">Teacher registration has been streamlined with new validation rules.</p>
                            <small class="text-muted" style="font-size: 0.7rem;">Today</small>
                        </div>
                    </div>

                    <div class="update-item d-flex gap-3">
                        <div class="mt-1" style="color: var(--vds-yellow);"><i class="bi bi-cone-striped"></i></div>
                        <div>
                            <p class="mb-1 fw-bold small">Maintenance Scheduled</p>
                            <p class="text-muted small mb-0">Routine maintenance on Nov 20, 2025.</p>
                            <small class="text-muted" style="font-size: 0.7rem;">Yesterday</small>
                        </div>
                    </div>

                    <div class="update-item d-flex gap-3">
                        <div class="mt-1" style="color: var(--vds-coral);"><i class="bi bi-shield-exclamation"></i></div>
                        <div>
                            <p class="mb-1 fw-bold small">Backup Reminder</p>
                            <p class="text-muted small mb-0">Please ensure regular database backups.</p>
                            <small class="text-muted" style="font-size: 0.7rem;">2 days ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>

    <!-- CREATE TEACHER MODAL -->
    <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content vds-modal border-0 p-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Create Teacher Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-4">
                    <form action="process_create_teacher.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="vds-form-group mb-0">
                                    <label class="vds-label">First Name</label>
                                    <input type="text" class="vds-input" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vds-form-group mb-0">
                                    <label class="vds-label">Middle Name</label>
                                    <input type="text" class="vds-input" name="middle_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vds-form-group mb-0">
                                    <label class="vds-label">Last Name</label>
                                    <input type="text" class="vds-input" name="last_name" required>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="vds-form-group mb-0">
                                    <label class="vds-label">Email Address</label>
                                    <input type="email" class="vds-input" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="vds-form-group mb-0">
                                    <label class="vds-label">Password</label>
                                    <input type="password" class="vds-input" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="vds-form-group mb-0">
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
        // Chart Defaults
        Chart.defaults.font.family = '"Inter", sans-serif';
        Chart.defaults.color = '#6b7280';
        Chart.defaults.scale.grid.color = 'rgba(0,0,0,0.03)';
        Chart.defaults.plugins.tooltip.padding = 10;
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(13, 59, 46, 0.9)';
        Chart.defaults.plugins.tooltip.titleFont = {
            size: 13,
            weight: 600
        };
        Chart.defaults.plugins.tooltip.bodyFont = {
            size: 13
        };
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        // Chart Instances
        let studentsChart = null;
        let passFailChart = null;
        let performanceChart = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Populate Programs
            fetch('api.php?action=get_programs&institute_id=<?php echo $institute_id; ?>')
                .then(res => res.json())
                .then(data => {
                    const sel = document.getElementById('filterProgram');
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.code;
                        sel.appendChild(opt);
                    });
                })
                .catch(err => console.log('Programs API not available or error', err));

            // Initial Fetch
            fetchAnalytics();
        });

        function fetchAnalytics() {
            const prog = document.getElementById('filterProgram').value;
            const year = document.getElementById('filterYear').value;
            const acad = document.getElementById('filterAcad').value;

            const params = new URLSearchParams({
                action: 'get_analytics',
                program_id: prog,
                year_level: year,
                academic_year: acad
            });

            fetch('api.php?' + params.toString())
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        const data = res.data;

                        // Update Quick Stats
                        document.getElementById('totalStudents').textContent = data.total_students;
                        document.getElementById('gradeCompletion').textContent = data.grade_completion || '0';
                        document.getElementById('completionBar').style.width = (data.grade_completion || 0) + '%';
                        document.getElementById('totalTeachers').textContent = data.total_teachers;

                        // Update Charts
                        updateStudentsChart(data.students_by_program);
                        updatePassFailChart(data.pass_fail_stats);
                        updatePerformanceChart(data.avg_grade_by_program);
                    }
                })
                .catch(err => console.error('Error fetching analytics:', err));
        }

        function updateStudentsChart(data) {
            const ctx = document.getElementById('studentsChart').getContext('2d');
            const labels = data.map(i => i.code);
            const values = data.map(i => i.count);

            if (studentsChart) studentsChart.destroy();

            studentsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#0d3b2e', '#4f772d', '#90a955', '#ecf39e', '#132a13'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'Inter'
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        function updatePassFailChart(data) {
            const ctx = document.getElementById('passFailChart').getContext('2d');
            // Ensure data exists
            const passed = data.find(i => i.status === 'Passed')?.count || 0;
            const failed = data.find(i => i.status === 'Failed')?.count || 0;

            if (passFailChart) passFailChart.destroy();

            passFailChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Passed', 'Failed'],
                    datasets: [{
                        label: 'Students',
                        data: [passed, failed],
                        backgroundColor: ['#4f772d', '#9b2226'], // Verdant Green vs Red
                        borderRadius: 5,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function updatePerformanceChart(data) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const labels = data.map(i => i.code);
            const values = data.map(i => i.avg_grade);

            if (performanceChart) performanceChart.destroy();

            performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Avg Grade',
                        data: values,
                        borderColor: '#0d3b2e',
                        backgroundColor: 'rgba(13, 59, 46, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            reverse: true,
                            min: 1,
                            max: 5,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>

</body>

</html>