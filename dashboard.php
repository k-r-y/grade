<?php
session_start();
if (!isset($_SESSION['email'])) {  // check login
    header("Location: login.php");
    exit();
}

// Use first_name from session
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - KLD Grade System</title>

  <!-- Local Bootstrap & Icons -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <style>
    body {
      background: linear-gradient(180deg, #e0fbfc, #fefae0);
      font-family: 'Poppins', sans-serif;
      color: #03045e;
    }
    .dashboard-container { padding-top: 100px; }
    .welcome-card {
      background: linear-gradient(45deg, #0077b6, #48cae4);
      color: white;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .card-custom {
      border: none;
      border-radius: 18px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }
    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .section-title { font-weight: 600; color: #023047; }
    .quick-link { text-decoration: none; color: #0077b6; font-weight: 500; transition: 0.3s; }
    .quick-link:hover { color: #023e8a; text-decoration: underline; }
  </style>
</head>
<body>

  <?php include 'navbar_dashboard.php'; ?>

  <div class="container dashboard-container">
    
    <!-- Welcome Card -->
    <div class="welcome-card p-5 mb-5 text-center">
      <h2 class="fw-bold mb-2">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h2>
      <p class="lead mb-0">Here’s your personalized dashboard — manage grades, view announcements, and access your profile easily.</p>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row g-4">

      <!-- Grades Overview -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-journal-text display-4 text-primary mb-3"></i>
          <h5 class="fw-bold">My Grades</h5>
          <p>Check your latest subject grades and performance reports.</p>
          <a href="grades.php" class="btn btn-primary">View Grades</a>
        </div>
      </div>

      <!-- Profile Card -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-person-circle display-4 text-success mb-3"></i>
          <h5 class="fw-bold">Profile</h5>
          <p>Manage your account details, password, and contact information.</p>
          <a href="profile.php" class="btn btn-success">Go to Profile</a>
        </div>
      </div>

      <!-- Announcements -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-megaphone display-4 text-warning mb-3"></i>
          <h5 class="fw-bold">Announcements</h5>
          <p>Stay updated with the latest system and school announcements.</p>
          <a href="announcements.php" class="btn btn-warning text-white">View Updates</a>
        </div>
      </div>
    </div>

    <!-- Recent Notices -->
    <div class="mt-5">
      <h4 class="section-title mb-3"><i class="bi bi-bell-fill me-2"></i>Recent Notices</h4>
      <div class="card card-custom p-4">
        <ul class="list-unstyled mb-0">
          <li class="mb-3"><i class="bi bi-circle-fill text-primary me-2"></i>Grade submission for 2nd Semester is now open.</li>
          <li class="mb-3"><i class="bi bi-circle-fill text-success me-2"></i>System maintenance scheduled on November 15, 2025.</li>
          <li><i class="bi bi-circle-fill text-danger me-2"></i>Reminder: Update your profile to ensure accurate student information.</li>
        </ul>
      </div>
    </div>
  </div>

  <?php include 'footer_dashboard.php'; ?>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
