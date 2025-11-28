<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kolehiyo ng Lungsod ng Dasmariñas Grade System</title>

  <!-- Local Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Local Bootstrap Icons -->
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="styles.css">

  <style>
  


    
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  

  <!-- Carousel -->
  <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">

      <div class="carousel-item active">
        <img src="assets/gym.png" alt="Gym Image">
        <div class="carousel-caption">
          <h1>Empowering Education Digitally</h1>
          <p>Streamlined, transparent, and modern grade management system.</p>
        </div>
      </div>

      <div class="carousel-item">
        <img src="assets/kld.png" alt="KLD Image">
        <div class="carousel-caption">
          <h1>Designed for KLD Students</h1>
          <p>Efficiency and clarity in every report card.</p>
        </div>
      </div>

      <div class="carousel-item">
        <img src="assets/room.png" alt="Room Image">
        <div class="carousel-caption">
          <h1>Smart Tools for Smart Learners</h1>
          <p>Real-time academic insights and analytics.</p>
        </div>
      </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <!-- About -->
  <section id="about" class="text-center">
    <div class="container">
  <h2 class="fw-bold mb-4">About the Website</h2>
  <p class="lead">
    The KLD Grade System enhances how grades are managed, accessed, and monitored by both faculty and students.
    This system ensures data accuracy, transparency, and ease of use while providing analytics to improve academic outcomes.
  </p>

  <p class="lead">
    Designed with modern educational needs in mind, the platform streamlines grading tasks and minimizes manual errors,
    allowing teachers to focus more on instruction and student development. Students benefit from real-time access to their
    performance data, helping them stay informed and motivated throughout the academic year.
  </p>

  <p class="lead">
    The system also offers secure data handling, customizable reports, and an intuitive interface that supports smooth navigation
    for all users. By integrating technology with academic processes, the KLD Grade System aims to create a more efficient,
    transparent, and student-centered learning environment.
  </p>
</div>

  </section>

 <!-- Team -->
<section id="team">
  <div class="container text-center">
    <h2 class="fw-bold mb-5 text-white">Meet Our Team</h2>
    <div class="row justify-content-center g-4">
      <div class="col-md-4 col-lg-2 team-member">
        <a href="team.php#rogie" class="text-decoration-none text-white">
          <img src="assets/rogie.png" alt="Rogie Mar U. Ramos">
          <h5 class="mt-2">Rogie Mar U. Ramos</h5>
          <p>Front-End Developer</p>
        </a>
      </div>
      <div class="col-md-4 col-lg-2 team-member">
        <a href="team.php#arsyl" class="text-decoration-none text-white">
          <img src="assets/arsyl.png" alt="Arsyl F. Salva">
          <h5 class="mt-2">Arsyl F. Salva</h5>
          <p>Back-End Developer</p>
        </a>
      </div>
      <div class="col-md-4 col-lg-2 team-member">
        <a href="team.php#jm" class="text-decoration-none text-white">
          <img src="assets/jm.png" alt="Jhon Messiah M. Romero">
          <h5 class="mt-2">Jhon Messiah M. Romero</h5>
          <p>System Analyst</p>
        </a>
      </div>
      <div class="col-md-4 col-lg-2 team-member">
        <a href="team.php#kevin" class="text-decoration-none text-white">
          <img src="assets/kevin.png" alt="Kevin L. Selibio">
          <h5 class="mt-2">Kevin L. Selibio</h5>
          <p>Administrator</p>
        </a>
      </div>
      <div class="col-md-4 col-lg-2 team-member">
        <a href="team.php#renzo" class="text-decoration-none text-white">
          <img src="assets/renzo.png" alt="Renzo Nathaniel D. Ortega">
          <h5 class="mt-2">Renzo Nathaniel D. Ortega</h5>
          <p>Business Process Manager</p>
        </a>
      </div>
    </div>
  </div>
</section>


  <!-- FAQ -->
<section id="faq">
  <div class="container">
    <h2 class="fw-bold text-center mb-5">Frequently Asked Questions</h2>
    <div class="accordion" id="faqAccordion">

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
            1. What is the purpose of this Grading System?
          </button>
        </h2>
        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            The system is designed to simplify grade computation for teachers and allow students to easily view their academic performance in each subject.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
            2. Who can use this system?
          </button>
        </h2>
        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            The system can be used by teachers for grade encoding and computation, and by students to view their grades and academic progress.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
            3. How do teachers compute grades in the system?
          </button>
        </h2>
        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Teachers can log in, select a class, and input scores for quizzes, exams, and activities. The system automatically calculates final grades based on predefined formulas.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
            4. Can students view their grades for each subject?
          </button>
        </h2>
        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes. Students can log in to their accounts and view their grades by subject, including breakdowns for each grading period.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
            5. Is the grading computation automatic?
          </button>
        </h2>
        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes. Once teachers input raw scores, the system automatically computes equivalent grades using the school’s grading formula.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading6">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
            6. Can teachers edit grades after submission?
          </button>
        </h2>
        <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Teachers can edit grades before final submission. Once grades are finalized, changes require admin approval to ensure data accuracy.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading7">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7">
            7. Are student records secure?
          </button>
        </h2>
        <div id="collapse7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes. All student and teacher data are protected using secure database storage and role-based access control.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading8">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8">
            8. Can students print or download their grades?
          </button>
        </h2>
        <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes. Students can download or print their grade reports directly from the system in PDF format for record-keeping.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading9">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9">
            9. What happens if I forget my password?
          </button>
        </h2>
        <div id="collapse9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You can use the “Forgot Password” feature on the login page to reset your password using your registered email address.
          </div>
        </div>
      </div>

      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading10">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10">
            10. How can I contact support for help?
          </button>
        </h2>
        <div id="collapse10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You can reach out to the system administrator or school IT department through the “Contact” section in the website’s footer.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

      </div>
    </div>
  </section>
  <!-- Contact -->
  <section id="contact" class="text-center">
    <div class="container">
      <h2 class="fw-bold mb-4">Contact Us</h2>
      <p class="lead mb-5">Have questions or feedback? Reach out to us below.</p>
      <form class="row g-3 justify-content-center">
        <div class="col-md-5">
          <input type="text" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="col-md-5">
          <input type="email" class="form-control" placeholder="Your Email" required>
        </div>
        <div class="col-md-10">
          <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn-send mt-3">
            <i class="bi bi-send-fill me-2"></i>Send Message
          </button>
        </div>
      </form>
    </div>
  </section>


 

 
<?php include 'footer.php'; ?>
  <!-- Local Bootstrap JS -->
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
