<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$school_id = $_SESSION['school_id'] ?? 'N/A';

// Fetch grades
$stmt = $conn->prepare("
    SELECT g.*, c.units, c.subject_description 
    FROM grades g
    LEFT JOIN classes c ON g.class_id = c.id
    WHERE g.student_id = ?
    ORDER BY g.semester DESC, g.subject_code ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$grades = [];
while ($row = $result->fetch_assoc()) {
    if (empty($row['subject_name']) && !empty($row['subject_description'])) {
        $row['subject_name'] = $row['subject_description'];
    }
    $grades[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Report - <?php echo htmlspecialchars($full_name); ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: white;
            font-family: 'Inter', sans-serif;
            color: #000;
        }
        .report-header {
            text-align: center;
            margin-bottom: 3rem;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
        }
        .logo {
            height: 80px;
            margin-bottom: 1rem;
        }
        .student-info {
            margin-bottom: 2rem;
        }
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        .grade-table th, .grade-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            font-size: 0.9rem;
        }
        .grade-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-5">

    <div class="no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary">Print Report</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="report-header">
        <img src="assets/logo2.png" alt="KLD Logo" class="logo">
        <h2>Kolehiyo ng Lungsod ng Dasmariñas</h2>
        <p>Official Grade Report</p>
    </div>

    <div class="student-info">
        <div class="row">
            <div class="col-6">
                <strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?><br>
                <strong>Student ID:</strong> <?php echo htmlspecialchars($school_id); ?><br>
            </div>
            <div class="col-6 text-end">
                <strong>Date Generated:</strong> <?php echo date('F d, Y'); ?><br>
                <strong>Academic Year:</strong> 2024-2025
            </div>
        </div>
    </div>

    <table class="grade-table">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Description</th>
                <th class="text-center">Units</th>
                <th class="text-center">Midterm</th>
                <th class="text-center">Final</th>
                <th class="text-center">Semestral</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($grades) > 0): ?>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($grade['units'] ?? 3); ?></td>
                        <td class="text-center"><?php echo $grade['midterm'] ? number_format($grade['midterm'], 2) : '-'; ?></td>
                        <td class="text-center"><?php echo $grade['final'] ? number_format($grade['final'], 2) : '-'; ?></td>
                        <td class="text-center fw-bold">
                            <?php 
                                $g = floatval($grade['grade']);
                                echo $g > 0 ? number_format($g, 2) : '-';
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($grade['remarks']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4">No grades found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-5">
        <div class="row">
            <div class="col-6">
                <p><strong>Certified True Copy:</strong></p>
                <br><br>
                <p>__________________________<br>College Registrar</p>
            </div>
        </div>
    </div>

</body>
</html>
