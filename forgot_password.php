<?php
session_start();
require 'db_connect.php';
require 'PHPMailer-7.0.0/src/PHPMailer.php';
require 'PHPMailer-7.0.0/src/SMTP.php';
require 'PHPMailer-7.0.0/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendResetOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kevinselibio10@gmail.com'; 
        $mail->Password   = 'ruxmlcupgdicyywc';   
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom('kevinselibio10@gmail.com', 'KLD Grade System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Code';
        $mail->Body    = "Your password reset code is <b>$otp</b>. Expires in 10 minutes.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$step = $_GET['step'] ?? '1';
$error = '';
$success = '';

// Step 1: Request OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_reset'])) {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', time() + 600);
        
        $stmtOtp = $conn->prepare("INSERT INTO verification_codes (email, code, expires_at) VALUES (?, ?, ?)");
        $stmtOtp->bind_param("sss", $email, $otp, $expires_at);
        $stmtOtp->execute();
        
        $_SESSION['reset_email'] = $email;
        if (sendResetOTP($email, $otp)) {
            header("Location: forgot.php?step=2");
            exit();
        } else {
            $error = "Failed to send email.";
        }
    } else {
        $error = "Email not found.";
    }
}

// Step 2: Verify OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    $email = $_SESSION['reset_email'] ?? '';
    
    $current_time = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT * FROM verification_codes WHERE email = ? AND code = ? AND expires_at > ?");
    $stmt->bind_param("sss", $email, $otp, $current_time);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['reset_verified'] = true;
        header("Location: forgot.php?step=3");
        exit();
    } else {
        $error = "Invalid or expired OTP.";
    }
}

// Step 3: Reset Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    if (!isset($_SESSION['reset_verified']) || !$_SESSION['reset_verified']) {
        header("Location: forgot.php");
        exit();
    }
    
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];
    
    if ($pass === $confirm) {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);
        $stmt->execute();
        
        // Cleanup
        $conn->query("DELETE FROM verification_codes WHERE email = '$email'");
        session_destroy();
        
        echo "<script>alert('Password reset successful! Please login.'); window.location.href='login.php';</script>";
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
</head>
<body>
    <nav class="vds-navbar">
        <div class="vds-container vds-nav-content">
            <a href="index.php" class="vds-brand">
                <img src="assets/logo2.png" alt="Logo" height="40">
                KLD Portal
            </a>
            <div class="vds-nav-links">
                <a href="login.php" class="vds-btn vds-btn-secondary">Back to Login</a>
            </div>
        </div>
    </nav>

    <div class="vds-section vds-min-h-screen vds-flex-center">
        <div class="vds-glass" style="width: 100%; max-width: 500px; padding: 40px;">
            
            <?php if ($step == '1'): ?>
                <div class="text-center mb-4">
                    <h2 class="vds-h2">Forgot Password</h2>
                    <p class="vds-text-muted">Enter your email to receive a reset code.</p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="vds-form-group">
                        <label class="vds-label">Email Address</label>
                        <input type="email" name="email" class="vds-input" required>
                    </div>
                    <button type="submit" name="request_reset" class="vds-btn vds-btn-primary w-100">Send Code</button>
                </form>

            <?php elseif ($step == '2'): ?>
                <div class="text-center mb-4">
                    <h2 class="vds-h2">Verify Code</h2>
                    <p class="vds-text-muted">Enter the code sent to your email.</p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="vds-form-group">
                        <input type="text" name="otp" class="vds-input text-center" style="font-size: 1.5rem; letter-spacing: 5px;" placeholder="######" required>
                    </div>
                    <button type="submit" name="verify_otp" class="vds-btn vds-btn-primary w-100">Verify</button>
                </form>

            <?php elseif ($step == '3'): ?>
                <div class="text-center mb-4">
                    <h2 class="vds-h2">New Password</h2>
                    <p class="vds-text-muted">Create a new secure password.</p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="vds-form-group">
                        <label class="vds-label">New Password</label>
                        <input type="password" name="password" class="vds-input" required>
                    </div>
                    <div class="vds-form-group">
                        <label class="vds-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="vds-input" required>
                    </div>
                    <button type="submit" name="reset_password" class="vds-btn vds-btn-primary w-100">Reset Password</button>
                </form>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
