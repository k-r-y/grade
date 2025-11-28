<?php
session_start();
require 'db_connect.php';
require 'PHPMailer-7.0.0/src/PHPMailer.php';
require 'PHPMailer-7.0.0/src/SMTP.php';
require 'PHPMailer-7.0.0/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Helper: Send OTP
function sendOTP($email, $otp) {
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
        $mail->Subject = 'KLD Verification Code';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #0D3B2E;'>Verification Code</h2>
                <p>Your OTP code is:</p>
                <h1 style='font-size: 32px; letter-spacing: 5px; color: #0D3B2E;'>$otp</h1>
                <p>This code will expire in 10 minutes.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// --- Session Persistence Logic ---
// Determine the current step based on session or default to 1
$session_step = $_SESSION['register_step'] ?? 1;
$request_step = $_GET['step'] ?? $session_step;

// Prevent jumping ahead of progress
if ($request_step > $session_step) {
    $request_step = $session_step;
}
$step = $request_step;

$error = '';
$success = '';

// --- STEP 1: Email & Password ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_step1'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!str_ends_with($email, '@kld.edu.ph')) {
        $error = "Registration is restricted to @kld.edu.ph emails only.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['is_verified'] == 1) {
                $error = "Email already registered. Please login.";
            } else {
                // Resend OTP logic for unverified user
                $otp = rand(100000, 999999);
                $expires_at = date('Y-m-d H:i:s', time() + 600);
                
                // Update password just in case they forgot it
                $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
                $stmtUpd = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                $stmtUpd->bind_param("ss", $hashed_pwd, $email);
                $stmtUpd->execute();

                // Insert new OTP
                $stmtOtp = $conn->prepare("INSERT INTO verification_codes (email, code, expires_at) VALUES (?, ?, ?)");
                $stmtOtp->bind_param("sss", $email, $otp, $expires_at);
                $stmtOtp->execute();

                $_SESSION['verify_email'] = $email;
                $_SESSION['register_step'] = 2; // Advance progress
                if (sendOTP($email, $otp)) {
                    header("Location: register.php?step=2");
                    exit();
                } else {
                    $error = "Failed to send OTP.";
                }
            }
        } else {
            // Create new unverified user
            $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $conn->prepare("INSERT INTO users (email, password_hash, is_verified, role) VALUES (?, ?, 0, 'student')");
            $stmtIns->bind_param("ss", $email, $hashed_pwd);
            
            if ($stmtIns->execute()) {
                // Generate OTP
                $otp = rand(100000, 999999);
                $expires_at = date('Y-m-d H:i:s', time() + 600);
                
                $stmtOtp = $conn->prepare("INSERT INTO verification_codes (email, code, expires_at) VALUES (?, ?, ?)");
                $stmtOtp->bind_param("sss", $email, $otp, $expires_at);
                $stmtOtp->execute();

                $_SESSION['verify_email'] = $email;
                $_SESSION['register_step'] = 2; // Advance progress
                if (sendOTP($email, $otp)) {
                    header("Location: register.php?step=2");
                    exit();
                } else {
                    $error = "Failed to send OTP.";
                }
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}

// --- STEP 2: Verify OTP ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);
    $email = $_SESSION['verify_email'] ?? '';

    if (empty($email)) {
        header("Location: register.php");
        exit();
    }

    // FIX: Use PHP time for comparison to avoid DB timezone mismatch
    $current_time = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT * FROM verification_codes WHERE email = ? AND code = ? AND expires_at > ?");
    $stmt->bind_param("sss", $email, $entered_otp, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark verified
        $stmtUpd = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmtUpd->bind_param("s", $email);
        $stmtUpd->execute();

        // Cleanup OTPs
        $conn->query("DELETE FROM verification_codes WHERE email = '$email'");
        
        $_SESSION['register_step'] = 3; // Advance progress
        header("Location: register.php?step=3");
        exit();
    } else {
        $error = "Invalid or expired OTP.";
    }
}

