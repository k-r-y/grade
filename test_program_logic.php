<?php
// Mock data
$class_program_code = " BSIS ";
$student_program_code = "bsis";

echo "Class Code: '$class_program_code'\n";
echo "Student Code: '$student_program_code'\n";

// Old Logic
if (!empty($class_program_code) && $class_program_code !== $student_program_code) {
    echo "Old Logic: Mismatch (Restricted)\n";
} else {
    echo "Old Logic: Match (Allowed)\n";
}

// New Logic
if (!empty($class_program_code) && strcasecmp(trim($class_program_code), trim($student_program_code)) !== 0) {
    echo "New Logic: Mismatch (Restricted)\n";
} else {
    echo "New Logic: Match (Allowed)\n";
}
?>
