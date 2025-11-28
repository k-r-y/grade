<?php
session_start();
require 'db_connect.php'; // your database connection
require 'PHPMailer-7.0.0/src/PHPMailer.php';
require 'PHPMailer-7.0.0/src/SMTP.php';
require 'PHPMailer-7.0.0/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kevinselibio10@gmail.com'; // replace with your email
        $mail->Password   = 'ruxmlcupgdicyywc';   // replace with Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('kevinselibio10@gmail.com', 'KLD Grade System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your KLD OTP Code';
        $mail->Body    = "Your OTP code is <b>$otp</b>. It will expire in 10 minutes.";

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<script>alert('Failed to send OTP: " . addslashes($mail->ErrorInfo) . "');</script>";
        return false;
    }
}

// Handle initial registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $confirm     = $_POST['confirm_password'];
    $user_type   = 'student'; // default user type

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already registered!');</script>";
        } else {
            $otp = rand(100000, 999999);

            $_SESSION['reg_data'] = [
                'first_name' => $first_name,
                'middle_name'=> $middle_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'otp'        => $otp,
                'user_type'  => $user_type,
              'otp_time'   => time()
            ];

            // store OTP in database (table `otps`) with 10 minute expiry
            $created_at = date('Y-m-d H:i:s', time());
            $expires_at = date('Y-m-d H:i:s', time() + 600); // 10 minutes
            $stmt_otp = $conn->prepare("INSERT INTO otps (email, otp, created_at, expires_at, used) VALUES (?, ?, ?, ?, 0)");
            if ($stmt_otp) {
              $stmt_otp->bind_param("siss", $email, $otp, $created_at, $expires_at);
              $stmt_otp->execute();
              $stmt_otp->close();
            }

            if (sendOTP($email, $otp)) {
                echo "<script>alert('OTP sent to your email'); window.location.href='register.php?step=otp';</script>";
                exit();
            } else {
                echo "<script>alert('Failed to send OTP. Check email settings.');</script>";
            }
        }

        $check->close();
    }
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    if (!isset($_SESSION['reg_data'])) {
        echo "<script>alert('No registration data found.'); window.location.href='register.php';</script>";
        exit();
    }

  $entered_otp = trim($_POST['otp']);
  $reg_data = $_SESSION['reg_data'];
  $email = $reg_data['email'];

  // Check the latest unused OTP for this email in the DB
  $entered_otp_int = (int)$entered_otp;
  $stmt_check = $conn->prepare("SELECT id, otp, expires_at, used FROM otps WHERE email = ? AND otp = ? AND used = 0 ORDER BY id DESC LIMIT 1");
  if ($stmt_check) {
    $stmt_check->bind_param("si", $email, $entered_otp_int);
    $stmt_check->execute();
    $res = $stmt_check->get_result();
    $otp_row = $res->fetch_assoc();
    $stmt_check->close();

    if (!$otp_row) {
      echo "<script>alert('Incorrect or already-used OTP.');</script>";
      exit();
    }

    if (strtotime($otp_row['expires_at']) < time()) {
      echo "<script>alert('OTP expired. Please register again.'); window.location.href='register.php';</script>";
      unset($_SESSION['reg_data']);
      exit();
    }

    // mark OTP as used
    $stmt_upd = $conn->prepare("UPDATE otps SET used = 1 WHERE id = ?");
    if ($stmt_upd) {
      $stmt_upd->bind_param("i", $otp_row['id']);
      $stmt_upd->execute();
      $stmt_upd->close();
    }

    // insert user record
    $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $reg_data['first_name'], $reg_data['middle_name'], $reg_data['last_name'], $reg_data['email'], $reg_data['password'], $reg_data['user_type']);
    $stmt->execute();

    unset($_SESSION['reg_data']);
    echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";
    exit();
  } else {
    echo "<script>alert('Server error verifying OTP. Please try again later.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | KLD Grade System</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
  :root {
    --primary-color: #0077b6;
    --secondary-color: #48cae4;
    --accent-color: #ade8f4;
    --dark-color: #03045e;
    --bg-color: #caf0f8;
  }
  body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e0fbfc, #fefae0); min-height: 100vh; display:flex; flex-direction: column; }
  .register-container { flex-grow:1; display:flex; align-items:center; justify-content:center; padding:80px 20px; }
  .register-box { background: rgba(255,255,255,0.25); backdrop-filter: blur(15px); border-radius:20px; box-shadow:0 8px 32px rgba(31,38,135,0.15); padding:50px 40px; max-width:500px; width:100%; text-align:center; }
  .register-box h2 { font-weight:700; color: var(--dark-color); margin-bottom:30px; }
  .form-control { border-radius:12px; padding:12px; border:1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.7); transition:all .3s; }
  .form-control:focus { box-shadow:0 0 10px var(--accent-color); border-color: var(--secondary-color); }
  .btn-register { background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); color:#fff; border:none; border-radius:30px; padding:12px 0; font-weight:600; width:100%; transition:all 0.3s; }
  .btn-register:hover { transform: scale(1.05); box-shadow:0 0 15px var(--accent-color); }
  .error-text { color:red; font-size:0.9rem; text-align:left; margin-top:5px; display:none; }
  .terms-checkbox { font-size:0.9rem; text-align:left; margin-top:10px; }
  .terms-checkbox input { margin-right:8px; }
  .terms-checkbox a { color: var(--primary-color); font-weight:500; }
  .terms-checkbox a:hover { color: var(--secondary-color); text-decoration:underline; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="register-container">
  <div class="register-box">
    <?php if(isset($_GET['step']) && $_GET['step'] === 'otp' && isset($_SESSION['reg_data'])): ?>
      <h2><i class="bi bi-shield-lock-fill"></i> Verify OTP</h2>
      <form method="post">
        <div class="mb-3 text-start">
          <label for="otp" class="form-label fw-semibold">Enter OTP</label>
          <input type="text" id="otp" name="otp" class="form-control" required>
        </div>
        <button type="submit" name="verify_otp" class="btn-register mt-3">Verify OTP</button>
      </form>
    <?php else: ?>
      <h2><i class="bi bi-person-plus-fill"></i> Create an Account</h2>
      <form method="post" id="registerForm">
        <div class="mb-3 text-start">
          <label for="first_name" class="form-label fw-semibold">First Name</label>
          <input type="text" id="first_name" name="first_name" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
          <input type="text" id="middle_name" name="middle_name" class="form-control">
        </div>
        <div class="mb-3 text-start">
          <label for="last_name" class="form-label fw-semibold">Last Name</label>
          <input type="text" id="last_name" name="last_name" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
          <div id="passwordError" class="error-text">Passwords do not match.</div>
        </div>
        <div class="terms-checkbox">
          <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
          <label for="agreeTerms">I agree to the <a href="terms.php">Terms & Conditions</a> and <a href="privacy.php">Privacy Policy</a>.</label>
        </div>
        <button type="submit" name="register" class="btn-register mt-3">Register</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('registerForm')?.addEventListener('submit', function(event) {
  const password = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  if(password !== confirm){
    document.getElementById('passwordError').style.display='block';
    event.preventDefault();
  }
});
</script>

</body>
</html>
