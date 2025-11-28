<?php
session_start();
if (!isset($_SESSION['email'])) {  // check login
    header("Location: login.php");
    exit();
}

// Use session variable for greeting if needed
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - KLD Grade System</title>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(180deg, #e0fbfc, #fefae0);
      color: #03045e;
    }
    .announcements-container { padding-top: 100px; padding-bottom: 50px; }
    .card-custom {
      border: none;
      border-radius: 18px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(10px);
      padding: 30px;
      margin-bottom: 20px;
    }
    .section-title { font-weight: 600; color: #023047; margin-bottom: 30px; text-align: center; }
    .announcement-item { font-size: 1rem; margin-bottom: 15px; }
    .announcement-item i { margin-right: 10px; }
    .btn-back { margin-top: 20px; }
  </style>
</head>
<body>

  <?php include 'navbar_dashboard.php'; ?>

  <div class="container announcements-container">
    <h2 class="section-title"><i class="bi bi-megaphone me-2"></i>Announcements</h2>

    <!-- Announcement Cards -->
    <div class="card card-custom">
      <div class="announcement-item">
        <i class="bi bi-circle-fill text-primary"></i>
        Grade submission for 2nd Semester is now open.
      </div>
      <div class="announcement-item">
        <i class="bi bi-circle-fill text-success"></i>
        System maintenance scheduled on November 15, 2025.
      </div>
      <div class="announcement-item">
        <i class="bi bi-circle-fill text-warning"></i>
        New grading policies will take effect next semester.
      </div>
      <div class="announcement-item">
        <i class="bi bi-circle-fill text-danger"></i>
        Reminder: Update your profile to ensure accurate student information.
      </div>
    </div>

    <!-- Back Button -->
    <div class="text-center btn-back">
      <a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard</a>
    </div>
  </div>

  <?php include 'footer_dashboard.php'; ?>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
