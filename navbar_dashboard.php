<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="vds-navbar">
    <div class="vds-container vds-nav-content">
        <a href="<?php echo ($_SESSION['role'] === 'teacher') ? 'teacher_dashboard.php' : 'dashboard.php'; ?>" class="vds-brand">
            <img src="assets/logo2.png" alt="Logo" height="40">
            KLD Portal
        </a>
        <div class="vds-nav-links">
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
                <a href="teacher_dashboard.php" class="vds-nav-link">Dashboard</a>
                <a href="manage_grades.php" class="vds-nav-link">Grades</a>
            <?php else: ?>
                <a href="dashboard.php" class="vds-nav-link">Dashboard</a>
                <a href="grades.php" class="vds-nav-link">My Grades</a>
            <?php endif; ?>
            
            <a href="profile.php" class="vds-nav-link">Profile</a>
            <a href="logout.php" class="vds-btn vds-btn-secondary" style="padding: 8px 20px;">Logout</a>
        </div>
    </div>
</nav>
