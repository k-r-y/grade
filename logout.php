<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logged Out | KLD Grade System</title>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0fbfc, #fefae0);
      color: #03045e;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .logout-box {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(31,38,135,0.15);
      padding: 50px 40px;
      text-align: center;
      max-width: 450px;
      width: 100%;
    }

    .logout-box h2 {
      color: #0077b6;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .logout-box p {
      margin-bottom: 30px;
      font-size: 1.1rem;
    }

    .btn-custom {
      margin: 0 10px;
      border-radius: 30px;
      padding: 12px 25px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-home {
      background: #0077b6;
      color: #fff;
      border: none;
    }

    .btn-home:hover {
      background: #005f8f;
    }

    .btn-login {
      background: #48cae4;
      color: #fff;
      border: none;
    }

    .btn-login:hover {
      background: #2ea3c4;
    }
  </style>
</head>
<body>

  <div class="logout-box">
    <h2><i class="bi bi-check-circle-fill"></i> Successfully Logged Out!</h2>
    <p>You have been safely logged out of your account.</p>
    <div>
      <a href="index.php" class="btn btn-home btn-custom">Go to Home</a>
      <a href="login.php" class="btn btn-login btn-custom">Go to Login</a>
    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
