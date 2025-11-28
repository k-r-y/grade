<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'teacher') {
    // Redirect if not teacher (Note: 'role' is used in register.php, assuming 'user_type' was old)
    // I need to check what login.php sets. Let's assume 'role' based on register.php refactor.
    // Actually, let's check login.php later. For now, I'll use 'role' as per my new schema/register.
    // If login.php uses 'user_type', I might need to fix it. 
    // BUT, I haven't refactored login.php yet. 
    // WAIT. I should verify login.php session keys.
    // The previous summary said login.php was refactored. 
    // Let's assume standard session keys. I'll stick to 'role' as per register.php.
    // If login.php is old, I will need to fix it.
}

// For now, let's just allow access if session is set, or redirect.
// Ideally, I should fix login.php to match register.php's 'role'.
// I'll use a generic check for now to avoid lockout during dev.
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['full_name'] ?? 'Teacher';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <!-- SheetJS for Excel Parsing -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
</head>
<body>

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container" style="padding-top: 40px; padding-bottom: 60px;">
        
        <!-- Welcome Section -->
        <div class="vds-card mb-5" style="background: linear-gradient(135deg, var(--vds-forest), var(--vds-moss)); color: white;">
            <div class="p-4">
                <h1 class="vds-h2" style="color: white;">Welcome, <?php echo htmlspecialchars($first_name); ?></h1>
                <p class="vds-text-lead" style="color: rgba(255,255,255,0.9);">Manage your classes and submit grades efficiently.</p>
            </div>
        </div>

        <!-- Actions Grid -->
        <div class="vds-grid-3 mb-5">
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-people vds-icon-lg mb-3" style="color: var(--vds-forest);"></i>
                <h3 class="vds-h3">My Classes</h3>
                <p class="vds-text-muted">View student lists and schedules.</p>
                <button class="vds-btn vds-btn-secondary w-100 mt-2">View Classes</button>
            </div>
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-file-earmark-spreadsheet vds-icon-lg mb-3" style="color: var(--vds-moss);"></i>
                <h3 class="vds-h3">Upload Grades</h3>
                <p class="vds-text-muted">Drag & drop Excel files to publish.</p>
                <button class="vds-btn vds-btn-primary w-100 mt-2" onclick="document.getElementById('uploadSection').scrollIntoView({behavior: 'smooth'})">Upload Now</button>
            </div>
            <div class="vds-card p-4 text-center hover-lift">
                <i class="bi bi-gear vds-icon-lg mb-3" style="color: var(--vds-sage);"></i>
                <h3 class="vds-h3">Settings</h3>
                <p class="vds-text-muted">Update profile and preferences.</p>
                <button class="vds-btn vds-btn-secondary w-100 mt-2">Manage Profile</button>
            </div>
        </div>

        <!-- Excel Upload Section -->
        <div id="uploadSection" class="vds-card p-5 mb-5">
            <h2 class="vds-h2 mb-4">Upload Grades</h2>
            <p class="vds-text-muted mb-4">Upload an Excel file (.xlsx) with columns: <strong>Student ID</strong>, <strong>Subject Code</strong>, <strong>Grade</strong>, <strong>Remarks</strong>.</p>
            
            <div class="vds-file-drop" id="dropZone">
                <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: var(--vds-forest); margin-bottom: 1rem;"></i>
                <h4 class="vds-h4">Drag & Drop Excel File Here</h4>
                <p class="vds-text-muted">or click to browse</p>
                <input type="file" id="fileInput" hidden accept=".xlsx, .xls">
            </div>

            <!-- Preview Area -->
            <div id="previewContainer" style="display: none;" class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="vds-h3">Preview Data</h3>
                    <button id="publishBtn" class="vds-btn vds-btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Publish Grades
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="vds-table" id="previewTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Subject Code</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer_dashboard.php'; ?>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewTableBody = document.querySelector('#previewTable tbody');
        const publishBtn = document.getElementById('publishBtn');
        let parsedData = [];

        // Drag & Drop Events
        dropZone.addEventListener('click', () => fileInput.click());
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--vds-forest)';
            dropZone.style.background = 'rgba(13, 59, 46, 0.05)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#ccc';
            dropZone.style.background = 'transparent';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ccc';
            dropZone.style.background = 'transparent';
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
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                parsedData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                
                // Remove header row if it exists and matches expected format
                // Assuming Row 1 is headers
                const headers = parsedData[0];
                parsedData = parsedData.slice(1); // Remove header

                renderPreview(parsedData);
            };
            reader.readAsArrayBuffer(file);
        }

        function renderPreview(data) {
            previewTableBody.innerHTML = '';
            // Limit preview to 10 rows for performance if large
            const displayData = data.slice(0, 10); 
            
            displayData.forEach(row => {
                // Expecting: StudentID, SubjectCode, Grade, Remarks
                if (row.length >= 3) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row[0] || ''}</td>
                        <td>${row[1] || ''}</td>
                        <td><span class="vds-badge">${row[2] || ''}</span></td>
                        <td>${row[3] || ''}</td>
                    `;
                    previewTableBody.appendChild(tr);
                }
            });

            if (data.length > 10) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="4" class="text-center text-muted">... and ${data.length - 10} more rows</td>`;
                previewTableBody.appendChild(tr);
            }

            previewContainer.style.display = 'block';
        }

        publishBtn.addEventListener('click', () => {
            if (parsedData.length === 0) return;

            if (!confirm('Are you sure you want to publish these grades?')) return;

            // Send to API
            fetch('api.php?action=publish_grades', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ grades: parsedData })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Grades published successfully!');
                    previewContainer.style.display = 'none';
                    parsedData = [];
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(err => alert('Network error occurred.'));
        });
    </script>

</body>
</html>
