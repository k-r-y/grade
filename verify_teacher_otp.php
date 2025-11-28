<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = trim($_POST['otp']);

    if (!isset($_SESSION['teacher_reg'])) {
        echo "<script>alert('No registration data found'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    $reg_data = $_SESSION['teacher_reg'];
    $email = $reg_data['email'];

    // Verify OTP from DB
    $stmt = $conn->prepare("SELECT id, otp, expires_at, used FROM otps WHERE email=? AND otp=? AND used=0 ORDER BY id DESC LIMIT 1");
    $otp_int = (int)$entered_otp;
    $stmt->bind_param("si", $email, $otp_int);
    $stmt->execute();
    $res = $stmt->get_result();
    $otp_row = $res->fetch_assoc();
    $stmt->close();

    if (!$otp_row) {
        echo "<script>alert('Incorrect or already-used OTP'); window.history.back();</script>";
        exit();
    }

    if (strtotime($otp_row['expires_at']) < time()) {
        unset($_SESSION['teacher_reg']);
        echo "<script>alert('OTP expired'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // Mark OTP as used
    $stmt_upd = $conn->prepare("UPDATE otps SET used=1 WHERE id=?");
    $stmt_upd->bind_param("i", $otp_row['id']);
    $stmt_upd->execute();
    $stmt_upd->close();

    // Insert teacher into users
    $stmt_user = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_user->bind_param("ssssss", $reg_data['first_name'], $reg_data['middle_name'], $reg_data['last_name'], $reg_data['email'], $reg_data['password'], $reg_data['user_type']);
    $stmt_user->execute();
    $stmt_user->close();

    unset($_SESSION['teacher_reg']);
    echo "<script>alert('Teacher account created successfully!'); window.location.href='admin_dashboard.php';</script>";
    exit();
}
?>

<!-- Simple OTP form -->
<form method="POST">
    <label>Enter OTP:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify OTP</button>
</form>
