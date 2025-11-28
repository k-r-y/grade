<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meet the Team | KLD Grade System</title>
    <link rel="icon" type="image/png" href="assets/kld.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="devicon-master/devicon.min.css">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <style>
        .profile-selector {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .profile-card-item {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .profile-card-item:hover, .profile-card-item.active {
            background: rgba(255, 255, 255, 0.8);
            border-color: var(--vds-forest-light);
            transform: translateX(5px);
        }

        .profile-card-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--vds-sage);
        }

        .profile-details-container {
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-photo-large {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 6px solid var(--vds-sage);
            object-fit: cover;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .social-link {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--vds-vapor);
            color: var(--vds-forest);
            margin: 0 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--vds-forest);
            color: white;
            transform: translateY(-3px);
        }

        .tech-icon {
            font-size: 2.5rem;
            margin: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .tech-icon:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body class="vds-bg-vapor" style="min-height: 100vh; display: flex; flex-direction: column;">

    <?php include('navbar.php'); ?>

    <section class="vds-section flex-grow-1">
        <div class="vds-container">
            <div class="text-center mb-5">
                <span class="vds-label">The Minds Behind</span>
                <h2 class="vds-h2">Meet Our Team</h2>
            </div>

            <div class="row g-4">
                <!-- Left: Member list -->
                <div class="col-lg-4">
                    <div class="profile-selector">
                        <div class="vds-card p-3 d-flex align-items-center gap-3 profile-card-item" onclick="showProfile('rogie')" id="card-rogie">
                            <img src="assets/rogie.png" alt="Rogie Ramos">
                            <div>
                                <h5 class="vds-h4 mb-0" style="font-size: 1rem;">Rogie Mar U. Ramos</h5>
                                <small class="vds-text-muted">Front-End Developer</small>
                            </div>
                        </div>
                        <div class="vds-card p-3 d-flex align-items-center gap-3 profile-card-item" onclick="showProfile('arsyl')" id="card-arsyl">
                            <img src="assets/arsyl.png" alt="Arsyl Salva">
                            <div>
                                <h5 class="vds-h4 mb-0" style="font-size: 1rem;">Arsyl F. Salva</h5>
                                <small class="vds-text-muted">Back-End Developer</small>
                            </div>
                        </div>
                        <div class="vds-card p-3 d-flex align-items-center gap-3 profile-card-item" onclick="showProfile('jm')" id="card-jm">
                            <img src="assets/jm.png" alt="Jhon Messiah Romero">
                            <div>
                                <h5 class="vds-h4 mb-0" style="font-size: 1rem;">Jhon Messiah M. Romero</h5>
                                <small class="vds-text-muted">System Analyst</small>
                            </div>
                        </div>
                        <div class="vds-card p-3 d-flex align-items-center gap-3 profile-card-item" onclick="showProfile('kevin')" id="card-kevin">
                            <img src="assets/kevin.png" alt="Kevin Selibio">
                            <div>
                                <h5 class="vds-h4 mb-0" style="font-size: 1rem;">Kevin L. Selibio</h5>
                                <small class="vds-text-muted">Administrator</small>
                            </div>
                        </div>
                        <div class="vds-card p-3 d-flex align-items-center gap-3 profile-card-item" onclick="showProfile('renzo')" id="card-renzo">
                            <img src="assets/renzo.png" alt="Renzo Ortega">
                            <div>
                                <h5 class="vds-h4 mb-0" style="font-size: 1rem;">Renzo Nathaniel D. Ortega</h5>
                                <small class="vds-text-muted">Process Manager</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Profile details -->
                <div class="col-lg-8">
                    <div class="vds-glass p-5 h-100 profile-details-container" style="background: rgba(255,255,255,0.8);">
                        <div id="profile-details" class="text-center w-100">
                            <div class="text-muted py-5">
                                <i class="bi bi-people" style="font-size: 4rem; opacity: 0.2;"></i>
                                <p class="mt-3">Select a team member to view their profile.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

    <script>
        const members = {
            rogie: {
                name: "Rogie Mar U. Ramos",
                role: "Front-End Developer",
                photo: "assets/rogie.png",
                bio: "Leads the front-end development of the KLD Grade System, ensuring a clean, responsive, and user-friendly interface.",
                email: "rogie.ramos@kld.edu.ph",
                facebook: "#",
                github: "#",
                skills: ["Front-End Development", "UI Design", "Responsive Web Design"],
                techstack: ["HTML", "CSS", "Bootstrap", "JavaScript"]
            },
            arsyl: {
                name: "Arsyl F. Salva",
                role: "Back-End Developer",
                photo: "assets/arsyl.png",
                bio: "Handles database integration, data processing, and server-side logic for seamless functionality.",
                email: "arsyl.salva@kld.edu.ph",
                facebook: "#",
                github: "#",
                skills: ["PHP Development", "Database Management", "API Integration"],
                techstack: ["PHP", "MySQL", "JavaScript", "Bootstrap"]
            },
            jm: {
                name: "Jhon Messiah M. Romero",
                role: "System Analyst",
                photo: "assets/jm.png",
                bio: "Analyzes and optimizes the overall system structure to ensure efficiency and logical workflow.",
                email: "jhon.romero@kld.edu.ph",
                facebook: "#",
                github: "#",
                skills: ["System Analysis", "Documentation", "Logical Design"],
                techstack: ["HTML", "CSS", "Bootstrap", "MySQL"]
            },
            kevin: {
                name: "Kevin L. Selibio",
                role: "Administrator",
                photo: "assets/kevin.png",
                bio: "Oversees the deployment and management of the KLD Grade System with a focus on stability and security.",
                email: "kevin.selibio@kld.edu.ph",
                facebook: "#",
                github: "#",
                skills: ["Project Management", "System Administration", "Security Management"],
                techstack: ["PHP", "MySQL", "Bootstrap"]
            },
            renzo: {
                name: "Renzo Nathaniel D. Ortega",
                role: "Business Process Manager",
                photo: "assets/renzo.png",
                bio: "Responsible for aligning system functionality with institutional processes to ensure business efficiency.",
                email: "renzo.ortega@kld.edu.ph",
                facebook: "#",
                github: "#",
                skills: ["Process Optimization", "Workflow Management", "Data Analysis"],
                techstack: ["HTML", "CSS", "Bootstrap", "MySQL"]
            }
        };

        const techIconMap = {
            "HTML": "devicon-html5-plain colored",
            "CSS": "devicon-css3-plain colored",
            "JavaScript": "devicon-javascript-plain colored",
            "PHP": "devicon-php-plain colored",
            "MySQL": "devicon-mysql-plain colored",
            "Bootstrap": "devicon-bootstrap-plain colored"
        };

        function showProfile(id) {
            const m = members[id];
            if (!m) return;
            
            // Update active state
            document.querySelectorAll('.profile-card-item').forEach(el => el.classList.remove('active'));
            document.getElementById('card-' + id).classList.add('active');

            const details = document.getElementById("profile-details");

            const skillsHtml = m.skills.map(s => `<span class='vds-pill vds-pill-pass m-1'>${s}</span>`).join("");
            const techHtml = m.techstack.map(t => `<i class='${techIconMap[t]} tech-icon' title='${t}'></i>`).join("");

            const gmailLink = `https://mail.google.com/mail/?view=cm&to=${m.email}&su=KLD%20Grade%20System%20Inquiry%20-%20${encodeURIComponent(m.name)}`;

            details.style.opacity = "0";
            
            setTimeout(() => {
                details.innerHTML = `
                    <img src="${m.photo}" class="profile-photo-large" alt="${m.name}">
                    <h2 class="vds-h2 mb-2">${m.name}</h2>
                    <h5 class="vds-text-muted mb-4" style="color: var(--vds-forest);">${m.role}</h5>
                    <p class="vds-text-lead mb-4" style="max-width: 600px; margin: 0 auto;">${m.bio}</p>

                    <div class="mb-5">
                        <a href="${gmailLink}" target="_blank" class="social-link" title="Send Email"><i class="bi bi-envelope"></i></a>
                        <a href="${m.facebook}" target="_blank" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="${m.github}" target="_blank" class="social-link" title="GitHub"><i class="bi bi-github"></i></a>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <div class="col-md-6">
                            <h5 class="vds-h4 mb-3">Expertise</h5>
                            <div class="d-flex flex-wrap justify-content-center">${skillsHtml}</div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="vds-h4 mb-3">Tech Stack</h5>
                            <div class="d-flex flex-wrap justify-content-center">${techHtml}</div>
                        </div>
                    </div>
                `;
                details.style.opacity = "1";
                details.style.transition = "opacity 0.4s ease";
            }, 200);
        }

        // Check for hash on load
        window.addEventListener('load', () => {
            const hash = window.location.hash.substring(1);
            if (hash && members[hash]) {
                showProfile(hash);
            }
        });
    </script>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
