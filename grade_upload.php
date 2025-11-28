<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Grades | KLD Grade System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="verdantDesignSystem.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <style>
        .vds-file-drop {
            border: 2px dashed var(--vds-sage);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            background: rgba(255,255,255,0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .vds-file-drop:hover {
            border-color: var(--vds-forest);
            background: rgba(13, 59, 46, 0.05);
        }
    </style>
</head>
<body class="vds-bg-vapor">

    <?php include 'navbar_dashboard.php'; ?>

    <div class="vds-container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="teacher_dashboard.php" class="vds-text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                <h1 class="vds-h2">Upload Grades</h1>
                <p class="vds-text-muted">Import grades via Excel file.</p>
            </div>
        </div>

        <div class="vds-card p-5 mb-5">
            <div class="text-center mb-4">
                <h2 class="vds-h2">Drag & Drop Upload</h2>
                <p class="vds-text-muted">Upload an Excel file (.xlsx) with columns: <strong>Student ID</strong>, <strong>Subject Code</strong>, <strong>Grade</strong>, <strong>Remarks</strong>.</p>
            </div>
            
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
            dropZone.style.borderColor = 'var(--vds-sage)';
            dropZone.style.background = 'rgba(255,255,255,0.5)';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--vds-sage)';
            dropZone.style.background = 'rgba(255,255,255,0.5)';
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
                const headers = parsedData[0];
                parsedData = parsedData.slice(1); // Remove header

                renderPreview(parsedData);
            };
            reader.readAsArrayBuffer(file);
        }

        function renderPreview(data) {
            previewTableBody.innerHTML = '';
            const displayData = data.slice(0, 10); 
            
            displayData.forEach(row => {
                if (row.length >= 3) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row[0] || ''}</td>
                        <td>${row[1] || ''}</td>
                        <td><span class="vds-pill vds-pill-pass">${row[2] || ''}</span></td>
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
