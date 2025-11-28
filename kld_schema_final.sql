CREATE DATABASE IF NOT EXISTS kld_grading_system;
USE kld_grading_system;

-- 1. INSTITUTES: The top-level colleges
CREATE TABLE IF NOT EXISTS institutes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL
);

-- 2. PROGRAMS: Linked to Institutes
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institute_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    FOREIGN KEY (institute_id) REFERENCES institutes(id) ON DELETE CASCADE
);

-- 3. USERS: Stores both Students and Teachers
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id VARCHAR(50) NOT NULL UNIQUE, -- KLD-2024-XXXX
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') DEFAULT 'student',
    program_id INT DEFAULT NULL, -- Nullable for teachers/admins
    is_verified TINYINT(1) DEFAULT 0, -- 0 = Pending, 1 = Verified
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id)
);

-- 4. VERIFICATION CODES: Temporary storage for OTPs
CREATE TABLE IF NOT EXISTS verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    INDEX (email)
);

-- 5. GRADES: The academic records
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_school_id VARCHAR(50) NOT NULL, -- Links to users.school_id
    subject_code VARCHAR(50) NOT NULL, -- e.g., CC101
    grade DECIMAL(5,2) NOT NULL, -- e.g., 1.75
    teacher_id INT NOT NULL,
    semester VARCHAR(50) DEFAULT '1st Sem 2024-2025',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- --- SEED DATA (KLD CONFIGURATION) ---
-- Clear existing data to avoid duplicates if re-running (optional, be careful in prod)
-- TRUNCATE TABLE programs;
-- TRUNCATE TABLE institutes;

INSERT INTO institutes (name, code) VALUES 
('Institute of Computing and Digital Innovation', 'ICDI'),
('Institute of Business and Management', 'IBM'),
('Institute of Engineering', 'IOE')
ON DUPLICATE KEY UPDATE code=code;

-- Programs for ICDI (Institute ID 1)
-- Assuming IDs are 1, 2, 3 based on insertion order. 
-- In a real script, we'd look up IDs, but for this SQL file we assume sequential AI.
INSERT INTO programs (institute_id, name, code) VALUES 
(1, 'Bachelor of Science in Information Systems', 'BSIS'),
(1, 'Bachelor of Science in Information Technology', 'BSIT'),
(1, 'Bachelor of Science in Computer Science', 'BSCS'),
(2, 'Bachelor of Science in Accountancy', 'BSA'),
(2, 'Bachelor of Science in Business Admin', 'BSBA')
ON DUPLICATE KEY UPDATE code=code;
