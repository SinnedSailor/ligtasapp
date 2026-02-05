<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #1C4D8D;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 60px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-title {
            font-size: 24px;
            font-weight: 600;
        }

        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-email {
            font-size: 14px;
            opacity: 0.9;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 60px);
            overflow: hidden;
            margin-top: 60px;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 250px;
            background: #1C4D8D;
            padding: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            position: fixed;
            height: calc(100vh - 60px);
            left: 0;
            display: flex;
            flex-direction: column;
        }

        /* Profile Section */
        .profile-section {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
            color: white;
        }

        .profile-avatar:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
        }

        .profile-avatar:hover::after {
            content: 'Edit Profile';
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }

        .profile-edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #ffd700;
            color: #1C4D8D;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid white;
        }

        .profile-name {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 14px;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #ffd700;
        }

        .nav-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #ffd700;
        }

        .nav-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            margin-left: 250px;
            height: calc(100vh - 60px);
        }

        @media (max-width: 768px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .nav-menu {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .nav-item {
                padding: 12px 15px;
                font-size: 12px;
                gap: 8px;
            }

            .nav-item span:last-child {
                display: none;
            }
        }

        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .content-card h1 {
            color: #1e3c72;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .content-card p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .coming-soon {
            font-size: 48px;
            margin: 30px 0;
            color: #2a5298;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 14px;
            margin-top: 40px;
        }
        /* Confirmation Modal Styles */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .confirmation-modal.show {
            display: flex;
        }

        .confirmation-dialog {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            min-width: 350px;
        }

        .confirmation-dialog h2 {
            color: #1C4D8D;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .confirmation-dialog p {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .confirmation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-yes, .btn-no {
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            min-width: 120px;
        }

        .btn-yes {
            background: #1C4D8D;
            color: white;
        }

        .btn-yes:hover {
            background: #2563A8;
        }

        .btn-no {
            background: #f5222d;
            color: white;
        }

        .btn-no:hover {
            background: #d9001b;
        }    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Integrated Water Safety Program</div>
        <div class="navbar-right">
            <a href="#" class="logout-btn" onclick="showLogoutConfirmation(event)">Logout</a>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="confirmation-modal" id="logoutModal">
        <div class="confirmation-dialog">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            <div class="confirmation-buttons">
                <button class="btn-yes" onclick="confirmLogout()">Yes</button>
                <button class="btn-no" onclick="cancelLogout()">No</button>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <!-- Profile Section -->
            <div class="profile-section">
                <a href="<?= base_url('/user-profile') ?>" class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                    <span class="profile-edit-icon"><i class="bi bi-pencil-fill"></i></span>
                </a>
                <div class="profile-name"><?= strtoupper(session()->get('username') ?? 'User') ?></div>
                <div class="profile-email"><?= session()->get('role_name') ?? 'No Role' ?></div>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="<?= base_url('/dashboard') ?>" class="nav-item">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('/ordinance') ?>" class="nav-item">
                    <i class="bi bi-file-earmark-arrow-up nav-icon"></i>
                    <span>Ordinance</span>
                </a>
                <a href="<?= base_url('/pops') ?>" class="nav-item">
                    <i class="bi bi-shield-check nav-icon"></i>
                    <span>POPS Plan</span>
                </a>
                <a href="<?= base_url('/incident-report') ?>" class="nav-item active">
                    <i class="bi bi-exclamation-triangle nav-icon"></i>
                    <span>Incident Report</span>
                </a>
                <?php if (session()->get('is_admin')): ?>
                <a href="<?= base_url('/admin-panel') ?>" class="nav-item">
                    <i class="bi bi-shield-lock nav-icon"></i>
                    <span>Admin Panel</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="content-wrapper">

    <div class="container">
        <style>
            .container {
                max-width: 1400px;
                margin: 40px auto;
                padding: 0 40px;
            }

            .import-section {
                background: white;
                border-radius: 10px;
                padding: 25px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 40px;
            }

            .import-section h2 {
                color: #1C4D8D;
                font-size: 22px;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 3px solid #1C4D8D;
            }

            .import-controls {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .file-input-wrapper {
                position: relative;
                overflow: hidden;
                display: inline-block;
            }

            .file-input-wrapper input[type=file] {
                position: absolute;
                left: -9999px;
            }

            .import-btn {
                background: #1C4D8D;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 600;
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .import-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 82, 152, 0.4);
            }

            .file-name {
                color: #666;
                font-size: 14px;
                margin-left: 10px;
            }

            .table-section {
                background: white;
                border-radius: 10px;
                padding: 25px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 40px;
                overflow-x: auto;
            }

            .table-section h2 {
                color: #1C4D8D;
                font-size: 22px;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 3px solid #1C4D8D;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table thead {
                background: #1C4D8D;
                color: white;
            }

            table th {
                padding: 12px;
                text-align: left;
                font-weight: 600;
                border: 1px solid #ddd;
            }

            table td {
                padding: 12px;
                border: 1px solid #ddd;
                color: #333;
            }

            table tbody tr:nth-child(even) {
                background: #f9f9f9;
            }

            table tbody tr:hover {
                background: #f0f4ff;
            }

            .edit-btn, .delete-btn, .save-btn, .cancel-btn {
                padding: 6px 12px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 600;
                font-size: 12px;
                transition: all 0.2s;
                margin-right: 4px;
            }

            .edit-btn {
                background: #1C4D8D;
                color: white;
            }

            .edit-btn:hover {
                background: #154470;
            }

            .delete-btn {
                background: #f5222d;
                color: white;
            }

            .delete-btn:hover {
                background: #d9001b;
            }

            .save-btn {
                background: #52c41a;
                color: white;
            }

            .save-btn:hover {
                background: #3f8600;
            }

            .cancel-btn {
                background: #bfbfbf;
                color: white;
            }

            .cancel-btn:hover {
                background: #8c8c8c;
            }

            .table-input {
                width: 100%;
                padding: 6px;
                border: 1px solid #1C4D8D;
                border-radius: 4px;
                font-size: 12px;
            }

            .empty-message {
                text-align: center;
                padding: 40px;
                color: #999;
                font-size: 16px;
            }
        </style>

        <h1 style="color: #1C4D8D; font-size: 32px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-table"></i>
            Data Management
        </h1>

        <!-- Excel Import Section -->
        <div class="import-section">
            <div class="import-controls">
                <div class="file-input-wrapper">
                    <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" />
                    <button class="import-btn" onclick="document.getElementById('excelFile').click()">
                        <i class="bi bi-file-earmark-excel"></i> Import Excel File
                    </button>
                </div>
                <span class="file-name" id="fileName">No file selected</span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-section">
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="import-btn" onclick="addNewRow()">
                    <i class="bi bi-plus-circle"></i> Add New Row
                </button>
                <button class="import-btn" onclick="returnWork()" style="background: #ece03d;">
                    <i class="bi bi-arrow-counterclockwise"></i> Return Submission
                </button>
            </div>
            <div id="tableContainer">
                <table>
                    <thead>
                        <tr>
                            <th>INDICATOR</th>
                            <th>TARGET</th>
                            <th>STRAT</th>
                            <th>TITLE</th>
                            <th>OFFICERS PRIMARY RESPONSIBLE</th>
                            <th>START DATE OF IMPLEMENTATION</th>
                            <th>COMPLETION</th>
                            <th>EXPECTED OUTPUT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="9" class="empty-message">No data yet. Upload an Excel file or click "Add New Row" to add data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let tableData = [];

        // File Upload Handler
        document.getElementById('excelFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            document.getElementById('fileName').textContent = `Selected: ${file.name}`;

            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const data = new Uint8Array(event.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet);

                    // Add imported data to existing tableData
                    tableData = tableData.concat(jsonData);
                    renderTable();
                    alert(`Successfully imported ${jsonData.length} rows!`);
                } catch (error) {
                    alert('Error reading file: ' + error.message);
                    document.getElementById('fileName').textContent = 'No file selected';
                }
            };
            reader.readAsArrayBuffer(file);
        });

        function renderTable() {
            const tbody = document.getElementById('tableBody');

            if (tableData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="empty-message">No data yet. Upload an Excel file or click "Add New Row" to add data.</td></tr>';
                return;
            }

            const columns = ['INDICATOR', 'TARGET', 'STRAT', 'TITLE', 'OFFICERS PRIMARY RESPONSIBLE', 'START DATE OF IMPLEMENTATION', 'COMPLETION', 'EXPECTED OUTPUT'];

            let html = '';
            tableData.forEach((row, index) => {
                html += `<tr id="row-${index}">`;
                columns.forEach(col => {
                    const value = row[col] || '';
                    html += `<td>
                        <span class="display-${index}-${col.replace(/ /g, '_')}">${value}</span>
                        <input type="text" class="input-${index}-${col.replace(/ /g, '_')} table-input" value="${value}" style="display:none;" />
                    </td>`;
                });
                html += `<td>
                    <button class="edit-btn" onclick="editRow(${index})" id="edit-${index}"><i class="bi bi-pencil-square"></i> Edit</button>
                    <button class="save-btn" onclick="saveRow(${index})" style="display:none;" id="save-${index}"><i class="bi bi-check-circle"></i> Save</button>
                    <button class="cancel-btn" onclick="cancelEdit(${index})" style="display:none;" id="cancel-${index}"><i class="bi bi-x-circle"></i> Cancel</button>
                    <button class="delete-btn" onclick="deleteRow(${index})"><i class="bi bi-trash"></i> Delete</button>
                </td></tr>`;
            });

            tbody.innerHTML = html;
        }

        function addNewRow() {
            const newRow = {
                'Indicator': '',
                'Target': '',
                'Strat': '',
                'Title': '',
                'Officers Primary Responsible': '',
                'Start Date of Implementation': '',
                'Completion': '',
                'Expected Output': ''
            };
            tableData.push(newRow);
            renderTable();
            
            // Automatically edit the new row
            const newIndex = tableData.length - 1;
            setTimeout(() => editRow(newIndex), 100);
        }

        function editRow(index) {
            const columns = ['Indicator', 'Target', 'Strat', 'Title', 'Officers Primary Responsible', 'Start Date of Implementation', 'Completion', 'Expected Output'];

            columns.forEach(col => {
                const colKey = col.replace(/ /g, '_');
                const display = document.querySelector(`.display-${index}-${colKey}`);
                const input = document.querySelector(`.input-${index}-${colKey}`);
                if (display) display.style.display = 'none';
                if (input) input.style.display = 'block';
            });

            document.getElementById(`edit-${index}`).style.display = 'none';
            document.getElementById(`save-${index}`).style.display = 'inline-block';
            document.getElementById(`cancel-${index}`).style.display = 'inline-block';
        }

        function saveRow(index) {
            const columns = ['Indicator', 'Target', 'Strat', 'Title', 'Officers Primary Responsible', 'Start Date of Implementation', 'Completion', 'Expected Output'];

            columns.forEach(col => {
                const colKey = col.replace(/ /g, '_');
                const input = document.querySelector(`.input-${index}-${colKey}`);
                if (input) {
                    tableData[index][col] = input.value;
                }
            });

            renderTable();
            alert('Row saved successfully!');
        }

        function cancelEdit(index) {
            renderTable();
        }

        function returnWork() {
            if (confirm('Are you sure you want to return this submission? This will notify the submitter that corrections are needed.')) {
                alert('Submission returned successfully! The user will be notified to make corrections.');
                // In production, this would send a notification to the submitter
            }
        }

        function returnWork() {
            if (confirm('Are you sure you want to return this submission? This will notify the submitter that corrections are needed.')) {
                alert('Submission returned successfully! The user will be notified to make corrections.');
                // In production, this would send a notification to the submitter
            }
        }

        function deleteRow(index) {
            if (confirm('Are you sure you want to delete this row?')) {
                tableData.splice(index, 1);
                renderTable();
                alert('Row deleted successfully!');
            }
        }
    </script>

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById('logoutModal').classList.add('show');
        }

        function confirmLogout() {
            window.location.href = '<?= base_url('/logout') ?>';
        }

        function cancelLogout() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        // Close modal when clicking outside of it
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                cancelLogout();
            }
        });
    </script>

    <div class="footer">
        <p>&copy; 2026 Integrated Water Safety Program. All rights reserved.</p>
    </div>
        </div>
    </div>
</body>
</html>
