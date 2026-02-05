<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Restore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            position: relative;
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

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: #1C4D8D;
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header p {
            color: #666;
            font-size: 14px;
        }

        .backup-restore-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .action-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1C4D8D;
        }

        .action-icon {
            font-size: 36px;
            color: #1C4D8D;
        }

        .action-title h2 {
            color: #1C4D8D;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .action-title p {
            color: #666;
            font-size: 13px;
        }

        .action-content {
            margin-bottom: 25px;
        }

        .action-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .action-features {
            list-style: none;
            margin-bottom: 20px;
        }

        .action-features li {
            color: #666;
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-features li i {
            color: #28a745;
            font-size: 18px;
        }

        .action-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-backup {
            background: #28a745;
            color: white;
        }

        .btn-backup:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-restore {
            background: #1C4D8D;
            color: white;
        }

        .btn-restore:hover {
            background: #2563A8;
            transform: translateY(-2px);
        }

        .backup-history {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .backup-history h3 {
            color: #1C4D8D;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table thead {
            background: #f8f9fa;
        }

        .history-table th {
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            color: #666;
        }

        .history-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .history-actions {
            display: flex;
            gap: 5px;
        }

        .action-btn-small {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-download {
            background: #17a2b8;
            color: white;
        }

        .btn-download:hover {
            background: #138496;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
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
                <a href="<?= base_url('/incident-report') ?>" class="nav-item">
                    <i class="bi bi-exclamation-triangle nav-icon"></i>
                    <span>Incident Report</span>
                </a>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="content-wrapper">

    <div class="container">
        <div class="page-header">
            <h1>
                <i class="bi bi-cloud-arrow-up-fill"></i>
                Backup & Restore
            </h1>
            <p>Protect your data with automated backups and restore when needed</p>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <span>Regular backups ensure your data is safe. We recommend backing up before making significant changes.</span>
        </div>

        <div class="backup-restore-grid">
            <!-- Backup Card -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <div class="action-title">
                        <h2>Create Backup</h2>
                        <p>Save current system state</p>
                    </div>
                </div>
                <div class="action-content">
                    <p>Create a complete backup of your system data including:</p>
                    <ul class="action-features">
                        <li><i class="bi bi-check-circle-fill"></i> User accounts and profiles</li>
                        <li><i class="bi bi-check-circle-fill"></i> Incident reports and data</li>
                        <li><i class="bi bi-check-circle-fill"></i> Ordinance documents</li>
                        <li><i class="bi bi-check-circle-fill"></i> POPS Plan files</li>
                        <li><i class="bi bi-check-circle-fill"></i> System configurations</li>
                    </ul>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Backup may take several minutes depending on data size.</span>
                    </div>
                </div>
                <button class="action-button btn-backup" onclick="createBackup()">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Create Backup Now
                </button>
            </div>

            <!-- Restore Card -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="bi bi-cloud-arrow-down"></i>
                    </div>
                    <div class="action-title">
                        <h2>Restore Data</h2>
                        <p>Recover from previous backup</p>
                    </div>
                </div>
                <div class="action-content">
                    <p>Restore your system to a previous state from a backup file:</p>
                    <ul class="action-features">
                        <li><i class="bi bi-check-circle-fill"></i> Select from backup history</li>
                        <li><i class="bi bi-check-circle-fill"></i> Preview backup contents</li>
                        <li><i class="bi bi-check-circle-fill"></i> Partial or full restore</li>
                        <li><i class="bi bi-check-circle-fill"></i> Verify data integrity</li>
                        <li><i class="bi bi-check-circle-fill"></i> Rollback option available</li>
                    </ul>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Restoring will replace current data. Create a backup first!</span>
                    </div>
                </div>
                <button class="action-button btn-restore" onclick="document.getElementById('restoreFile').click()">
                    <i class="bi bi-cloud-arrow-down"></i>
                    Upload Backup File
                </button>
                <input type="file" id="restoreFile" style="display: none;" accept=".zip,.backup" onchange="handleRestore(event)">
            </div>
        </div>

        <!-- Backup History -->
        <div class="backup-history">
            <h3>
                <i class="bi bi-clock-history"></i>
                Backup History
            </h3>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="backupHistoryBody">
                    <tr>
                        <td>2026-02-03 10:30 AM</td>
                        <td>IWAS_backup_20260203_1030.zip</td>
                        <td>45.2 MB</td>
                        <td><span class="status-badge status-success">Success</span></td>
                        <td class="history-actions">
                            <button class="action-btn-small btn-download" onclick="downloadBackup('20260203_1030')">
                                <i class="bi bi-download"></i> Download
                            </button>
                            <button class="action-btn-small btn-restore" onclick="restoreBackup('20260203_1030')">
                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                            </button>
                            <button class="action-btn-small btn-delete" onclick="deleteBackup('20260203_1030')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-02-02 03:15 PM</td>
                        <td>IWAS_backup_20260202_1515.zip</td>
                        <td>43.8 MB</td>
                        <td><span class="status-badge status-success">Success</span></td>
                        <td class="history-actions">
                            <button class="action-btn-small btn-download" onclick="downloadBackup('20260202_1515')">
                                <i class="bi bi-download"></i> Download
                            </button>
                            <button class="action-btn-small btn-restore" onclick="restoreBackup('20260202_1515')">
                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                            </button>
                            <button class="action-btn-small btn-delete" onclick="deleteBackup('20260202_1515')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-02-01 09:00 AM</td>
                        <td>IWAS_backup_20260201_0900.zip</td>
                        <td>42.5 MB</td>
                        <td><span class="status-badge status-success">Success</span></td>
                        <td class="history-actions">
                            <button class="action-btn-small btn-download" onclick="downloadBackup('20260201_0900')">
                                <i class="bi bi-download"></i> Download
                            </button>
                            <button class="action-btn-small btn-restore" onclick="restoreBackup('20260201_0900')">
                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                            </button>
                            <button class="action-btn-small btn-delete" onclick="deleteBackup('20260201_0900')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function createBackup() {
            if (confirm('Create a backup of all system data? This may take several minutes.')) {
                // Show loading state
                alert('Creating backup... Please wait.');
                
                // In production, this would call the backup API
                setTimeout(() => {
                    alert('Backup created successfully!\n\nFile: IWAS_backup_' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '.zip\nSize: ~45 MB');
                    // Reload to show new backup in history
                    // location.reload();
                }, 2000);
            }
        }

        function handleRestore(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (confirm(`Restore from backup file: ${file.name}?\n\nWARNING: This will replace all current data!`)) {
                alert('Restoring from backup... Please wait.\n\nDo not close this window.');
                
                // In production, this would upload and restore from the file
                setTimeout(() => {
                    alert('Backup restored successfully!\n\nThe system will now reload.');
                    // window.location.reload();
                }, 3000);
            }
        }

        function downloadBackup(id) {
            alert('Downloading backup file: IWAS_backup_' + id + '.zip\n\nIn production, this would download the actual backup file.');
            // In production: window.location.href = '/api/backup/download/' + id;
        }

        function restoreBackup(id) {
            if (confirm('Restore from this backup?\n\nWARNING: Current data will be replaced!')) {
                alert('Restoring backup from ' + id + '...\n\nPlease wait.');
                
                setTimeout(() => {
                    alert('Backup restored successfully!');
                    // location.reload();
                }, 2000);
            }
        }

        function deleteBackup(id) {
            if (confirm('Delete this backup?\n\nThis action cannot be undone.')) {
                alert('Backup deleted: IWAS_backup_' + id + '.zip');
                // In production, remove the row and call delete API
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
