<?php
include 'navbar_dashboard.php';
include 'db.php'; // your DB connection

// Fetch students
$students = $conn->query("SELECT id, first_name, last_name FROM students");

// Fetch subjects
$subjects = $conn->query("SELECT id, subject_name FROM subjects");
?>

<div class="container mt-5">
  <h3 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Submit Student Grade</h3>

  <div class="card p-4">
    <form action="submit_grade_process.php" method="POST">

      <!-- Select Student -->
      <div class="mb-3">
        <label class="form-label fw-bold">Student</label>
        <select name="student_id" class="form-select" required>
          <option value="" disabled selected>Select Student</option>
          <?php while($s = $students->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>">
              <?= $s['first_name'] . ' ' . $s['last_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Select Subject -->
      <div class="mb-3">
        <label class="form-label fw-bold">Subject</label>
        <select name="subject_id" class="form-select" required>
          <option value="" disabled selected>Select Subject</option>
          <?php while($sub = $subjects->fetch_assoc()): ?>
            <option value="<?= $sub['id'] ?>">
              <?= $sub['subject_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Grade -->
      <div class="mb-3">
        <label class="form-label fw-bold">Grade</label>
        <input type="text" name="grade" class="form-control" required>
      </div>

      <!-- Remarks -->
      <div class="mb-3">
        <label class="form-label fw-bold">Remarks (Optional)</label>
        <textarea name="remarks" class="form-control"></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100">Submit Grade</button>

    </form>
  </div>
</div>

<?php include 'footer_dashboard.php'; ?>
