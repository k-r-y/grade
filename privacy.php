<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Privacy Policy | KLD Grade System</title>

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

    .policy-container {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px;
    }

    .policy-box {
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

    .policy-box h2 {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 20px;
      text-align: center;
    }

    .policy-box h4 {
      color: var(--primary-color);
      margin-top: 30px;
      font-weight: 600;
    }

    .policy-box p, .policy-box ul {
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
      .policy-box {
        padding: 40px 25px;
      }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="policy-container">
    <div class="policy-box">
      <h2><i class="bi bi-shield-lock-fill"></i> Privacy Policy</h2>
      <p>Last updated: November 2025</p>

      <p>At <strong>Kolehiyo ng Lungsod ng Dasmariñas Grade System</strong>, we value your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, and safeguard your information when you use our online grade management system.</p>

      <h4>1. Information We Collect</h4>
      <p>We collect the following types of information from users:</p>
      <ul>
        <li>Personal details such as name, and email address.</li>
        <li>Academic data including grades, courses, and subjects.</li>
        <li>Login credentials (securely encrypted).</li>
        <li>System usage logs for performance and security monitoring.</li>
      </ul>

      <h4>2. How We Use Your Information</h4>
      <p>Your data is used solely for the following purposes:</p>
      <ul>
        <li>To provide and maintain the grade management services.</li>
        <li>To ensure accuracy and transparency in academic reporting.</li>
        <li>To communicate updates or important notices regarding your account.</li>
        <li>To enhance system security and prevent unauthorized access.</li>
      </ul>

      <h4>3. Data Protection</h4>
      <p>We use strong security protocols, including SSL encryption and database access restrictions, to protect your personal and academic data. Only authorized personnel have access to sensitive information.</p>

      <h4>4. Sharing of Information</h4>
      <p>We do not share or sell your personal data. Information may only be disclosed:</p>
      <ul>
        <li>To authorized school administrators and teachers for academic purposes.</li>
        <li>If required by law or government regulations.</li>
      </ul>

      <h4>5. Session and System Logs</h4>
      <p>Our system may use session data to enhance user experience and maintain secure access. No tracking or advertising cookies are used.</p>

      <h4>6. Your Rights</h4>
      <p>You have the right to access, and correct your personal data. Requests can be made through the system administrator or school IT department.</p>

      <h4>7. Updates to This Policy</h4>
      <p>We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. The latest version will always be available on this page.</p>

      <h4>8. Contact Us</h4>
      <p>If you have questions or concerns about this Privacy Policy, please contact our IT Department at <a href="mailto:support@kldgradesystem.edu.ph">support@kldgradesystem.edu.ph</a>.</p>

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