// --- Resend OTP Handler ---
if (isset($_POST['resend_otp'])) {
    $email = $_SESSION['verify_email'] ?? '';
    if ($email) {
        $otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', time() + 600);
        
        $stmtOtp = $conn->prepare("INSERT INTO verification_codes (email, code, expires_at) VALUES (?, ?, ?)");
        $stmtOtp->bind_param("sss", $email, $otp, $expires_at);
        $stmtOtp->execute();
        
        if (sendOTP($email, $otp)) {
            $success = "New code sent to your email.";
        } else {
            $error = "Failed to send new code.";
        }
    }
}

// --- STEP 3: Complete Profile ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_profile'])) {
    $email = $_SESSION['verify_email'] ?? '';
    if (empty($email)) {
        header("Location: login.php");
        exit();
    }

    $school_id = trim($_POST['school_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $role = $_POST['role']; // 'student' or 'teacher'
    $institute_id = intval($_POST['institute_id']);
    
    // Program is optional for teachers
    $program_id = isset($_POST['program_id']) && $_POST['program_id'] !== '' ? intval($_POST['program_id']) : null;
    
    $full_name = "$first_name $last_name"; 
    
    // Determine status
    $status = ($role === 'teacher') ? 'pending' : 'active';

    // Update User
    $stmt = $conn->prepare("UPDATE users SET school_id = ?, full_name = ?, program_id = ?, institute_id = ?, role = ?, status = ? WHERE email = ?");
    $stmt->bind_param("ssiisss", $school_id, $full_name, $program_id, $institute_id, $role, $status, $email);
    
    if ($stmt->execute()) {
        // Clear registration session data
        unset($_SESSION['verify_email']);
        unset($_SESSION['register_step']);

        if ($role === 'teacher') {
            // Redirect to Pending Approval Page
            header("Location: pending_approval.php");
            exit();
        } else {
            // Auto Login for Students
            $stmtUser = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
            $stmtUser->bind_param("s", $email);
            $stmtUser->execute();
            $user = $stmtUser->get_result()->fetch_assoc();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $full_name;
            
            header("Location: student_dashboard.php");
            exit();
        }
    } else {
        $error = "Failed to update profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
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
                <a href="index.php" class="vds-nav-link">Home</a>
                <a href="login.php" class="vds-btn vds-btn-secondary">Login</a>
            </div>
        </div>
    </nav>

    <div class="vds-section vds-min-h-screen vds-flex-center">
        <div class="vds-glass" style="width: 100%; max-width: 500px; padding: 40px;">
            
            <?php if ($step == '1'): ?>
                <!-- STEP 1: Create Account -->
                <div class="text-center mb-4">
                    <h2 class="vds-h2">Create Account</h2>
                    <p class="vds-text-muted">Step 1 of 3: Account Credentials</p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>
                
                <form method="POST">
                    <div class="vds-form-group">
                        <label class="vds-label">KLD Email</label>
                        <input type="email" name="email" class="vds-input" placeholder="student@kld.edu.ph" required>
                    </div>
                    <div class="vds-form-group">
                        <label class="vds-label">Password</label>
                        <input type="password" name="password" class="vds-input" required>
                    </div>
                    <div class="vds-form-group">
                        <label class="vds-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="vds-input" required>
                    </div>
                    <button type="submit" name="register_step1" class="vds-btn vds-btn-primary w-100">Next: Verify Email</button>
                </form>

            <?php elseif ($step == '2'): ?>
                <!-- STEP 2: Verify OTP -->
                <div class="text-center mb-4">
                    <h2 class="vds-h2">Verify Email</h2>
                    <p class="vds-text-muted">Step 2 of 3: Enter the code sent to <strong><?php echo htmlspecialchars($_SESSION['verify_email']); ?></strong></p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>
                <?php if($success): ?><div class="vds-pill vds-pill-pass mb-4 w-100 justify-content-center"><?php echo $success; ?></div><?php endif; ?>

                <form method="POST">
                    <div class="vds-form-group">
                        <input type="text" name="otp" class="vds-input text-center" style="font-size: 1.5rem; letter-spacing: 5px;" placeholder="######" maxlength="6" required>
                    </div>
                    <button type="submit" name="verify_otp" class="vds-btn vds-btn-primary w-100 mb-3">Verify Code</button>
                </form>
                
                <form method="POST" class="text-center">
                    <button type="submit" name="resend_otp" class="vds-btn vds-btn-secondary btn-sm" style="font-size: 0.8rem; padding: 6px 16px;">Resend Code</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="register.php?step=1" class="vds-text-muted small">Wrong email? Start over</a>
                </div>

            <?php elseif ($step == '3'): ?>
                <!-- STEP 3: Complete Profile -->
                <div class="text-center mb-4">
                    <h2 class="vds-h2">Complete Profile</h2>
                    <p class="vds-text-muted">Step 3 of 3: Personal Information</p>
                </div>
                <?php if($error): ?><div class="vds-pill vds-pill-fail mb-4 w-100 justify-content-center"><?php echo $error; ?></div><?php endif; ?>

                <form method="POST">
                    <div class="vds-form-group">
                        <label class="vds-label">Role</label>
                        <select name="role" id="roleSelect" class="vds-select" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>

                    <div class="vds-form-group">
                        <label class="vds-label">ID Number</label>
                        <input type="text" name="school_id" class="vds-input" placeholder="KLD-2024-XXXX" required>
                    </div>
                    <div class="vds-grid-2">
                        <div class="vds-form-group">
                            <label class="vds-label">First Name</label>
                            <input type="text" name="first_name" class="vds-input" required>
                        </div>
                        <div class="vds-form-group">
                            <label class="vds-label">Last Name</label>
                            <input type="text" name="last_name" class="vds-input" required>
                        </div>
                    </div>
                    
                    <div class="vds-form-group">
                        <label class="vds-label">Institute</label>
                        <select id="instituteSelect" name="institute_id" class="vds-select" required>
                            <option value="">Select Institute</option>
                        </select>
                    </div>
                    
                    <!-- Program is only for Students -->
                    <div class="vds-form-group" id="programGroup">
                        <label class="vds-label">Program</label>
                        <select id="programSelect" name="program_id" class="vds-select">
                            <option value="">Select Program</option>
                        </select>
                    </div>

                    <button type="submit" name="complete_profile" class="vds-btn vds-btn-primary w-100">Finish Registration</button>
                </form>

            <?php endif; ?>

        </div>
    </div>

    <script>
        // API Integration for Dropdowns (Only needed for Step 3)
        if (document.getElementById('instituteSelect')) {
            const instituteSelect = document.getElementById('instituteSelect');
            const programSelect = document.getElementById('programSelect');
            const roleSelect = document.getElementById('roleSelect');
            const programGroup = document.getElementById('programGroup');

            // Toggle Program field based on Role
            roleSelect.addEventListener('change', function() {
                if (this.value === 'teacher') {
                    programGroup.style.display = 'none';
                    programSelect.required = false;
                } else {
                    programGroup.style.display = 'block';
                    programSelect.required = true;
                }
            });

            fetch('api.php?action=get_institutes')
                .then(response => response.json())
                .then(data => {
                    data.forEach(inst => {
                        const option = document.createElement('option');
                        option.value = inst.id;
                        option.textContent = inst.code + ' - ' + inst.name;
                        instituteSelect.appendChild(option);
                    });
                });

            instituteSelect.addEventListener('change', function() {
                const instId = this.value;
                programSelect.innerHTML = '<option value="">Select Program</option>';
                
                // Only fetch programs if role is student
                if (roleSelect.value === 'student' && instId) {
                    fetch(`api.php?action=get_programs&institute_id=${instId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(prog => {
                                const option = document.createElement('option');
                                option.value = prog.id;
                                option.textContent = prog.code + ' - ' + prog.name;
                                programSelect.appendChild(option);
                            });
                        });
                }
            });
        }
    </script>

</body>
</html>
