<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body class="vds-flex-center vds-min-h-screen" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">

    <div class="vds-glass text-center" style="max-width: 500px; padding: 40px;">
        <div style="font-size: 4rem; color: var(--vds-yellow); margin-bottom: 20px;">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <h1 class="vds-h2">Account Pending Approval</h1>
        <p class="vds-text-muted mb-4">
            Thank you for registering! Your teacher account is currently under review by the Institute Head. 
            You will be notified once your account has been approved.
        </p>
        <a href="index.php" class="vds-btn vds-btn-primary">Return to Home</a>
    </div>

</body>
</html>
