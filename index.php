<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kolehiyo ng Lungsod ng Dasmariñas Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        /* Minimalist overrides */
        .hero-section {
            background: linear-gradient(rgba(13, 59, 46, 0.8), rgba(13, 59, 46, 0.7)), url('assets/kld.png') center/cover fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .feature-icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--vds-sage);
            color: var(--vds-forest);
            border-radius: 16px;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .vds-card:hover .feature-icon-box {
            background: var(--vds-forest);
            color: white;
            transform: scale(1.1);
        }

        .team-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            border: 4px solid var(--vds-sage);
            transition: all 0.3s ease;
        }
        
        .vds-card:hover .team-img {
            border-color: var(--vds-forest);
        }
    </style>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">

    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="vds-container text-center">
            <div class="vds-glass p-5 mx-auto fade-in-up" style="max-width: 800px; border: 1px solid rgba(255,255,255,0.1);">
                <span class="vds-pill vds-pill-pass mb-3" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">Welcome to KLD Portal</span>
                <h1 class="vds-h1 mb-4" style="color: white; font-size: 3.5rem; letter-spacing: -1px;">Academic Excellence Digitized</h1>
                <p class="vds-text-lead mb-5" style="color: rgba(255,255,255,0.9);">
                    A streamlined, secure, and modern platform for managing grades and academic performance at Kolehiyo ng Lungsod ng Dasmariñas.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="login.php" class="vds-btn vds-btn-primary" style="padding: 16px 40px; font-size: 1.1rem;">Login to Portal</a>
                    <a href="register.php" class="vds-btn vds-btn-secondary" style="padding: 16px 40px; font-size: 1.1rem; border-color: rgba(255,255,255,0.4); color: white;">Create Account</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About / Features Section -->
    <section id="about" class="vds-section" style="background: white;">
        <div class="vds-container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <span class="vds-label" style="color: var(--vds-forest-light);">About The System</span>
                    <h2 class="vds-h2 mb-4" style="font-size: 2.5rem;">Efficiency Meets Transparency</h2>
                    <p class="vds-text-lead mb-4">
                        The KLD Grade System is designed to bridge the gap between faculty and students, ensuring accurate grade management and real-time academic monitoring.
                    </p>
                    <p class="vds-text-muted">
                        By digitizing the grading process, we minimize errors, save time for educators, and empower students with instant access to their performance metrics.
                    </p>
                </div>
                <div class="col-lg-7">
                    <div class="vds-grid-2">
                        <!-- Feature 1 -->
                        <div class="vds-card p-4 border-0 shadow-sm">
                            <div class="feature-icon-box">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <h4 class="vds-h4">Fast Processing</h4>
                            <p class="vds-text-muted small mb-0">Instant grade computation and report generation.</p>
                        </div>
                        <!-- Feature 2 -->
                        <div class="vds-card p-4 border-0 shadow-sm">
                            <div class="feature-icon-box">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h4 class="vds-h4">Secure Data</h4>
                            <p class="vds-text-muted small mb-0">End-to-end encryption for all student records.</p>
                        </div>
                        <!-- Feature 3 -->
                        <div class="vds-card p-4 border-0 shadow-sm">
                            <div class="feature-icon-box">
                                <i class="bi bi-phone-fill"></i>
                            </div>
                            <h4 class="vds-h4">Mobile First</h4>
                            <p class="vds-text-muted small mb-0">Access your dashboard from any device.</p>
                        </div>
                        <!-- Feature 4 -->
                        <div class="vds-card p-4 border-0 shadow-sm">
                            <div class="feature-icon-box">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <h4 class="vds-h4">Analytics</h4>
                            <p class="vds-text-muted small mb-0">Visual insights into academic performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="vds-section" style="background-color: var(--vds-vapor);">
        <div class="vds-container text-center">
            <span class="vds-label">The Minds Behind</span>
            <h2 class="vds-h2 mb-5">Meet Our Team</h2>
            
            <div class="row justify-content-center g-4">
                <?php
                $team = [
                    ['name' => 'Rogie Mar U. Ramos', 'role' => 'Front-End Developer', 'img' => 'assets/rogie.png', 'id' => 'rogie'],
                    ['name' => 'Arsyl F. Salva', 'role' => 'Back-End Developer', 'img' => 'assets/arsyl.png', 'id' => 'arsyl'],
                    ['name' => 'Jhon Messiah M. Romero', 'role' => 'System Analyst', 'img' => 'assets/jm.png', 'id' => 'jm'],
                    ['name' => 'Kevin L. Selibio', 'role' => 'Administrator', 'img' => 'assets/kevin.png', 'id' => 'kevin'],
                    ['name' => 'Renzo Nathaniel D. Ortega', 'role' => 'Process Manager', 'img' => 'assets/renzo.png', 'id' => 'renzo']
                ];

                foreach ($team as $member) {
                    echo '
                    <div class="col-md-4 col-lg-2">
                        <a href="team.php#'.$member['id'].'" class="text-decoration-none">
                            <div class="vds-card p-4 h-100 text-center border-0 shadow-sm hover-lift" style="background: white;">
                                <img src="'.$member['img'].'" alt="'.$member['name'].'" class="team-img">
                                <h5 class="vds-h4" style="font-size: 1rem; margin-bottom: 0.5rem;">'.$member['name'].'</h5>
                                <p class="vds-text-muted small mb-0">'.$member['role'].'</p>
                            </div>
                        </a>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="vds-section" style="background: white;">
        <div class="vds-container">
            <div class="text-center mb-5">
                <span class="vds-label">Support</span>
                <h2 class="vds-h2">Frequently Asked Questions</h2>
            </div>
            
            <div class="accordion accordion-flush mx-auto" id="faqAccordion" style="max-width: 800px;">
                <?php
                $faqs = [
                    "What is the purpose of this Grading System?" => "The system is designed to simplify grade computation for teachers and allow students to easily view their academic performance.",
                    "Who can use this system?" => "Teachers for grade encoding and students for viewing grades and progress.",
                    "How do teachers compute grades?" => "Teachers input scores for quizzes and exams, and the system automatically calculates final grades based on formulas.",
                    "Can students view grades per subject?" => "Yes, students can view detailed grade breakdowns for each enrolled subject.",
                    "Is the computation automatic?" => "Yes, raw scores are automatically converted to equivalent grades.",
                    "Can teachers edit grades?" => "Yes, before final submission. After submission, admin approval is required.",
                    "Is my data secure?" => "Yes, we use secure database storage and role-based access control.",
                    "Can I print my grades?" => "Yes, students can download PDF reports of their grades.",
                    "I forgot my password, what do I do?" => "Use the 'Forgot Password' link on the login page to reset it via email.",
                    "How do I contact support?" => "Use the contact form below or email the IT department."
                ];

                $i = 1;
                foreach ($faqs as $question => $answer) {
                    echo '
                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading'.$i.'">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$i.'" style="background: var(--vds-vapor); border-radius: 12px; font-weight: 600; color: var(--vds-forest); box-shadow: none;">
                                '.$question.'
                            </button>
                        </h2>
                        <div id="collapse'.$i.'" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="color: var(--vds-text-muted); padding: 1.5rem;">
                                '.$answer.'
                            </div>
                        </div>
                    </div>';
                    $i++;
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="vds-section" style="background-color: var(--vds-vapor);">
        <div class="vds-container">
            <div class="vds-glass p-5 mx-auto" style="max-width: 700px;">
                <div class="text-center mb-5">
                    <span class="vds-label">Get in Touch</span>
                    <h2 class="vds-h2">Contact Us</h2>
                    <p class="vds-text-lead">Have questions? We'd love to hear from you.</p>
                </div>
                
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="vds-form-group">
                                <label class="vds-label">Name</label>
                                <input type="text" class="vds-input" placeholder="Your Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="vds-form-group">
                                <label class="vds-label">Email</label>
                                <input type="email" class="vds-input" placeholder="Your Email" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="vds-form-group">
                                <label class="vds-label">Message</label>
                                <textarea class="vds-input" rows="4" placeholder="How can we help?" required></textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="vds-btn vds-btn-primary w-100">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'includes/legal_modals.php'; ?>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
