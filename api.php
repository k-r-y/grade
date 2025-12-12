<?php
require 'db_connect.php';
require 'email_helper.php';
require 'csrf_helper.php';
header('Content-Type: application/json');

// Disable error display to prevent HTML output
ini_set('display_errors', 0);
error_reporting(E_ALL);

$action = $_GET['action'] ?? '';

// Debug Logging
$logFile = 'debug_log.txt';
$logMessage = date('Y-m-d H:i:s') . " - Action: $action\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

if ($action === 'get_institutes') {
    $sql = "SELECT * FROM institutes GROUP BY name ORDER BY name ASC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($action === 'get_programs') {
    $institute_id = isset($_GET['institute_id']) ? intval($_GET['institute_id']) : 0;
    if ($institute_id > 0) {
        $sql = "SELECT * FROM programs WHERE institute_id = $institute_id GROUP BY name ORDER BY name ASC";
    } else {
        $sql = "SELECT * FROM programs GROUP BY name ORDER BY name ASC";
    }
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// Helper: Transmute Grade
function transmuteGrade($raw)
{
    if ($raw >= 97) return [1.00, 'Passed'];
    if ($raw >= 94) return [1.25, 'Passed'];
    if ($raw >= 91) return [1.50, 'Passed'];
    if ($raw >= 88) return [1.75, 'Passed'];
    if ($raw >= 85) return [2.00, 'Passed'];
    if ($raw >= 82) return [2.25, 'Passed'];
    if ($raw >= 79) return [2.50, 'Passed'];
    if ($raw >= 76) return [2.75, 'Passed'];
    if ($raw >= 75) return [3.00, 'Passed']; // Adjusted to include 75 as passing if needed, or stick to 75=3.00
    // Previous code had 70=3.00, let's stick to standard if possible, but user code had 70.
    // Let's use the logic from the previous file:
    if ($raw >= 70) return [3.00, 'Passed'];
    return [5.00, 'Failed'];
}

// Bulk Upload Grades
if ($action === 'bulk_upload_grades') {

    try {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            throw new Exception('Unauthorized. Only teachers can upload grades.');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $grades = $input['grades'] ?? [];
        $class_id = isset($input['class_id']) ? intval($input['class_id']) : 0;
        $grading_period = $input['grading_period'] ?? 'grade';
        $create_ghosts = $input['create_ghosts'] ?? false;
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

        if (!verify_csrf_token($csrf_token)) {
            throw new Exception('Invalid CSRF Token');
        }

        // Map grading period to column name
        $target_column = 'grade';
        if ($grading_period === 'midterm') $target_column = 'midterm';
        if ($grading_period === 'final') $target_column = 'final';
        if ($grading_period === 'grade') $target_column = 'grade';

        $section = '';
        $subject_code = '';
        $subject_name = '';
        $semester = '';

        if ($class_id > 0) {
            $stmtClass = $conn->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
            if (!$stmtClass) throw new Exception("Prepare failed for class check: " . $conn->error);

            $stmtClass->bind_param("ii", $class_id, $_SESSION['user_id']);
            $stmtClass->execute();
            $classRes = $stmtClass->get_result();
            if ($classRow = $classRes->fetch_assoc()) {
                $section = $classRow['section'];
                $subject_code = $classRow['subject_code'];
                $subject_name = $classRow['subject_description'];
                $semester = $classRow['semester'];
            } else {
                throw new Exception('Invalid Class ID');
            }
        } else {
            $section = trim($input['section'] ?? '');
            $subject_code = trim($input['subject_code'] ?? '');
            $subject_name = trim($input['subject_name'] ?? '');
            $semester = trim($input['semester'] ?? '1st Sem 2024-2025');
        }

        $teacher_id = $_SESSION['user_id'];

        if (empty($grades)) {
            throw new Exception('No grade data provided');
        }

        if (empty($section) || empty($subject_code)) {
            throw new Exception('Section and Subject Code are required');
        }

        // Initialize bind variables
        $b_class_id = $class_id;
        $b_student_school_id = '';

        $b_student_id = 0;
        $b_subject_code = $subject_code;
        $b_subject_name = $subject_name;
        $b_transmuted_grade = 0.0;
        $b_raw_grade = 0.0;
        $b_final_remarks = '';
        $b_teacher_id = $teacher_id;
        $b_section = $section;
        $b_semester = $semester;

        $b_ghost_name = '';
        $b_ghost_email = '';
        $b_ghost_pass = '';

        $b_semestral_grade = 0.0;

        $conn->begin_transaction();

        // 1. Prepare Find Student
        $stmtFindStudent = $conn->prepare("
            SELECT u.id, u.full_name, u.email, u.role, g.midterm, g.final 
            FROM users u 
            LEFT JOIN grades g ON u.id = g.student_id AND g.class_id = ?
            WHERE u.school_id = ?
        ");
        if (!$stmtFindStudent) throw new Exception("Prepare failed for find student: " . $conn->error);
        $stmtFindStudent->bind_param("is", $b_class_id, $b_student_school_id);

        // 2. Prepare Upsert Grade
        // Note: We use b_transmuted_grade twice (once for column, once for target period if applicable)
        $stmtUpsert = $conn->prepare("
            INSERT INTO grades (student_id, subject_code, subject_name, transmutated_grade, $target_column, raw_grade, remarks, teacher_id, section, semester, class_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                $target_column = VALUES($target_column), 
                transmutated_grade = VALUES(transmutated_grade),
                raw_grade = VALUES(raw_grade),
                remarks = VALUES(remarks),
                subject_name = VALUES(subject_name),
                class_id = VALUES(class_id),
                updated_at = CURRENT_TIMESTAMP
        ");
        if (!$stmtUpsert) throw new Exception("Prepare failed for grades insert: " . $conn->error);
        $stmtUpsert->bind_param("issdddssssi", $b_student_id, $b_subject_code, $b_subject_name, $b_transmuted_grade, $b_transmuted_grade, $b_raw_grade, $b_final_remarks, $b_teacher_id, $b_section, $b_semester, $b_class_id);

        // 3. Prepare Create Ghost
        $stmtCreateGhost = $conn->prepare("INSERT INTO users (school_id, full_name, email, password_hash, role, status, is_verified) VALUES (?, ?, ?, ?, 'student', 'ghost', 0)");
        if (!$stmtCreateGhost) throw new Exception("Prepare failed for ghost user insert: " . $conn->error);
        $stmtCreateGhost->bind_param("ssss", $b_student_school_id, $b_ghost_name, $b_ghost_email, $b_ghost_pass);

        // 4. Prepare Update Semestral Grade
        $stmtUpdateGrade = $conn->prepare("UPDATE grades SET grade = ? WHERE student_id = ? AND class_id = ?");
        $stmtUpdateGrade->bind_param("dii", $b_semestral_grade, $b_student_id, $b_class_id);

        foreach ($grades as $row) {
            $b_student_school_id = trim($row[0] ?? '');
            $b_raw_grade = floatval($row[1] ?? 0);
            $notes = trim($row[2] ?? '');

            if ($b_raw_grade < 0 || $b_raw_grade > 100) {
                $errors[] = "Grade for $b_student_school_id must be between 0 and 100";
                continue;
            }

            // Transmute
            list($t_grade, $status_remarks) = transmuteGrade($b_raw_grade);
            $b_transmuted_grade = $t_grade; // Updates bind var

            $b_final_remarks = $status_remarks;
            if (!empty($notes)) {
                $b_final_remarks .= " - " . $notes;
            }

            if (empty($b_student_school_id)) continue;

            // Execute Find
            // b_class_id and b_student_school_id are already bound
            $stmtFindStudent->execute();
            $res = $stmtFindStudent->get_result();
            $student = $res->fetch_assoc();
            $res->free(); // Explicitly free result to prevent out of sync commands

            if ($student) {
                if ($student['role'] !== 'student') {
                    $errors[] = "User $b_student_school_id exists but is a {$student['role']}, not a student.";
                    continue;
                }

                $b_student_id = $student['id'];

                // Calculate Semestral Grade
                $midterm = $student['midterm'];
                $final = $student['final']; // These might be null

                if ($grading_period === 'midterm') $midterm = $b_transmuted_grade;
                if ($grading_period === 'final') $final = $b_transmuted_grade;

                $semestral_val = null;
                if ($midterm > 0 && $final > 0) {
                    $semestral_val = ($midterm + $final) / 2;
                }

                // Update Semestral Grade if applicable
                if ($semestral_val !== null) {
                    $b_semestral_grade = $semestral_val;
                    $stmtUpdateGrade->execute();
                }

                // Execute Upsert
                // All bind vars ($b_student_id, etc) are updated
                if ($stmtUpsert->execute()) {
                    if ($stmtUpsert->affected_rows === 1) {
                        $successCount++;
                    } else {
                        $updateCount++;
                    }
                } else {
                    $errors[] = "Error saving grade for $b_student_school_id";
                }
            } else {
                // Ghost Logic
                if ($create_ghosts) {
                    $b_ghost_email = "ghost_" . $b_student_school_id . "@kld.edu.ph";
                    $b_ghost_pass = password_hash("ghost", PASSWORD_DEFAULT);
                    $b_ghost_name = "Student " . $b_student_school_id;
                    // b_student_school_id is already set

                    if ($stmtCreateGhost->execute()) {
                        $b_student_id = $stmtCreateGhost->insert_id;
                        $autoEnrolled[] = $b_student_school_id . " (Ghost)";

                        // Execute Upsert for New Ghost
                        if ($stmtUpsert->execute()) {
                            $successCount++;
                        } else {
                            // Succeeded ghost creation but failed grade?
                            $errors[] = "Created ghost $b_student_school_id but failed to save grade.";
                        }
                    } else {
                        $errors[] = "Failed to create ghost user for $b_student_school_id: " . $stmtCreateGhost->error;
                    }
                } else {
                    $notFound[] = $b_student_school_id;
                    $errors[] = "Student $b_student_school_id not found (Ghost creation disabled)";
                }
            }
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'inserted' => $successCount,
            'updated' => $updateCount,
            'errors' => $errors,
            'not_found' => $notFound,
            'auto_enrolled' => $autoEnrolled
        ]);
    } catch (Throwable $e) {
        file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
        try {
            $conn->rollback();
        } catch (Throwable $e2) {
        }

        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Validate Students
if ($action === 'validate_students') {

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $student_ids = $input['student_ids'] ?? [];
    $class_id = isset($input['class_id']) ? intval($input['class_id']) : 0;

    $valid = [];
    $invalid = [];
    $not_enrolled = [];

    $stmt = $conn->prepare("SELECT id, school_id, full_name, role FROM users WHERE school_id = ?");
    $stmtCheckEnrollment = $conn->prepare("SELECT id FROM enrollments WHERE class_id = ? AND student_id = ?");

    foreach ($student_ids as $school_id) {
        $school_id = trim($school_id);
        if (empty($school_id)) continue;

        $stmt->bind_param("s", $school_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['role'] !== 'student') {
                $invalid[] = [
                    'school_id' => $school_id,
                    'error' => "User is a {$row['role']}"
                ];
                continue;
            }

            if ($class_id > 0) {
                $stmtCheckEnrollment->bind_param("ii", $class_id, $row['id']);
                $stmtCheckEnrollment->execute();
                if ($stmtCheckEnrollment->get_result()->num_rows === 0) {
                    $not_enrolled[] = $school_id;
                }
            }

            $valid[] = [
                'school_id' => $school_id,
                'name' => $row['full_name']
            ];
        } else {
            if ($input['create_ghosts'] ?? false) {
                $valid[] = [
                    'school_id' => $school_id,
                    'status' => 'ghost_create'
                ];
            } else {
                $invalid[] = [
                    'school_id' => $school_id,
                    'error' => 'Not Found'
                ];
            }
        }
    }

    echo json_encode([
        'success' => true,
        'valid' => $valid,
        'invalid' => $invalid,
        'not_enrolled' => $not_enrolled
    ]);
    exit;
}

// Create Class
if ($action === 'create_class') {
    if ($_SESSION['role'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $subject_code = trim($input['subject_code']);
    $subject_desc = trim($input['subject_description'] ?? '');
    $section = trim($input['section']);
    $semester = trim($input['semester']);
    $units = intval($input['units'] ?? 3);
    $schedule = trim($input['schedule'] ?? 'TBA');
    $program_id = !empty($input['program_id']) ? intval($input['program_id']) : null;
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    if (empty($subject_code) || empty($section)) {
        echo json_encode(['success' => false, 'message' => 'Subject Code and Section are required']);
        exit;
    }

    if (preg_match('/[^a-zA-Z0-9]/', $section)) {
        echo json_encode(['success' => false, 'message' => 'Section must be alphanumeric (e.g., 209, A, B1). No special characters or dashes allowed.']);
        exit;
    }

    $class_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

    $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE class_code = ?");
    $stmtCheck->bind_param("s", $class_code);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows > 0) {
        $class_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    }

    $stmt = $conn->prepare("INSERT INTO classes (teacher_id, subject_code, subject_description, section, class_code, semester, units, schedule, program_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssisi", $teacher_id, $subject_code, $subject_desc, $section, $class_code, $semester, $units, $schedule, $program_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'class_code' => $class_code, 'message' => 'Class created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    exit;
}

if ($action === 'edit_class') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($input['class_id']);
    $subject_code = trim($input['subject_code']);
    $subject_desc = trim($input['subject_description'] ?? '');
    $section = trim($input['section']);
    $semester = trim($input['semester']);
    $units = intval($input['units'] ?? 3);
    $schedule = trim($input['schedule'] ?? 'TBA');
    $program_id = !empty($input['program_id']) ? intval($input['program_id']) : null;
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    if (empty($class_id) || empty($subject_code) || empty($section)) {
        echo json_encode(['success' => false, 'message' => 'Class ID, Subject Code and Section are required']);
        exit;
    }

    if (preg_match('/[^a-zA-Z0-9]/', $section)) {
        echo json_encode(['success' => false, 'message' => 'Section must be alphanumeric (e.g., 209, A, B1). No special characters or dashes allowed.']);
        exit;
    }

    // Verify ownership
    $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmtCheck->bind_param("ii", $class_id, $teacher_id);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Class not found or unauthorized']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE classes SET subject_code = ?, subject_description = ?, section = ?, semester = ?, units = ?, schedule = ?, program_id = ? WHERE id = ?");
    $stmt->bind_param("ssssisii", $subject_code, $subject_desc, $section, $semester, $units, $schedule, $program_id, $class_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Class updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    exit;
}

// Join Class
if ($action === 'join_class') {
    if ($_SESSION['role'] !== 'student') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_code = trim($input['class_code']);
    $student_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    if (empty($class_code)) {
        echo json_encode(['success' => false, 'message' => 'Class Code is required']);
        exit;
    }

    // Get Class Info with Program Code
    $stmt = $conn->prepare("
        SELECT c.id, c.subject_code, c.section, c.program_id, p.code as program_code 
        FROM classes c 
        LEFT JOIN programs p ON c.program_id = p.id 
        WHERE c.class_code = ?
    ");
    $stmt->bind_param("s", $class_code);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $class_id = $row['id'];
        $class_section = $row['section'];
        $class_program_code = $row['program_code'];

        // Fetch Student Info with Program Code
        $stmtUser = $conn->prepare("
            SELECT u.section, u.program_id, p.code as program_code 
            FROM users u 
            LEFT JOIN programs p ON u.program_id = p.id 
            WHERE u.id = ?
        ");
        $stmtUser->bind_param("i", $student_id);
        $stmtUser->execute();
        $student = $stmtUser->get_result()->fetch_assoc();

        // Check Restrictions
        // DEBUG LOGGING
        file_put_contents('debug_log.txt', "Join Class Debug:\nClass ID: $class_id\nClass Program: $class_program_code\nStudent ID: $student_id\nStudent Program: {$student['program_code']}\n", FILE_APPEND);

        // Compare CODES instead of IDs to handle duplicate program entries
        if (!empty($class_program_code) && strcasecmp(trim($class_program_code), trim($student['program_code'])) !== 0) {
            echo json_encode(['success' => false, 'message' => 'You cannot join this class. Program restriction mismatch.']);
            exit;
        }

        if (!empty($class_section) && strcasecmp($class_section, $student['section']) !== 0) {
            echo json_encode(['success' => false, 'message' => "You cannot join this class. Section restriction mismatch (Required: $class_section)."]);
            exit;
        }

        $stmtCheck = $conn->prepare("SELECT id FROM enrollments WHERE class_id = ? AND student_id = ?");
        $stmtCheck->bind_param("ii", $class_id, $student_id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'You are already enrolled in this class']);
            exit;
        }

        $stmtEnroll = $conn->prepare("INSERT INTO enrollments (class_id, student_id) VALUES (?, ?)");
        $stmtEnroll->bind_param("ii", $class_id, $student_id);

        if ($stmtEnroll->execute()) {
            echo json_encode(['success' => true, 'message' => "Successfully joined {$row['subject_code']} - {$row['section']}"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Enrollment failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid Class Code']);
    }
    exit;
}

// Get Classes
if ($action === 'get_classes') {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $include_archived = isset($_GET['archived']) && $_GET['archived'] === 'true';
    $archived_sql = $include_archived ? "is_archived = 1" : "is_archived = 0";

    $data = [];

    if ($role === 'teacher') {
        $stmt = $conn->prepare("SELECT * FROM classes WHERE teacher_id = ? AND $archived_sql ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $stmtCount = $conn->prepare("SELECT COUNT(*) as count FROM enrollments WHERE class_id = ?");
            $stmtCount->bind_param("i", $row['id']);
            $stmtCount->execute();
            $row['student_count'] = $stmtCount->get_result()->fetch_assoc()['count'];
            $data[] = $row;
        }
    } elseif ($role === 'student') {
        $stmt = $conn->prepare("
            SELECT c.*, u.full_name as teacher_name 
            FROM enrollments e 
            JOIN classes c ON e.class_id = c.id 
            JOIN users u ON c.teacher_id = u.id 
            WHERE e.student_id = ? AND c.$archived_sql
            ORDER BY e.joined_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode(['success' => true, 'classes' => $data]);
    exit;
}

// Archive Class
if ($action === 'archive_class') {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($input['class_id']);
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    // Verify ownership (if teacher)
    if ($_SESSION['role'] === 'teacher') {
        $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
        $stmtCheck->bind_param("ii", $class_id, $teacher_id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    $stmt = $conn->prepare("UPDATE classes SET is_archived = 1 WHERE id = ?");
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Class archived successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Unarchive Class
if ($action === 'unarchive_class') {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($input['class_id']);
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    // Verify ownership (if teacher)
    if ($_SESSION['role'] === 'teacher') {
        $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
        $stmtCheck->bind_param("ii", $class_id, $teacher_id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    $stmt = $conn->prepare("UPDATE classes SET is_archived = 0 WHERE id = ?");
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Class unarchived successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Get Class Students
if ($action === 'get_class_students') {
    $class_id = intval($_GET['class_id']);
    $teacher_id = $_SESSION['user_id'];

    $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmtCheck->bind_param("ii", $class_id, $teacher_id);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT u.id, u.school_id, u.full_name, u.email, e.joined_at, g.grade, g.midterm, g.final, g.raw_grade, g.transmutated_grade, g.remarks

            FROM enrollments e 
            JOIN users u ON e.student_id = u.id 
            LEFT JOIN grades g ON g.student_id = u.id AND g.class_id = ?
            WHERE e.class_id = ? 
            ORDER BY SUBSTRING_INDEX(u.full_name, ' ', -1) ASC, u.full_name ASC
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $class_id, $class_id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $res = $stmt->get_result();
        $students = [];
        while ($row = $res->fetch_assoc()) {
            // Format Name: "First Last" -> "Last, First"
            $nameParts = explode(' ', trim($row['full_name']));
            if (count($nameParts) > 1) {
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);
                $row['full_name'] = "$lastName, $firstName";
            }
            $students[] = $row;
        }

        echo json_encode(['success' => true, 'students' => $students]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Error loading students: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_single_grade') {
    if ($_SESSION['role'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($input['class_id']);
    $student_id = intval($input['student_id']);
    $raw_grade = floatval($input['raw_grade']);

    if ($raw_grade < 0 || $raw_grade > 100) {
        echo json_encode(['success' => false, 'message' => 'Grade must be between 0 and 100']);
        exit;
    }
    $remarks = trim($input['remarks']);
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    // Transmute
    list($transmuted_grade, $status_remarks) = transmuteGrade($raw_grade);

    if (empty($remarks)) {
        $remarks = $status_remarks;
    }

    // Verify ownership
    $stmtCheck = $conn->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
    $stmtCheck->bind_param("ii", $class_id, $teacher_id);
    $stmtCheck->execute();
    $classInfo = $stmtCheck->get_result()->fetch_assoc();

    if (!$classInfo) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Upsert Grade
    $stmt = $conn->prepare("
        INSERT INTO grades (student_id, subject_code, subject_name, grade, raw_grade, transmutated_grade, remarks, teacher_id, section, semester, class_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            grade = VALUES(grade), 
            raw_grade = VALUES(raw_grade),
            transmutated_grade = VALUES(transmutated_grade),
            remarks = VALUES(remarks),
            updated_at = CURRENT_TIMESTAMP
    ");

    $stmt->bind_param(
        "issdddssisi",
        $student_id,
        $classInfo['subject_code'],
        $classInfo['subject_description'],
        $transmuted_grade,
        $raw_grade,
        $transmuted_grade,
        $remarks,
        $teacher_id,
        $classInfo['section'],
        $classInfo['semester'],
        $class_id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Grade updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    exit;
}
// Get Settings
if ($action === 'get_settings') {
    if (!isset($_SESSION['role'])) { // Public or at least logged in? Let's say logged in.
        // Actually, maybe some settings are needed for class creation (teacher), so let's allow logged in users.
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    $result = $conn->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    echo json_encode(['success' => true, 'settings' => $settings]);
    exit;
}

// Update Settings
if ($action === 'update_settings') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    $settings = $input['settings'] ?? [];
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");

    foreach ($settings as $key => $value) {
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    exit;
}

// Manual Enroll (Irregular Students)
if ($action === 'manual_enroll') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($input['class_id']);
    $student_school_id = trim($input['student_school_id']);
    $teacher_id = $_SESSION['user_id'];
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? '');

    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF Token']);
        exit;
    }

    // Verify Class Ownership
    $stmtCheck = $conn->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmtCheck->bind_param("ii", $class_id, $teacher_id);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or Class Not Found']);
        exit;
    }

    // Find Student
    $stmtUser = $conn->prepare("SELECT id, role, full_name FROM users WHERE school_id = ?");
    $stmtUser->bind_param("s", $student_school_id);
    $stmtUser->execute();
    $student = $stmtUser->get_result()->fetch_assoc();

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student ID not found']);
        exit;
    }

    if ($student['role'] !== 'student') {
        echo json_encode(['success' => false, 'message' => 'User is not a student']);
        exit;
    }

    $student_id = $student['id'];

    // Check if already enrolled
    $stmtEnrollCheck = $conn->prepare("SELECT id FROM enrollments WHERE class_id = ? AND student_id = ?");
    $stmtEnrollCheck->bind_param("ii", $class_id, $student_id);
    $stmtEnrollCheck->execute();
    if ($stmtEnrollCheck->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Student is already enrolled']);
        exit;
    }

    // Enroll
    $stmtEnroll = $conn->prepare("INSERT INTO enrollments (class_id, student_id) VALUES (?, ?)");
    $stmtEnroll->bind_param("ii", $class_id, $student_id);

    if ($stmtEnroll->execute()) {
        echo json_encode(['success' => true, 'message' => "Successfully enrolled {$student['full_name']}"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    exit;
}
// Admin Analytics (Institute Specific)
if ($action === 'get_analytics') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $admin_id = $_SESSION['user_id'];
    // Fetch Admin's Institute
    $stmtAdm = $conn->prepare("SELECT institute_id FROM users WHERE id = ?");
    $stmtAdm->bind_param("i", $admin_id);
    $stmtAdm->execute();
    $instId = $stmtAdm->get_result()->fetch_assoc()['institute_id'];

    // --- FILTERS ---
    $filter_prog = isset($_GET['program_id']) && $_GET['program_id'] !== '' ? intval($_GET['program_id']) : null;
    $filter_year = isset($_GET['year_level']) && $_GET['year_level'] !== '' ? intval($_GET['year_level']) : null;
    $filter_acad = isset($_GET['academic_year']) && $_GET['academic_year'] !== '' ? $_GET['academic_year'] : null;

    $data = [];

    // Helper to build WHERE clauses dynamically
    // Base conditions: 
    // Students/Teachers: u.institute_id = ? (or linked via program)
    // Classes: u.institute_id = ? (via teacher)
    // Grades: linked via class -> teacher -> institute

    // 1. Total Students
    // Base: users u JOIN programs p ... WHERE role='student' AND p.institute_id = ?
    $sqlStud = "SELECT COUNT(u.id) FROM users u JOIN programs p ON u.program_id = p.id WHERE u.role='student' AND p.institute_id = ?";
    $paramsStud = [$instId];
    $typesStud = "i";

    if ($filter_prog) {
        $sqlStud .= " AND u.program_id = ?";
        $paramsStud[] = $filter_prog;
        $typesStud .= "i";
    }
    if ($filter_year) {
        $sqlStud .= " AND u.year_level = ?";
        $paramsStud[] = $filter_year;
        $typesStud .= "i";
    }
    // Academic Year doesn't apply strictly to "Total Students" (current state), but could filter by 'created_at' if needed. 
    // Usually 'Total Students' means currently enrolled. Let's keep it simple for now or strictly strictly ignore acad year for student count unless implies enrollment history.
    // User asked "all analytics should be filtered". Let's apply where possible. 
    // For student count, we'll ignore Academic Year as it's a property of Users, not Enrollments (unless we join enrollments).
    // Let's stick to current active students filters (Prog/Year).

    $stmtStud = $conn->prepare($sqlStud);
    $stmtStud->bind_param($typesStud, ...$paramsStud);
    $stmtStud->execute();
    $data['total_students'] = $stmtStud->get_result()->fetch_row()[0];

    // 2. Total Teachers (Filtered by Institute)
    // Teachers usually don't have 'year_level' or 'program' (unless specific).
    // If filters are applied, this metric might be irrelevant or 0.
    // If Program filter is set, we could find teachers in that program? (Table says program_id nullable for teachers).
    $sqlTeach = "SELECT COUNT(*) FROM users WHERE role='teacher' AND institute_id = ?";
    $paramsTeach = [$instId];
    $typesTeach = "i";
    if ($filter_prog) {
        $sqlTeach .= " AND program_id = ?";
        $paramsTeach[] = $filter_prog;
        $typesTeach .= "i";
    }
    $stmtTeach = $conn->prepare($sqlTeach);
    $stmtTeach->bind_param($typesTeach, ...$paramsTeach);
    $stmtTeach->execute();
    $data['total_teachers'] = $stmtTeach->get_result()->fetch_row()[0];


    // 3. Total Classes (Filtered)
    // c.semester = academic_year
    // c.program_id = program
    // c.teacher (institute)
    $sqlClass = "
        SELECT COUNT(c.id) 
        FROM classes c 
        JOIN users u ON c.teacher_id = u.id 
        WHERE u.institute_id = ?
    ";
    $paramsClass = [$instId];
    $typesClass = "i";

    if ($filter_prog) {
        $sqlClass .= " AND c.program_id = ?";
        $paramsClass[] = $filter_prog;
        $typesClass .= "i";
    }
    if ($filter_acad) {
        $sqlClass .= " AND c.semester = ?";
        $paramsClass[] = $filter_acad;
        $typesClass .= "s";
    }
    // Year Level might not be in 'classes' directly. Assuming classes are for mixed years? 
    // Or if we want to filter specific year classes? Schema doesn't have year_level in classes.
    // We will skip Year Level filter for Total Classes unless we infer it.

    $stmtClass = $conn->prepare($sqlClass);
    $stmtClass->bind_param($typesClass, ...$paramsClass);
    $stmtClass->execute();
    $data['total_classes'] = $stmtClass->get_result()->fetch_row()[0];


    // 4. Grade Completion % (Active Classes with Grades / Total Active Classes)
    // Applied Filters: Prog, Acad Year.
    $sqlComp = "
        SELECT 
            COUNT(DISTINCT c.id) as total_active,
            COUNT(DISTINCT CASE WHEN g.id IS NOT NULL THEN c.id END) as with_grades
        FROM classes c
        JOIN users u ON c.teacher_id = u.id
        LEFT JOIN grades g ON c.id = g.class_id
        WHERE u.institute_id = ? AND c.is_archived = 0
    ";
    $paramsComp = [$instId];
    $typesComp = "i";

    if ($filter_prog) {
        $sqlComp .= " AND c.program_id = ?";
        $paramsComp[] = $filter_prog;
        $typesComp .= "i";
    }
    if ($filter_acad) {
        $sqlComp .= " AND c.semester = ?";
        $paramsComp[] = $filter_acad;
        $typesComp .= "s";
    }

    $stmtCompletion = $conn->prepare($sqlComp);
    $stmtCompletion->bind_param($typesComp, ...$paramsComp);
    $stmtCompletion->execute();
    $compRes = $stmtCompletion->get_result()->fetch_assoc();

    $totalActive = $compRes['total_active'];
    $withGrades = $compRes['with_grades'];
    $data['grade_completion'] = ($totalActive > 0) ? round(($withGrades / $totalActive) * 100, 1) : 0;


    // 5. Chart 1: Students per Program (Aggregate)
    // Filters: Year Level. (Program filter makes this chart single-bar, which is fine).
    $sqlProg = "
        SELECT p.code, COUNT(u.id) as count 
        FROM programs p 
        LEFT JOIN users u ON p.id = u.program_id AND u.role='student' 
        WHERE p.institute_id = ? 
    ";
    $paramsProg = [$instId];
    $typesProg = "i";

    if ($filter_prog) {
        $sqlProg .= " AND p.id = ?";
        $paramsProg[] = $filter_prog;
        $typesProg .= "i";
    }
    // For the JOINed users:
    if ($filter_year) {
        $sqlProg .= " AND u.year_level = ?";
        $paramsProg[] = $filter_year;
        $typesProg .= "i";
    }

    $sqlProg .= " GROUP BY p.id";

    $stmtProg = $conn->prepare($sqlProg);
    $stmtProg->bind_param($typesProg, ...$paramsProg);
    $stmtProg->execute();
    $progRes = $stmtProg->get_result();
    $data['students_by_program'] = [];
    while ($row = $progRes->fetch_assoc()) {
        $data['students_by_program'][] = $row;
    }


    // 6. Chart 2: Pass/Fail Distribution
    // Filters: Program, Year Level (Student's), Acad Year (Class Semester).
    // Fix: Ensure we get 0 counts if null.
    $sqlPass = "
        SELECT 
            CASE 
                WHEN g.grade <= 3.0 THEN 'Passed' 
                ELSE 'Failed' 
            END as status,
            COUNT(*) as count
        FROM grades g
        JOIN classes c ON g.class_id = c.id
        JOIN users u ON g.student_id = u.id -- Filter by Student properties
        WHERE u.role='student' 
          AND (SELECT institute_id FROM users WHERE id = c.teacher_id) = ? -- Filter by Admin's Institute (via Class Teacher)
          AND g.grade > 0
    ";
    // Optimized Where:
    /*
      We need grades from classes belonging to the institute.
      grades -> classes -> teacher -> institute_id = ?
    */
    $sqlPass = "
        SELECT 
            CASE 
                WHEN g.grade <= 3.0 THEN 'Passed' 
                ELSE 'Failed' 
            END as status,
            COUNT(*) as count
        FROM grades g
        JOIN classes c ON g.class_id = c.id
        JOIN users t ON c.teacher_id = t.id -- t is Teacher
        JOIN users s ON g.student_id = s.id -- s is Student
        WHERE t.institute_id = ? AND g.grade > 0
    ";

    $paramsPass = [$instId];
    $typesPass = "i";

    if ($filter_prog) {
        // Filter by Student's Program or Class Program? Usually Student's for outcome.
        $sqlPass .= " AND s.program_id = ?";
        $paramsPass[] = $filter_prog;
        $typesPass .= "i";
    }
    if ($filter_year) {
        $sqlPass .= " AND s.year_level = ?";
        $paramsPass[] = $filter_year;
        $typesPass .= "i";
    }
    if ($filter_acad) {
        $sqlPass .= " AND c.semester = ?";
        $paramsPass[] = $filter_acad;
        $typesPass .= "s";
    }

    $sqlPass .= " GROUP BY status";

    $stmtPass = $conn->prepare($sqlPass);
    $stmtPass->bind_param($typesPass, ...$paramsPass);
    $stmtPass->execute();
    $passRes = $stmtPass->get_result();

    // Initialize defaults to ensure chart renders
    $stats = ['Passed' => 0, 'Failed' => 0];
    while ($row = $passRes->fetch_assoc()) {
        $stats[$row['status']] = (int)$row['count'];
    }

    // Format for Chart
    $data['pass_fail_stats'] = [
        ['status' => 'Passed', 'count' => $stats['Passed']],
        ['status' => 'Failed', 'count' => $stats['Failed']]
    ];


    // 7. Chart 3: Avg Grade per Program
    // Filters: Year, Acad Year.
    $sqlPerf = "
        SELECT p.code, AVG(g.grade) as avg_grade
        FROM programs p
        JOIN users s ON p.id = s.program_id
        JOIN grades g ON s.id = g.student_id
        JOIN classes c ON g.class_id = c.id
        JOIN users t ON c.teacher_id = t.id
        WHERE p.institute_id = ? AND g.grade > 0 AND g.grade <= 5
    ";
    $paramsPerf = [$instId];
    $typesPerf = "i";

    if ($filter_prog) {
        $sqlPerf .= " AND p.id = ?";
        $paramsPerf[] = $filter_prog;
        $typesPerf .= "i";
    }
    if ($filter_year) {
        $sqlPerf .= " AND s.year_level = ?";
        $paramsPerf[] = $filter_year;
        $typesPerf .= "i";
    }
    if ($filter_acad) {
        $sqlPerf .= " AND c.semester = ?";
        $paramsPerf[] = $filter_acad;
        $typesPerf .= "s";
    }

    $sqlPerf .= " GROUP BY p.id";

    $stmtPerf = $conn->prepare($sqlPerf);
    $stmtPerf->bind_param($typesPerf, ...$paramsPerf);
    $stmtPerf->execute();
    $perfRes = $stmtPerf->get_result();
    $data['avg_grade_by_program'] = [];
    while ($row = $perfRes->fetch_assoc()) {
        $data['avg_grade_by_program'][] = $row;
    }

    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}
