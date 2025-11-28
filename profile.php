<?php
session_start();
if (!isset($_SESSION['email'])) {  // check login
    header("Location: login.php");
    exit();
}

// Use session variables
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - KLD Grade System</title>
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
    .profile-container { padding-top: 100px; padding-bottom: 50px; }
    .card-custom {
      border: none;
      border-radius: 18px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(10px);
      padding: 30px;
      max-width: 600px;
      margin: auto;
    }
    .section-title { font-weight: 600; color: #023047; margin-bottom: 30px; text-align: center; }
    .profile-info { font-size: 1rem; margin-bottom: 15px; }
    .profile-info strong { width: 120px; display: inline-block; }
    .btn-back { margin-top: 20px; }
  </style>
</head>
<body>

  <?php include 'navbar_dashboard.php'; ?>

  <div class="container profile-container">
    <h2 class="section-title"><i class="bi bi-person-circle me-2"></i>My Profile</h2>

    <div class="card card-custom text-start">
      <div class="profile-info"><strong>First Name:</strong> <?php echo htmlspecialchars($first_name); ?></div>
      <div class="profile-info"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
      <div class="profile-info"><strong>Last Name:</strong> Placeholder</div>
      <div class="profile-info"><strong>Phone:</strong> Placeholder</div>
      <div class="profile-info"><strong>Address:</strong> Placeholder</div>

      <div class="text-center btn-back">
        <a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard</a>
      </div>
    </div>
  </div>

  <?php include 'footer_dashboard.php'; ?>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
