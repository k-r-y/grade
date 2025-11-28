<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
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
        }
        .grade-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
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
                <strong>Student ID:</strong> <?php echo $_SESSION['email']; // Placeholder for ID ?><br>
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
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch Data Here -->
            <tr>
                <td>IT 101</td>
                <td>Introduction to Computing</td>
                <td>1.50</td>
                <td>Passed</td>
            </tr>
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
