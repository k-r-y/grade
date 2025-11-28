<?php
session_start();
require 'db_connect.php';
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
        $mail->Username   = 'kevinselibio10@gmail.com'; // Replace with your Gmail
        $mail->Password   = 'ruxmlcupgdicyywc'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('kevinselibio10@gmail.com', 'Grade System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code for Teacher Account';
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

// Handle teacher registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $confirm     = $_POST['confirm_password'];
    $user_type   = 'teacher';

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit();
    }

    $otp = rand(100000, 999999);

    // Store registration data in session
    $_SESSION['teacher_reg'] = [
        'first_name' => $first_name,
        'middle_name'=> $middle_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'password'   => password_hash($password, PASSWORD_DEFAULT),
        'otp'        => $otp,
        'user_type'  => $user_type,
        'otp_time'   => time()
    ];

    // Insert OTP into DB
    $created_at = date('Y-m-d H:i:s');
    $expires_at = date('Y-m-d H:i:s', time() + 600); // 10 minutes
    $stmt_otp = $conn->prepare("INSERT INTO otps (email, otp, created_at, expires_at, used) VALUES (?, ?, ?, ?, 0)");
    $stmt_otp->bind_param("siss", $email, $otp, $created_at, $expires_at);
    $stmt_otp->execute();
    $stmt_otp->close();

    if (sendOTP($email, $otp)) {
        echo "<script>alert('OTP sent to teacher email'); window.location.href='verify_teacher_otp.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to send OTP. Check email settings.'); window.history.back();</script>";
        exit();
    }
}
?>
