<!-- footer_dashboard.php -->
<footer class="footer mt-5 py-4">
  <div class="container text-center">
    <div class="footer-content">

      <!-- System Name -->
      <p class="fw-semibold mb-1">
        <i class="bi bi-mortarboard-fill text-primary me-2"></i>
        KLD Grade System
      </p>
      <p class="small mb-3">
        Empowering students and educators through digital academic management.
      </p>

      <!-- Navigation Links -->
      <div class="footer-nav mb-3">
        <a href="dashboard.php" class="footer-link me-3">Dashboard</a>
        <a href="grades.php" class="footer-link me-3">Grades</a>
        <a href="profile.php" class="footer-link me-3">Profile</a>
        <a href="announcements.php" class="footer-link me-3">Announcements</a>
        <a href="privacy-logged.php" class="footer-link">Privacy</a>
      </div>

      <!-- Footer Bottom -->
      <p class="small mb-0 text-muted">
        &copy; <?php echo date('Y'); ?> KLD Grade System. All rights reserved.
      </p>
    </div>
  </div>
</footer>

<style>
  .footer {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    color: #03045e;
  }
  .footer p {
    margin-bottom: 0;
  }
  .footer-nav {
    margin-bottom: 10px;
  }
  .footer-link {
    color: #0077b6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
  }
  .footer-link:hover {
    color: #023e8a;
    text-decoration: underline;
  }
</style>
