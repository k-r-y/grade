<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Terms and Conditions | KLD Grade System</title>

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

    .terms-container {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px;
    }

    .terms-box {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(31,38,135,0.15);
      padding: 50px 40px;
      max-width: 900px;
      width: 100%;
      color: var(--dark-color);
    }

    .terms-box h2 {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 20px;
      text-align: center;
    }

    .terms-box h4 {
      color: var(--primary-color);
      margin-top: 30px;
      font-weight: 600;
    }

    .terms-box p, .terms-box ul {
      font-size: 1rem;
      line-height: 1.7;
      color: #333;
    }

    ul {
      list-style-type: disc;
      margin-left: 25px;
    }

    .btn-back {
      display: inline-block;
      margin-top: 30px;
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 10px 25px;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
    }

    .btn-back:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px var(--accent-color);
    }

    @media (max-width: 576px) {
      .terms-box {
        padding: 40px 25px;
      }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="terms-container">
    <div class="terms-box">
      <h2><i class="bi bi-file-earmark-text-fill"></i> Terms and Conditions</h2>
      <p>Last updated: November 2025</p>

      <p>Welcome to the <strong>Kolehiyo ng Lungsod ng Dasmariñas Grade System</strong>. By accessing and using this portal, you agree to comply with and be bound by the following Terms and Conditions. Please read them carefully before using the system.</p>

      <h4>1. Acceptance of Terms</h4>
      <p>By creating an account or using this website, you acknowledge that you have read, understood, and agreed to these Terms and Conditions. If you do not agree, please refrain from using the system.</p>

      <h4>2. User Responsibilities</h4>
      <ul>
        <li>You must provide accurate and truthful information during registration.</li>
        <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
        <li>You agree not to share your password or grant access to unauthorized users.</li>
        <li>Any misuse or tampering with the system may result in account suspension or disciplinary action by the school administration.</li>
      </ul>

      <h4>3. Authorized Use</h4>
      <p>This system is intended for legitimate academic purposes only, including the management and viewing of grades by students, faculty, and administrators. Any other use is strictly prohibited.</p>

      <h4>4. Data Accuracy</h4>
      <p>While we strive to ensure all information is correct and up-to-date, the system may occasionally contain data discrepancies. Users are encouraged to report any errors to their department head or system administrator for review.</p>

      <h4>5. Intellectual Property</h4>
      <p>All system content, design elements, and code are the property of the <strong>Kolehiyo ng Lungsod ng Dasmariñas</strong>. Unauthorized reproduction, modification, or distribution of materials is strictly prohibited.</p>

      <h4>6. Privacy and Security</h4>
      <p>Your privacy is important to us. All personal data is processed in accordance with our <a href="privacy-policy.php" class="text-decoration-none fw-semibold" style="color: var(--primary-color);">Privacy Policy</a>. The system employs security measures to safeguard stored information.</p>

      <h4>7. Limitation of Liability</h4>
      <p>The KLD Grade System is provided “as is.” The institution will not be held liable for any damages resulting from data loss, system downtime, or unauthorized access, unless caused by gross negligence.</p>

      <h4>8. Account Suspension</h4>
      <p>The institution reserves the right to suspend accounts that violate these terms or misuse the platform.</p>

      <h4>9. Amendments to Terms</h4>
      <p>We may modify or update these Terms and Conditions at any time. Continued use of the system after changes implies acceptance of the new terms.</p>

      <h4>10. Contact Information</h4>
      <p>For questions regarding these Terms and Conditions, please contact the KLD IT Department at <a href="mailto:support@kldgradesystem.edu.ph">support@kldgradesystem.edu.ph</a>.</p>

      <div class="text-center">
        <a href="index.php" class="btn-back"><i class="bi bi-arrow-left-circle me-2"></i>Back to Home</a>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <!-- Local Bootstrap JS -->
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
