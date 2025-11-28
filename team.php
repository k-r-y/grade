<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="assets/logo2.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Meet the Team | KLD Grade System</title>
  <link rel="icon" type="image/png" href="assets/kld.png">

  <!-- Bootstrap & Icons -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="devicon-master/devicon.min.css">
  <link rel="stylesheet" href="styles.css">

  <style>
    :root {
      --primary-color: #0077b6;
      --secondary-color: #48cae4;
      --accent-color: #ade8f4;
      --dark-color: #03045e;
    }

    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #e0fbfc, #fefae0);
      color: var(--dark-color);
      min-height: 100vh;
    }

    .team-section {
      padding: 80px 0;
    }

    .profile-selector {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .profile-card {
      display: flex;
      align-items: center;
      gap: 15px;
      background: rgba(255, 255, 255, 0.25);
      border-radius: 12px;
      padding: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .profile-card:hover {
      transform: translateX(5px);
      background: rgba(255, 255, 255, 0.45);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }

    .profile-card img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      border: 3px solid var(--accent-color);
      object-fit: cover;
    }

    .profile-card span {
      font-weight: 600;
    }

    .profile-details {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: 40px;
      backdrop-filter: blur(15px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
      min-height: 400px;
    }

    .profile-photo {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 4px solid var(--secondary-color);
      object-fit: cover;
      margin-bottom: 15px;
    }

    .profile-role {
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 10px;
    }

    .social-links a {
      color: var(--dark-color);
      font-size: 1.5rem;
      margin: 0 8px;
      transition: 0.3s;
    }

    .social-links a:hover {
      color: var(--secondary-color);
      transform: scale(1.1);
    }

    .badge-skill {
      display: inline-block;
      background: var(--accent-color);
      color: var(--dark-color);
      font-weight: 500;
      border-radius: 20px;
      padding: 6px 12px;
      margin: 5px;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .profile-details {
        margin-top: 30px;
      }
    }
  </style>
</head>
<body>

  <?php include('navbar.php'); ?>

  <section class="team-section">
    <div class="container">
      <h2 class="text-center fw-bold mb-5">Meet Our Team</h2>
      <div class="row">
        <!-- Left: Member list -->
        <div class="col-md-4">
          <div class="profile-selector">
            <div class="profile-card" onclick="showProfile('rogie')">
              <img src="assets/rogie.png" alt="Rogie Ramos">
              <span>Rogie Mar U. Ramos</span>
            </div>
            <div class="profile-card" onclick="showProfile('arsyl')">
              <img src="assets/arsyl.png" alt="Arsyl Salva">
              <span>Arsyl F. Salva</span>
            </div>
            <div class="profile-card" onclick="showProfile('jm')">
              <img src="assets/jm.png" alt="Jhon Messiah Romero">
              <span>Jhon Messiah M. Romero</span>
            </div>
            <div class="profile-card" onclick="showProfile('kevin')">
              <img src="assets/kevin.png" alt="Kevin Selibio">
              <span>Kevin L. Selibio</span>
            </div>
            <div class="profile-card" onclick="showProfile('renzo')">
              <img src="assets/renzo.png" alt="Renzo Ortega">
              <span>Renzo Nathaniel D. Ortega</span>
            </div>
          </div>
        </div>

        <!-- Right: Profile details -->
        <div class="col-md-8">
          <div id="profile-details" class="profile-details text-center">
            <p>Select a team member to view their details.</p>
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
      const details = document.getElementById("profile-details");

      const skillsHtml = m.skills.map(s => `<span class='badge-skill'>${s}</span>`).join("");
      const techHtml = m.techstack.map(t => `<i class='${techIconMap[t]}' style='font-size:2rem; margin:0.3rem;'></i>`).join("");

      const gmailLink = `https://mail.google.com/mail/?view=cm&to=${m.email}&su=KLD%20Grade%20System%20Inquiry%20-%20${encodeURIComponent(m.name)}`;

      details.innerHTML = `
        <img src="${m.photo}" class="profile-photo" alt="${m.name}">
        <h2>${m.name}</h2>
        <h5 class="profile-role">${m.role}</h5>
        <p>${m.bio}</p>

        <div class="social-links mb-3">
          <a href="${gmailLink}" target="_blank" title="Send Email"><i class="bi bi-envelope"></i></a>
          <a href="${m.facebook}" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="${m.github}" target="_blank" title="GitHub"><i class="bi bi-github"></i></a>
        </div>

        <h5>Skills</h5>
        <div>${skillsHtml}</div>

        <h5 class="mt-3">Tech Stack</h5>
        <div>${techHtml}</div>
      `;

      details.style.opacity = "0";
      details.style.transition = "opacity 0.3s ease";
      setTimeout(() => { details.style.opacity = "1"; }, 50);
    }
  </script>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
