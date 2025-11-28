<?php
require 'db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get_institutes') {
    $sql = "SELECT * FROM institutes ORDER BY name ASC";
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($action === 'get_programs') {
    $institute_id = intval($_GET['institute_id']);
    $sql = "SELECT * FROM programs WHERE institute_id = $institute_id ORDER BY name ASC";
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// ... other API endpoints will be added here

if ($action === 'publish_grades') {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $grades = $input['grades'] ?? [];
    
    if (empty($grades)) {
        echo json_encode(['success' => false, 'message' => 'No data provided']);
        exit;
    }

    $successCount = 0;
    $errors = [];

    // Prepare statements
    // 1. Find User ID by School ID
    $stmtUser = $conn->prepare("SELECT id FROM users WHERE school_id = ?");
    
    // 2. Insert Grade
    $stmtGrade = $conn->prepare("INSERT INTO grades (student_id, subject_code, grade, remarks) VALUES (?, ?, ?, ?)");

    foreach ($grades as $row) {
        // Expected: [SchoolID, SubjectCode, Grade, Remarks]
        $schoolId = isset($row[0]) ? trim($row[0]) : '';
        $subjectCode = isset($row[1]) ? trim($row[1]) : '';
        $gradeVal = isset($row[2]) ? floatval($row[2]) : 0.0;
        $remarks = isset($row[3]) ? trim($row[3]) : '';

        if (empty($schoolId) || empty($subjectCode)) continue;

        // Find Student
        $stmtUser->bind_param("s", $schoolId);
        $stmtUser->execute();
        $resUser = $stmtUser->get_result();
        
        if ($userRow = $resUser->fetch_assoc()) {
            $studentId = $userRow['id'];
            
            // Insert Grade
            $stmtGrade->bind_param("isds", $studentId, $subjectCode, $gradeVal, $remarks);
            if ($stmtGrade->execute()) {
                $successCount++;
            } else {
                $errors[] = "Failed to insert for $schoolId: " . $stmtGrade->error;
            }
        } else {
            $errors[] = "Student not found: $schoolId";
        }
    }

    echo json_encode([
        'success' => true, 
        'count' => $successCount, 
        'errors' => $errors
    ]);
    exit;
}
?>
