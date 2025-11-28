<?php
// Make sure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <img src="assets/logo.png" alt="Logo" width="40" height="40" class="me-2">
      Grade Portal
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDashboard">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarDashboard">
      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link" href="grades.php">Grades</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="announcements.php">Announcements</a>
        </li>
         <li class="nav-item">
         <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
           </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
