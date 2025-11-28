<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | KLD Grade System</title>
    <link rel="icon" type="image/png" href="assets/logo2.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, var(--vds-forest), #0f4c3a);
            height: 200px;
            border-radius: 24px;
            position: relative;
            margin-bottom: 80px;
        }

        .profile-avatar-container {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 6px solid white;
            background: white;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="profile-header fade-in-up">
                    <div class="profile-avatar-container">
                        <img src="assets/logo2.png" alt="Profile" class="profile-avatar">
                        <h2 class="vds-h3 mt-3 mb-0"><?php echo htmlspecialchars($first_name); ?></h2>
                        <span class="vds-pill vds-pill-pass mt-2">Active Student</span>
                    </div>
                </div>

                <div class="vds-glass p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="vds-h3 mb-0">Personal Information</h3>
                        <button class="vds-btn vds-btn-secondary" disabled>
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </button>
                    </div>

                    <form>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">First Name</label>
                                    <input type="text" class="vds-input" value="<?php echo htmlspecialchars($first_name); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">Last Name</label>
                                    <input type="text" class="vds-input" value="Placeholder" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="vds-form-group">
                                    <label class="vds-label">Email Address</label>
                                    <input type="email" class="vds-input" value="<?php echo htmlspecialchars($email); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">Phone Number</label>
                                    <input type="tel" class="vds-input" value="+63 900 000 0000" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="vds-form-group">
                                    <label class="vds-label">Student ID</label>
                                    <input type="text" class="vds-input" value="2023-00001" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="vds-form-group">
                                    <label class="vds-label">Address</label>
                                    <input type="text" class="vds-input" value="Dasmariñas City, Cavite" readonly>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-5 pt-4 border-top">
                        <h4 class="vds-h4 mb-3 text-danger">Danger Zone</h4>
                        <div class="d-flex justify-content-between align-items-center p-3" style="background: #fee2e2; border-radius: 12px;">
                            <div>
                                <h5 class="mb-1 text-danger" style="font-size: 1rem; font-weight: 600;">Delete Account</h5>
                                <p class="mb-0 small text-muted">Once you delete your account, there is no going back.</p>
                            </div>
                            <button class="btn btn-danger btn-sm rounded-pill px-4">Delete</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
