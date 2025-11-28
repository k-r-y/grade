<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - KLD Grade System</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <style>
    body {
      background: linear-gradient(180deg, #f7f9fb, #eef6ff);
      font-family: 'Poppins', sans-serif;
      color: #03045e;
    }
    .dashboard-container { padding-top: 100px; }

    .welcome-card {
      background: linear-gradient(45deg, #1a73e8, #4dabf7);
      color: white;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .card-custom {
      border: none;
      border-radius: 18px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      transition: 0.3s ease;
    }
    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .section-title {
      font-weight: 600; 
      color: #023047;
    }
  </style>
</head>
<body>

<?php include 'navbar_dashboard.php'; ?>

<div class="container dashboard-container">

    <!-- WELCOME CARD -->
    <div class="welcome-card p-5 mb-5 text-center">
      <h2 class="fw-bold mb-2">Welcome, Admin <?php echo htmlspecialchars($first_name); ?>!</h2>
      <p class="lead mb-0">Manage teachers, students, and system settings in one place.</p>
    </div>

    <!-- ADMIN ACTION CARDS -->
    <div class="row g-4">

      <!-- Manage Teachers -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-person-badge-fill display-4 text-primary mb-3"></i>
          <h5 class="fw-bold">Manage Teachers</h5>
          <p>Create, update, or remove teacher accounts.</p>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
            Create Teacher
          </button>
        </div>
      </div>

      <!-- Manage Students -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-people-fill display-4 text-success mb-3"></i>
          <h5 class="fw-bold">Manage Students</h5>
          <p>View student lists and update student profiles.</p>
          <a href="manage_students.php" class="btn btn-success">Manage Students</a>
        </div>
      </div>

      <!-- System Settings -->
      <div class="col-lg-4 col-md-6">
        <div class="card card-custom p-4 text-center">
          <i class="bi bi-gear-fill display-4 text-warning mb-3"></i>
          <h5 class="fw-bold">System Settings</h5>
          <p>Control grade periods, announcements, and more.</p>
          <a href="settings.php" class="btn btn-warning text-white">Open Settings</a>
        </div>
      </div>

    </div>

    <!-- Recent Notices -->
    <div class="mt-5">
      <h4 class="section-title mb-3"><i class="bi bi-bell-fill me-2"></i>Admin Updates</h4>
      <div class="card card-custom p-4">
        <ul class="list-unstyled mb-0">
          <li class="mb-3"><i class="bi bi-circle-fill text-primary me-2"></i>Teacher registration module updated.</li>
          <li class="mb-3"><i class="bi bi-circle-fill text-success me-2"></i>System maintenance scheduled on Nov 20, 2025.</li>
          <li><i class="bi bi-circle-fill text-danger me-2"></i>Backup your database regularly to avoid data loss.</li>
        </ul>
      </div>
    </div>
</div>

<?php include 'footer_dashboard.php'; ?>

<!-- BOOTSTRAP JS -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- CREATE TEACHER MODAL -->
<div class="modal fade" id="createTeacherModal" tabindex="-1" aria-labelledby="createTeacherLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="createTeacherLabel">Create Teacher Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="process_create_teacher.php" method="POST">
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="first_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" class="form-control" name="middle_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Teacher Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
          </div>

          <input type="hidden" name="user_type" value="teacher">

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Teacher</button>
        </div>
      </form>

    </div>
  </div>
</div>

</body>
</html>
