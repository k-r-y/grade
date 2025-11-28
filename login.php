<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | KLD Grade System</title>

  <!-- Local Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Local Bootstrap Icons -->
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

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0fbfc, #fefae0);
      color: var(--dark-color);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    .login-container {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(31,38,135,0.15);
      padding: 50px 40px;
      max-width: 450px;
      width: 100%;
      text-align: center;
    }

    .login-box h2 {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 30px;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px;
      border: 1px solid rgba(255,255,255,0.4);
      background: rgba(255,255,255,0.7);
      transition: all .3s;
    }

    .form-control:focus {
      box-shadow: 0 0 10px var(--accent-color);
      border-color: var(--secondary-color);
    }

    .btn-login {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: #fff;
      border: none;
      border-radius: 30px;
      padding: 12px 0;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s;
    }

    .btn-login:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px var(--accent-color);
    }

    .login-box a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color .3s;
    }

    .login-box a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }

    .terms-links {
      font-size: 0.9rem;
      margin-top: 15px;
      color: #333;
    }

    .terms-links a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
    }

    .terms-links a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }

    /* Error alert */
    .alert-danger {
      text-align: left;
      font-size: 0.9rem;
      margin-bottom: 20px;
      padding: 10px 15px;
      border-radius: 10px;
    }

    @media (max-width: 576px) {
      .login-box {
        padding: 40px 25px;
      }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="login-container">
    <div class="login-box">
      <h2><i class="bi bi-person-circle"></i> Login to Your Account</h2>

      <!-- Display login error if exists -->
      <?php if(isset($_SESSION['login_error'])): ?>
        <div class="alert alert-danger">
          <?php 
            echo $_SESSION['login_error']; 
            unset($_SESSION['login_error']); 
          ?>
        </div>
      <?php endif; ?>

      <form action="authenticate.php" method="POST">
        <div class="mb-3 text-start">
          <label for="username" class="form-label fw-semibold">Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3 text-start">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn-login mt-2">Login</button>
      </form>

      <div class="mt-4">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="forgot.php">Forgot Password?</a></p>
      </div>

      <div class="terms-links mt-3">
        <p>By logging in, you agree to our 
          <a href="terms.php">Terms & Conditions</a>
        </p>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
