<?php
session_start();
require 'db_connect.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get email and password from login form
    $email = trim($_POST['email']); // make sure your login form input is name="email"
    $password = $_POST['password'];

    // Prepare statement to fetch user by email
    $stmt = $conn->prepare("SELECT user_type, user_id, first_name, last_name, email, Password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['Password'])) {
            // Login successful, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['email'] = $user['email']; // optional
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect to dashboard
            // header("Location: dashboard.php");
            // exit();
            if ($user['user_type'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } 
            else if ($user['user_type'] === 'teacher') {
                header("Location: teacher_dashboard.php");
                exit();
            } 
            else if ($user['user_type'] === 'student') {
                header("Location: dashboard.php");
                exit();
            } 
            else {
                // ❌ Unexpected user_type
                $_SESSION['login_error'] = "User role is not recognized. Please contact the administrator.";
                header("Location: login.php");
                exit();
            }
        } else {
            // Wrong password
            $_SESSION['login_error'] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['login_error'] = "No account found with that email.";
        header("Location: login.php");
        exit();
    }

} else {
    // If not POST request, redirect to login
    header("Location: login.php");
    exit();
}
?>
