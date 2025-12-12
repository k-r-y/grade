<?php
session_start();
require 'db_connect.php';
require_once 'csrf_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$class_id_param = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Fetch all classes for this teacher
$classes = [];
$stmt = $conn->prepare("SELECT * FROM classes WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Grades | KLD Grade System</title>
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .vds-file-drop {
            border: 2px dashed var(--vds-sage);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .vds-file-drop:hover,
        .vds-file-drop.drag-over {
            border-color: var(--vds-forest);
            background: rgba(13, 59, 46, 0.05);
        }

        .validation-icon {
            font-size: 1.2rem;
        }

        .row-valid {
            background: rgba(34, 197, 94, 0.1);
        }

        .row-invalid {
            background: rgba(239, 68, 68, 0.1);
        }

        .row-duplicate {
            background: rgba(251, 191, 36, 0.1);
        }

        .editable-cell {
            border-bottom: 1px dashed var(--vds-sage);
            cursor: text;
            transition: background 0.2s;
        }

        .editable-cell:hover,
        .editable-cell:focus {
            background: rgba(255, 255, 255, 0.8);
            outline: none;
            border-bottom: 1px solid var(--vds-forest);
        }
    </style>
</head>

<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <?php if ($class_id_param > 0): ?>
                    <a href="class_details.php?id=<?php echo $class_id_param; ?>" id="backToClassBtn" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Class</a>
                <?php else: ?>
                    <a href="my_classes.php" id="backToClassBtn" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Classes</a>
                <?php endif; ?>
                <h1 class="vds-h2">Upload Grades</h1>
                <p class="vds-text-muted">Import grades via Excel file for fast data encoding.</p>
            </div>
            <div>
                <button id="downloadTemplateHeader" class="vds-btn vds-btn-outline" style="border: 1px solid var(--vds-forest) !important;">
                    <i class="bi bi-download me-1"></i>Download Template
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Upload Workflow -->
            <div class="col-lg-7">

                <!-- Step 1: Select Class -->
                <div class="vds-card p-4 mb-4">
                    <h2 class="vds-h5 mb-3 text-uppercase fw-bold text-success"><i class="bi bi-1-circle me-2"></i>Select Class</h2>

                    <?php if (empty($classes)): ?>
                        <div class="alert alert-warning mb-0">
                            <strong>No Classes Found.</strong> You need to create a class first.
                        </div>
                    <?php else: ?>
                        <div class="vds-form-group mb-3">
                            <label class="vds-label">Class <span class="text-danger">*</span></label>
                            <select id="classSelect" class="vds-input">
                                <option value="">-- Select a Class --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>"
                                        data-section="<?php echo htmlspecialchars($class['section']); ?>"
                                        data-subject-code="<?php echo htmlspecialchars($class['subject_code']); ?>"
                                        data-subject-name="<?php echo htmlspecialchars($class['subject_description']); ?>"
                                        data-semester="<?php echo htmlspecialchars($class['semester']); ?>"
                                        data-units="<?php echo htmlspecialchars($class['units'] ?? 3); ?>"
                                        <?php echo ($class_id_param == $class['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['subject_description'] . ' (' . $class['subject_code'] . ') - ' . $class['section']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="vds-form-group">
                            <label class="vds-label">Grading Period <span class="text-danger">*</span></label>
                            <select id="gradingPeriod" class="vds-input">
                                <option value="midterm">Midterm Grade</option>
                                <option value="final">Final Grade</option>
                                <option value="grade" selected>Semestral Grade</option>
                            </select>
                        </div>

                        <input type="hidden" id="section">
                        <input type="hidden" id="subjectCode">
                        <input type="hidden" id="subjectName">
                        <input type="hidden" id="semester">
                    <?php endif; ?>
                </div>

                <!-- Step 2: Upload -->
                <div class="vds-card p-4 mb-4" id="uploadStep" style="opacity: 0.5; pointer-events: none;">
                    <h2 class="vds-h5 mb-3 text-uppercase fw-bold text-success"><i class="bi bi-2-circle me-2"></i>Upload File</h2>

                    <div class="vds-file-drop p-5" id="dropZone">
                        <i class="bi bi-cloud-arrow-up text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h5 class="fw-bold">Drag & Drop Excel File</h5>
                        <p class="text-muted small mb-0">or click to browse</p>
                        <input type="file" id="fileInput" hidden accept=".xlsx, .xls">
                    </div>
                </div>

                <!-- Step 3: Preview -->
                <div id="previewContainer" style="display: none;" class="vds-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="vds-h5 mb-0 text-uppercase fw-bold text-success"><i class="bi bi-3-circle me-2"></i>Preview & Validate</h2>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="createGhostsCheck" checked>
                            <label class="form-check-label small" for="createGhostsCheck">Auto-enroll new students</label>
                        </div>
                    </div>

                    <div class="table-responsive mb-3" style="max-height: 400px;">
                        <table class="table table-sm table-hover" id="previewTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <div class="small text-muted" id="previewSummary">Loaded 0 records</div>
                        <div>
                            <button id="validateBtn" class="vds-btn vds-btn-secondary btn-sm me-2">validate</button>
                            <button id="publishBtn" class="vds-btn vds-btn-primary btn-sm" disabled>Publish Grades</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Instructions & Guide -->
            <div class="col-lg-5">
                <div class="vds-card p-4 sticky-top" style="top: 100px; z-index: 90;">
                    <h3 class="vds-h4 mb-4">How to Upload Grades</h3>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">1. Prepare your Excel File</h5>
                        <p class="text-muted small mb-3">Your Excel file should match the format below. Using the template is highly recommended.</p>

                        <div class="bg-light p-3 rounded mb-3 text-center">
                            <img src="assets/sampleFormat.png" onerror="this.src='https://placehold.co/400x150?text=Excel+Format+Example'" alt="Excel Format" class="img-fluid rounded shadow-sm border">
                            <p class="text-muted small mt-2 fst-italic">Example of valid Excel structure</p>
                        </div>

                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Column A (Student ID):</strong> Must be in format <code>20XX-X-XXXXXX</code>.</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Column B (Raw Grade):</strong> Numeric value (0-100).</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Column C (Notes):</strong> Optional remarks.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Dos and Don'ts</h5>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 rounded h-100">
                                    <h6 class="text-success fw-bold"><i class="bi bi-check-lg me-1"></i>Do</h6>
                                    <ul class="ps-3 small mb-0 text-success" style="font-size: 0.85rem;">
                                        <li>Use the provided template</li>
                                        <li>Check Student IDs for typos</li>
                                        <li>Verify raw grades before uploading</li>
                                        <li>Use a new file for every period</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-danger bg-opacity-10 rounded h-100">
                                    <h6 class="text-danger fw-bold"><i class="bi bi-x-lg me-1"></i>Don't</h6>
                                    <ul class="ps-3 small mb-0 text-danger" style="font-size: 0.85rem;">
                                        <li>Merge cells in Excel</li>
                                        <li>Add extra headers above row 1</li>
                                        <li>Use special characters in IDs</li>
                                        <li>Upload password protected files</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Note:</strong> Re-uploading a file for the same class and period will <strong>overwrite</strong> any existing grades for those students.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer_dashboard.php'; ?>

    <script>
        const classSelect = document.getElementById('classSelect');
        const uploadStep = document.getElementById('uploadStep');
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewTableBody = document.querySelector('#previewTable tbody');
        const publishBtn = document.getElementById('publishBtn');
        const validateBtn = document.getElementById('validateBtn');
        const downloadTemplateBtn = document.getElementById('downloadTemplateHeader');
        const previewSummary = document.getElementById('previewSummary');

        // Hidden fields
        const sectionInput = document.getElementById('section');
        const subjectCodeInput = document.getElementById('subjectCode');
        const subjectNameInput = document.getElementById('subjectName');
        const semesterInput = document.getElementById('semester');

        const gradingPeriodSelect = document.getElementById('gradingPeriod');
        // periodLabel removed as it's no longer in the UI

        let parsedData = [];
        let validationResults = {};
        let currentClassId = 0;

        // Transmutation Logic (Matches PHP)
        function transmuteGrade(raw) {
            raw = parseFloat(raw);
            if (isNaN(raw)) return ['-', ''];
            if (raw >= 97) return ['1.00', 'Passed'];
            if (raw >= 94) return ['1.25', 'Passed'];
            if (raw >= 91) return ['1.50', 'Passed'];
            if (raw >= 88) return ['1.75', 'Passed'];
            if (raw >= 85) return ['2.00', 'Passed'];
            if (raw >= 82) return ['2.25', 'Passed'];
            if (raw >= 79) return ['2.50', 'Passed'];
            if (raw >= 76) return ['2.75', 'Passed'];
            if (raw >= 70) return ['3.00', 'Passed'];
            return ['5.00', 'Failed'];
        }

        // Initialize state based on selection
        function updateClassState() {
            if (!classSelect) return;

            const selectedOption = classSelect.options[classSelect.selectedIndex];
            const backBtn = document.getElementById('backToClassBtn');

            if (selectedOption.value) {
                currentClassId = selectedOption.value;
                sectionInput.value = selectedOption.dataset.section;
                subjectCodeInput.value = selectedOption.dataset.subjectCode;
                subjectNameInput.value = selectedOption.dataset.subjectName;
                semesterInput.value = selectedOption.dataset.semester;

                uploadStep.style.opacity = '1';
                uploadStep.style.pointerEvents = 'auto';

                // Update Back Button
                if (backBtn) {
                    backBtn.href = `class_details.php?id=${currentClassId}`;
                    backBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> Back to Class';
                }
            } else {
                currentClassId = 0;
                uploadStep.style.opacity = '0.5';
                uploadStep.style.pointerEvents = 'none';
                previewContainer.style.display = 'none';

                // Reset Back Button (to Dashboard if no class selected)
                if (backBtn) {
                    backBtn.href = 'my_classes.php';
                    backBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> Back to Classes';
                }
            }
        }



        classSelect.addEventListener('change', updateClassState);

        gradingPeriodSelect.addEventListener('change', () => {
            const text = gradingPeriodSelect.options[gradingPeriodSelect.selectedIndex].text;
            // periodLabel.textContent = text; // Removed
        });

        // Run on load to handle pre-selection
        updateClassState();

        // Download Template
        downloadTemplateBtn.addEventListener('click', () => {
            const wb = XLSX.utils.book_new();
            const wsData = [
                ['Student ID', 'Raw Grade', 'Notes'],
                ['2024-2-000550', '98', 'Excellent work'],
                ['2024-2-000551', '85', ''],
                ['2024-2-000552', '74', 'Needs improvement']
            ];
            const ws = XLSX.utils.aoa_to_sheet(wsData);

            // Set column widths
            ws['!cols'] = [{
                    wch: 20
                }, // Student ID
                {
                    wch: 15
                }, // Raw Grade
                {
                    wch: 30
                } // Notes
            ];

            XLSX.utils.book_append_sheet(wb, ws, "Template");
            XLSX.writeFile(wb, "grade_upload_template.xlsx");
        });

        // Drag & Drop Events
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length) handleFile(files[0]);
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) handleFile(e.target.files[0]);
        });

        function handleFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                parsedData = XLSX.utils.sheet_to_json(firstSheet, {
                    header: 1
                });

                // Smart Parsing: Find header row
                let headerRowIndex = -1;
                for (let i = 0; i < Math.min(parsedData.length, 20); i++) {
                    const row = parsedData[i];
                    if (!row || row.length === 0) continue;

                    const rowStr = JSON.stringify(row).toLowerCase();
                    // Look for standard headers
                    if (rowStr.includes('student id') || (rowStr.includes('id') && rowStr.includes('grade'))) {
                        headerRowIndex = i;
                        break;
                    }
                }

                if (headerRowIndex !== -1) {
                    parsedData = parsedData.slice(headerRowIndex + 1);
                } else {
                    // Fallback: Look for the first row that looks like data (starts with a number)
                    let dataStartIndex = 0;
                    for (let i = 0; i < Math.min(parsedData.length, 20); i++) {
                        const firstCell = parsedData[i][0];
                        if (firstCell && typeof firstCell === 'string' && /^\d/.test(firstCell)) {
                            dataStartIndex = i;
                            break;
                        }
                    }
                    parsedData = parsedData.slice(dataStartIndex);
                }

                renderPreview(parsedData);
                previewContainer.style.display = 'block';
            };
            reader.readAsArrayBuffer(file);
        }

        function renderPreview(data) {
            previewTableBody.innerHTML = '';
            validationResults = {};
            publishBtn.disabled = true;

            // Update Table Header
            const thead = document.querySelector('#previewTable thead tr');
            thead.innerHTML = `
                <th style="width: 50px;">#</th>
                <th style="width: 60px;">Status</th>
                <th>Student ID <i class="bi bi-pencil-square small text-muted ms-1"></i></th>
                <th>Student Name</th>
                <th>Units</th>
                <th>Raw Grade <i class="bi bi-pencil-square small text-muted ms-1"></i></th>
                <th>Transmuted</th>
                <th>Remarks</th>
            `;

            const units = classSelect.options[classSelect.selectedIndex].dataset.units || 3;

            data.forEach((row, index) => {
                // Ensure row has at least 3 elements
                if (!row[0]) row[0] = '';
                if (!row[1]) row[1] = '';
                if (!row[2]) row[2] = '';

                const rawGrade = row[1];
                const [transmuted, remarks] = transmuteGrade(rawGrade);

                const studentId = row[0];
                const idRegex = /^\d{4}-\d{1}-\d{6}$/;
                let statusIcon = 'bi-question-circle text-muted';
                let statusTitle = 'Pending Validation';

                if (studentId && !idRegex.test(studentId)) {
                    statusIcon = 'bi-exclamation-triangle-fill text-warning';
                    statusTitle = 'Invalid ID Format (xxxx-x-xxxxxx)';
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td><i class="bi ${statusIcon} validation-icon" id="status-${index}" title="${statusTitle}"></i></td>
                    <td contenteditable="true" class="editable-cell" onblur="updateData(${index}, 0, this.innerText)" onfocus="highlightRow(this)">${row[0]}</td>
                    <td id="student-name-${index}" class="text-muted">-</td>
                    <td>${units}</td>
                    <td contenteditable="true" class="editable-cell" oninput="handleGradeInput(${index}, this)" onblur="updateData(${index}, 1, this.innerText)" onfocus="highlightRow(this)">${rawGrade}</td>
                    <td id="transmuted-${index}"><span class="fw-bold text-primary">${transmuted}</span></td>
                    <td id="remarks-${index}"><span class="badge ${remarks === 'Passed' ? 'bg-success' : 'bg-danger'}">${remarks}</span> <small class="text-muted">${row[2]}</small></td>
                `;
                previewTableBody.appendChild(tr);
            });

            updateSummary(data.length, 0, 0, 0);
        }

        // Highlight row being edited
        window.highlightRow = function(cell) {
            document.querySelectorAll('tr').forEach(tr => tr.classList.remove('table-active'));
            cell.closest('tr').classList.add('table-active');
        };

        // Handle Real-time Grade Transmutation
        window.handleGradeInput = function(index, cell) {
            const raw = cell.innerText;
            const rawFloat = parseFloat(raw);

            if (isNaN(rawFloat) || rawFloat < 0 || rawFloat > 100) {
                cell.classList.add('text-danger', 'fw-bold');
                cell.title = "Grade must be between 0 and 100";
            } else {
                cell.classList.remove('text-danger', 'fw-bold');
                cell.title = "";
            }

            const [transmuted, remarks] = transmuteGrade(raw);

            document.getElementById(`transmuted-${index}`).innerHTML = `<span class="fw-bold text-primary">${transmuted}</span>`;

            // Preserve existing notes if any
            const existingNotes = parsedData[index][2] || '';
            document.getElementById(`remarks-${index}`).innerHTML = `<span class="badge ${remarks === 'Passed' ? 'bg-success' : 'bg-danger'}">${remarks}</span> <small class="text-muted">${existingNotes}</small>`;
        };

        // Update parsedData array when cell is blurred
        window.updateData = function(index, colIndex, value) {
            parsedData[index][colIndex] = value.trim();

            // If Student ID changed, reset validation status for that row
            if (colIndex === 0) {
                const idRegex = /^\d{4}-\d{1}-\d{6}$/;
                const isValidFormat = idRegex.test(value);

                const statusEl = document.getElementById(`status-${index}`);
                if (!isValidFormat && value) {
                    statusEl.className = 'bi bi-exclamation-triangle-fill validation-icon text-warning';
                    statusEl.title = 'Invalid ID Format (xxxx-x-xxxxxx)';
                } else {
                    statusEl.className = 'bi bi-question-circle validation-icon text-muted';
                    statusEl.title = 'Pending Validation';
                }

                document.getElementById(`student-name-${index}`).textContent = '-';
                statusEl.closest('tr').className = '';

                // Reset summary counts (visual only, real count updates on Validate)
                // We force user to click Validate again to be sure
                publishBtn.disabled = true;
            }
        };

        function updateSummary(total, valid, invalid, duplicates) {
            previewSummary.innerHTML = `
                Total: <strong>${total}</strong> | 
                Valid: <strong class="text-success">${valid}</strong> | 
                Error: <strong class="text-danger">${invalid}</strong> | 
                Not Enrolled: <strong class="text-info">${duplicates}</strong>
            `;
        }

        // Validate Students
        validateBtn.addEventListener('click', async () => {
            if (!currentClassId) {
                alert("Please select a class first.");
                return;
            }

            validateBtn.disabled = true;
            validateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Validating...';

            const studentIds = parsedData
                .filter(row => row[0])
                .map(row => row[0]);

            try {
                const response = await fetch('api.php?action=validate_students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_ids: studentIds,
                        class_id: currentClassId,
                        create_ghosts: document.getElementById('createGhostsCheck').checked,
                        csrf_token: document.querySelector('meta[name="csrf-token"]').content
                    })
                });

                const result = await response.json();

                if (result.success) {
                    validationResults = {};

                    // Populate validation results
                    if (result.valid) {
                        result.valid.forEach(item => {
                            validationResults[item.school_id] = {
                                valid: true,
                                status: item.status || 'valid',
                                name: item.name || 'New Student'
                            };
                        });
                    }
                    if (result.invalid) {
                        result.invalid.forEach(item => {
                            validationResults[item.school_id] = {
                                valid: false,
                                status: 'invalid',
                                error: item.error
                            };
                        });
                    }
                    if (result.not_enrolled) {
                        result.not_enrolled.forEach(school_id => {
                            validationResults[school_id] = {
                                valid: true, // Treat as valid for auto-enrollment
                                status: 'not_enrolled'
                            };
                        });
                    }

                    // Update UI
                    let validCount = 0;
                    let errorCount = 0;
                    let autoEnrollCount = 0;

                    parsedData.forEach((row, index) => {
                        const schoolId = row[0];
                        const statusIcon = document.getElementById(`status-${index}`);
                        const nameCell = document.getElementById(`student-name-${index}`);
                        const rowElem = statusIcon.closest('tr');

                        if (validationResults[schoolId]) {
                            const res = validationResults[schoolId];
                            if (res.status === 'valid') {
                                statusIcon.className = 'bi bi-check-circle-fill validation-icon text-success';
                                nameCell.textContent = res.name;
                                rowElem.className = 'row-valid';
                                validCount++;
                            } else if (res.status === 'not_enrolled') {
                                statusIcon.className = 'bi bi-info-circle-fill validation-icon text-info';
                                nameCell.textContent = 'Not Enrolled (Grade will be saved)';
                                rowElem.className = 'row-duplicate';
                                validCount++; // Treat as valid for button enablement
                                autoEnrollCount++; // Show in summary
                            } else if (res.status === 'ghost_create') {
                                statusIcon.className = 'bi bi-person-badge-fill validation-icon text-warning';
                                nameCell.textContent = 'Will Create Ghost Account';
                                rowElem.className = 'row-duplicate';
                                autoEnrollCount++;
                            } else {
                                statusIcon.className = 'bi bi-exclamation-circle-fill validation-icon text-danger';
                                nameCell.textContent = 'Not Enrolled';
                                rowElem.className = 'row-invalid';
                                errorCount++;
                            }
                        } else {
                            // If ID was not sent for validation (e.g., empty or invalid format)
                            const idRegex = /^\d{4}-\d{1}-\d{6}$/;
                            if (!schoolId || !idRegex.test(schoolId)) {
                                statusIcon.className = 'bi bi-exclamation-triangle-fill validation-icon text-warning';
                                statusIcon.title = 'Invalid ID Format (xxxx-x-xxxxxx)';
                                nameCell.textContent = '-';
                                rowElem.className = 'row-invalid';
                                errorCount++;
                            } else {
                                // Should not happen if all valid IDs are sent
                                statusIcon.className = 'bi bi-question-circle validation-icon text-muted';
                                statusIcon.title = 'Pending Validation';
                                nameCell.textContent = '-';
                                rowElem.className = '';
                            }
                        }
                    });

                    updateSummary(parsedData.length, validCount, errorCount, autoEnrollCount);

                    // Error Rate Check
                    const errorRate = (errorCount / parsedData.length) * 100;
                    if (errorRate > 20) {
                        Swal.fire({
                            title: 'High Error Rate Detected',
                            text: `It looks like ${Math.round(errorRate)}% of the rows are invalid. Are you sure this is the correct file?`,
                            icon: 'warning',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'I understand, proceed'
                        });
                    }

                    if (validCount > 0 || autoEnrollCount > 0) {
                        publishBtn.disabled = false;
                    }

                    Swal.fire({
                        title: 'Validation Complete',
                        html: `✓ ${validCount} enrolled students<br>ℹ️ ${autoEnrollCount} will be auto-enrolled<br>✗ ${errorCount} errors (not found)`,
                        icon: 'info',
                        confirmButtonColor: '#0D3B2E'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error validating students: ' + error.message
                });
            } finally {
                validateBtn.disabled = false;
                validateBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Validate Students';
            }
        });

        // Publish Grades
        publishBtn.addEventListener('click', async () => {
            try {
                console.log('Publish button clicked');

                if (!currentClassId) {
                    console.warn('No class selected');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please select a class first.'
                    });
                    return;
                }

                const section = sectionInput.value;
                const subjectCode = subjectCodeInput.value;
                const subjectName = subjectNameInput.value;
                const semester = semesterInput.value;
                const gradingPeriod = gradingPeriodSelect.value;
                const gradingPeriodText = gradingPeriodSelect.options[gradingPeriodSelect.selectedIndex].text;

                console.log('Publishing for:', {
                    section,
                    subjectCode,
                    gradingPeriod,
                    count: parsedData.length
                });

                if (!parsedData || parsedData.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Data',
                        text: 'No grade data to publish.'
                    });
                    return;
                }

                // Check for invalid grades
                const hasInvalidGrades = parsedData.some(row => {
                    const grade = parseFloat(row[1]);
                    return isNaN(grade) || grade < 0 || grade > 100;
                });

                if (hasInvalidGrades) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Grades',
                        text: 'Some grades are invalid (must be 0-100). Please correct them before publishing.'
                    });
                    return;
                }

                const result = await Swal.fire({
                    title: 'Confirm Upload?',
                    html: `Class: ${subjectCode} - ${section}<br>Period: <strong>${gradingPeriodText}</strong><br>Total Records: ${parsedData.length}<br><br>This will save/update grades in the database.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0D3B2E',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Upload'
                });

                if (!result.isConfirmed) return;

                publishBtn.disabled = true;
                publishBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Publishing...';

                const response = await fetch('api.php?action=bulk_upload_grades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        grades: parsedData,
                        section: section,
                        subject_code: subjectCode,
                        subject_name: subjectName,
                        semester: semester,
                        class_id: currentClassId,
                        grading_period: gradingPeriod,
                        create_ghosts: document.getElementById('createGhostsCheck').checked,
                        csrf_token: document.querySelector('meta[name="csrf-token"]').content
                    })
                });

                const resJson = await response.json();
                console.log('Server response:', resJson);

                if (resJson.success) {
                    let msg = `Inserted: ${resJson.inserted}<br>Updated: ${resJson.updated}<br>Errors: ${resJson.errors.length}`;
                    if (resJson.auto_enrolled && resJson.auto_enrolled.length > 0) {
                        msg += `<br><br>ℹ️ ${resJson.auto_enrolled.length} ghost accounts were created.`;
                    }
                    Swal.fire({
                        title: 'Success!',
                        html: msg,
                        icon: 'success',
                        confirmButtonColor: '#0D3B2E'
                    });

                    // Reset form
                    previewContainer.style.display = 'none';
                    parsedData = [];
                    fileInput.value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resJson.message
                    });
                }
            } catch (error) {
                console.error('Publish Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An unexpected error occurred: ' + error.message
                });
            } finally {
                publishBtn.disabled = false;
                publishBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Publish Grades';
            }
        });
    </script>

</body>

</html>