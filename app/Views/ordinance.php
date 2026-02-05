<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordinance</title>
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
            background: #FFDE15;
            color: #002C76;
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
            background: rgba(0, 44, 118, 0.2);
            color: #002C76;
            border: 1px solid rgba(0, 44, 118, 0.3);
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: rgba(0, 44, 118, 0.3);
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
            background: #002C76;
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
            background: rgba(255, 222, 21, 0.3);
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
            background: rgba(255, 222, 21, 0.5);
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
            background: #FFDE15;
            color: #002C76;
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
            background: rgba(255, 222, 21, 0.1);
            border-left-color: #FFDE15;
        }

        .nav-item.active {
            color: white;
            background: rgba(255, 222, 21, 0.15);
            border-left-color: #FFDE15;
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
            color: #002C76;
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
            color: #002C76;
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
            color: #FFDE15;
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
            background: #002C76;
            color: white;
        }

        .btn-yes:hover {
            background: #1a1f4d;
        }

        .btn-no {
            background: #C9282D;
            color: white;
        }

        .btn-no:hover {
            background: #9d1f22;
        }
    </style>
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
                <a href="<?= base_url('/ordinance') ?>" class="nav-item active">
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
                max-width: 100%;
                margin: 0;
                padding: 30px;
            }

            .page-header {
                margin-bottom: 30px;
            }

            .page-header h1 {
                color: #002C76;
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

            .ordinances-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .ordinance-card {
                background: white;
                border-radius: 10px;
                padding: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s;
                border-left: 4px solid #002C76;
            }

            .ordinance-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            }

            .ordinance-header {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                margin-bottom: 15px;
            }

            .ordinance-icon {
                font-size: 32px;
                color: #002C76;
                flex-shrink: 0;
            }

            .ordinance-title {
                flex: 1;
            }

            .ordinance-title h3 {
                color: #002C76;
                font-size: 18px;
                margin-bottom: 5px;
            }

            .ordinance-number {
                color: #999;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .ordinance-content {
                color: #666;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 15px;
            }

            .ordinance-details {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .detail-item {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #666;
                font-size: 13px;
            }

            .detail-icon {
                color: #002C76;
                font-size: 16px;
            }

            .ordinance-footer {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .status-badge {
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .status-active {
                background: #d4edda;
                color: #155724;
            }

            .status-pending {
                background: #fff3cd;
                color: #856404;
            }

            .view-btn {
                background: #002C76;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.3s;
            }

            .view-btn:hover {
                background: #1a1f4d;
            }

            .upload-section {
                background: white;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 30px;
            }

            .upload-section h2 {
                color: #002C76;
                font-size: 20px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .upload-area {
                border: 2px dashed #002C76;
                border-radius: 10px;
                padding: 40px;
                text-align: center;
                background: #f8f9fa;
                transition: all 0.3s;
                cursor: pointer;
            }

            .upload-area:hover {
                background: #fffaf0;
                border-color: #FFD700;
            }

            .upload-icon {
                font-size: 48px;
                color: #002C76;
                margin-bottom: 20px;
            }

            .upload-text {
                color: #333;
                font-size: 18px;
                margin-bottom: 10px;
                font-weight: 600;
            }

            .upload-hint {
                color: #666;
                font-size: 14px;
            }

            .file-input {
                display: none;
            }

            .upload-btn {
                background: #002C76;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                margin-top: 20px;
                transition: all 0.3s;
            }

            .upload-btn:hover {
                background: #1a1f4d;
            }
        </style>

        <div class="upload-section">
            <h2>
                <i class="bi bi-cloud-arrow-up"></i>
                Upload Additional Ordinance Documents
            </h2>
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                </div>
                <div class="upload-text">Click to upload ordinance documents</div>
                <div class="upload-hint">Supported formats: PDF, DOC, DOCX (Max 10MB each)</div>
                <input type="file" id="fileInput" class="file-input" multiple accept=".pdf,.doc,.docx" onchange="handleFiles(this.files)">
                <button class="upload-btn">Browse Files</button>
            </div>
        </div>
    </div>

    <script>
        function viewOrdinance(type) {
            alert(`Viewing detailed ordinance information for: ${type}\n\nThis would open a detailed view with full ordinance text, implementation guidelines, and compliance checklist.`);
        }

        function handleFiles(files) {
            alert(`${files.length} file(s) selected for upload.\n\nIn production, these documents would be uploaded to the server for review and implementation.`);
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

        </div>
    </div>
</body>
</html>
