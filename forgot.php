<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password | KLD Grade System</title>

  <!-- Bootstrap CSS -->
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

    /* Flex column layout so footer sticks to bottom */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0fbfc, #fefae0);
      color: var(--dark-color);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
    }

    /* Main content wrapper */
    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px;
    }

    .forgot-box {
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

    .forgot-box h2 {
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

    .btn-reset {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: #fff;
      border: none;
      border-radius: 30px;
      padding: 12px 0;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s;
    }

    .btn-reset:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px var(--accent-color);
    }

    .forgot-box a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color .3s;
    }

    .forgot-box a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }

    .alert {
      text-align: left;
      font-size: 0.9rem;
      margin-bottom: 20px;
      padding: 10px 15px;
      border-radius: 10px;
    }

    @media (max-width: 576px) {
      .forgot-box {
        padding: 40px 25px;
      }
    }

    /* Footer sticks to bottom */
    footer {
      margin-top: auto;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="main-content">
    <div class="forgot-box">
      <h2><i class="bi bi-key-fill"></i> Forgot Your Password?</h2>

      <!-- Placeholder for success message -->
      <div id="message" class="alert alert-info" style="display:none;"></div>

      <form id="forgotForm">
        <div class="mb-3 text-start">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input type="email" id="email" class="form-control" placeholder="Enter your registered email" required>
        </div>
        <button type="submit" class="btn-reset mt-2">Send Reset Link</button>
      </form>

      <div class="mt-4">
        <p>Remembered your password? <a href="login.php">Login here</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>

      <div class="terms-links mt-3">
        <p>By using this service, you agree to our 
          <a href="terms.php">Terms & Conditions</a>
        </p>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    const form = document.getElementById('forgotForm');
    const message = document.getElementById('message');

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value;

      // Show fake success message
      message.style.display = 'block';
      message.textContent = `If an account with ${email} exists, a password reset link has been sent.`;
      message.className = 'alert alert-info';

      // Clear the form
      form.reset();
    });
  </script>
</body>
</html>
