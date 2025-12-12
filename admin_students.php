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

// Get Programs for Filter
$stmtProgs = $conn->prepare("SELECT id, code, name FROM programs WHERE institute_id = ? ORDER BY code ASC");
$stmtProgs->bind_param("i", $institute_id);
$stmtProgs->execute();
$programs = $stmtProgs->get_result();

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$prog_filter = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;
$sec_filter = isset($_GET['section']) ? trim($_GET['section']) : '';
$year_filter = isset($_GET['year_level']) ? intval($_GET['year_level']) : 0;

// Build Query
$sql = "
    SELECT u.full_name, u.email, u.school_id, u.created_at, u.section, p.code as program_code 
    FROM users u 
    LEFT JOIN programs p ON u.program_id = p.id 
    WHERE u.role = 'student' AND u.institute_id = ?
";
$params = [$institute_id];
$types = "i";

if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE ? OR u.school_id LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if ($prog_filter > 0) {
    $sql .= " AND u.program_id = ?";
    $params[] = $prog_filter;
    $types .= "i";
}

if (!empty($sec_filter)) {
    $sql .= " AND u.section LIKE ?";
    $secTerm = "%$sec_filter%";
    $params[] = $secTerm;
    $types .= "s";
}

if ($year_filter >= 1 && $year_filter <= 4) {
    $sql .= " AND u.section LIKE ?";
    $yearTerm = $year_filter . '%';
    $params[] = $yearTerm;
    $types .= "s";
}

$sql .= " ORDER BY u.created_at DESC";

$stmtStudents = $conn->prepare($sql);
$stmtStudents->bind_param($types, ...$params);
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

        <!-- Filters -->
        <div class="vds-card p-4 mb-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="vds-label">Search</label>
                    <input type="text" name="search" class="vds-input" placeholder="Name or Student ID" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="vds-label">Program</label>
                    <select name="program_id" class="vds-select">
                        <option value="0">All Programs</option>
                        <?php
                        $programs->data_seek(0);
                        while ($p = $programs->fetch_assoc()):
                        ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($prog_filter == $p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['code']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="vds-label">Section</label>
                    <input type="text" name="section" class="vds-input" placeholder="e.g. 101" value="<?php echo htmlspecialchars($sec_filter); ?>">
                </div>
                <div class="col-md-2">
                    <label class="vds-label">Year Level</label>
                    <select name="year_level" class="vds-select">
                        <option value="0">All Years</option>
                        <option value="1" <?php echo ($year_filter == 1) ? 'selected' : ''; ?>>1st Year</option>
                        <option value="2" <?php echo ($year_filter == 2) ? 'selected' : ''; ?>>2nd Year</option>
                        <option value="3" <?php echo ($year_filter == 3) ? 'selected' : ''; ?>>3rd Year</option>
                        <option value="4" <?php echo ($year_filter == 4) ? 'selected' : ''; ?>>4th Year</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="vds-btn vds-btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="vds-card overflow-hidden">
            <div class="table-responsive">
                <table class="vds-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Section</th>
                            <th>Email</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students->num_rows > 0): ?>
                            <?php while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold" style="color: var(--vds-forest);"><?php echo htmlspecialchars($student['school_id']); ?></td>
                                    <td>
                                        <?php
                                        $nameParts = explode(' ', trim($student['full_name']));
                                        if (count($nameParts) > 1) {
                                            $lastName = array_pop($nameParts);
                                            $firstName = implode(' ', $nameParts);
                                            echo htmlspecialchars("$lastName, $firstName");
                                        } else {
                                            echo htmlspecialchars($student['full_name']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($student['program_code'])): ?>
                                            <span class="vds-pill" style="background: var(--vds-sage); color: var(--vds-forest);">
                                                <?php echo htmlspecialchars($student['program_code']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($student['section'])): ?>
                                            <?php echo htmlspecialchars($student['section']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td class="text-muted"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center p-5 text-muted">No students found matching your criteria.</td>
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