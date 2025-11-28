<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Grades - KLD Grade System</title>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(180deg, #e0fbfc, #fefae0);
      color: #03045e;
    }
    .grades-container { padding-top: 100px; padding-bottom: 50px; }
    .table-custom {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .table-custom th, .table-custom td {
      vertical-align: middle;
      text-align: center;
    }
    .section-title { font-weight: 600; color: #023047; margin-bottom: 30px; }
  </style>
</head>
<body>

  <?php include 'navbar_dashboard.php'; ?>

  <div class="container grades-container">
    <h2 class="section-title text-center mb-4"><i class="bi bi-journal-text me-2"></i>My Grades</h2>

    <div class="table-responsive">
      <table class="table table-striped table-hover table-custom">
        <thead class="table-primary">
          <tr>
            <th>Subject</th>
            <th>Semester</th>
            <th>Grade</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mathematics</td>
            <td>1st Semester</td>
            <td>A</td>
            <td>Excellent</td>
          </tr>
          <tr>
            <td>English</td>
            <td>1st Semester</td>
            <td>B+</td>
            <td>Good</td>
          </tr>
          <tr>
            <td>Science</td>
            <td>1st Semester</td>
            <td>A-</td>
            <td>Very Good</td>
          </tr>
          <tr>
            <td>History</td>
            <td>1st Semester</td>
            <td>B</td>
            <td>Good</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard</a>
    </div>
  </div>

  <?php include 'footer_dashboard.php'; ?>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
