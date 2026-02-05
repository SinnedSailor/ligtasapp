<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POPS</title>
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
            background: #ffd700;
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
            color: #1C4D8D;
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
            color: #1C4D8D;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 14px;
            margin-top: 40px;
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
                <a href="<?= base_url('/ordinance') ?>" class="nav-item">
                    <i class="bi bi-file-earmark-arrow-up nav-icon"></i>
                    <span>Ordinance</span>
                </a>
                <a href="<?= base_url('/pops') ?>" class="nav-item active">
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
                color: #FFDE15;
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

            .upload-card {
                background: white;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 30px;
            }

            .upload-area {
                border: 2px dashed #FFDE15;
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

            .upload-area.dragover {
                background: #fffef5;
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
                transform: translateY(-2px);
            }

            .files-list {
                margin-top: 30px;
            }

            .files-list h3 {
                color: #333;
                font-size: 20px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .file-item {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 15px 20px;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                transition: all 0.3s;
            }

            .file-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-color: #002C76;
            }

            .file-info {
                display: flex;
                align-items: center;
                gap: 15px;
                flex: 1;
            }

            .file-icon {
                font-size: 24px;
                color: #002C76;
            }

            .file-details {
                flex: 1;
            }

            .file-name {
                color: #333;
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 5px;
            }

            .file-meta {
                color: #999;
                font-size: 12px;
            }

            .file-actions {
                display: flex;
                gap: 10px;
            }

            .action-btn {
                padding: 8px 16px;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.3s;
            }

            .btn-delete {
                background: #C9282D;
                color: white;
            }

            .btn-delete:hover {
                background: #9d1f22;
            }

            .submit-btn {
                background: #002C76;
                color: white;
                border: none;
                padding: 15px 40px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 18px;
                font-weight: 600;
                margin-top: 20px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .submit-btn:hover {
                background: #1a1f4d;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 44, 118, 0.4);
            }

            .submit-btn:disabled {
                background: #ccc;
                cursor: not-allowed;
                transform: none;
            }

            .alert {
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .alert-warning {
                background: #fff3cd;
                color: #856404;
                border: 1px solid #ffeeba;
            }
        </style>

        <div class="page-header">
            <h1>
                <i class="bi bi-shield-check"></i>
                POPS Plan Upload
            </h1>
            <p>Peace and Order and Public Safety Plan - Upload required POPS documents</p>
        </div>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Upload your POPS Plan documents (PDF, DOC, DOCX) to complete the submission.</span>
        </div>

        <div class="upload-card">
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <div class="upload-text">Click to upload or drag and drop</div>
                <div class="upload-hint">Supported formats: PDF, DOC, DOCX (Max 10MB each)</div>
                <input type="file" id="fileInput" class="file-input" multiple accept=".pdf,.doc,.docx" onchange="handleFiles(this.files)">
                <button class="upload-btn">Browse Files</button>
            </div>

            <div class="files-list" id="filesList" style="display: none;">
                <h3>
                    <i class="bi bi-files"></i>
                    Uploaded Documents (<span id="fileCount">0</span>)
                </h3>
                <div id="filesContainer"></div>
                
                <button class="submit-btn" id="submitBtn" disabled onclick="submitDocuments()">
                    <i class="bi bi-check-circle"></i>
                    <span>Submit POPS Plan</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let uploadedFiles = [];

        // Drag and drop handlers
        const uploadArea = document.getElementById('uploadArea');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            for (let file of files) {
                if (!validTypes.includes(file.type)) {
                    alert(`${file.name} is not a supported file type. Please upload PDF, DOC, or DOCX files.`);
                    continue;
                }

                if (file.size > maxSize) {
                    alert(`${file.name} exceeds the 10MB size limit.`);
                    continue;
                }

                uploadedFiles.push({
                    name: file.name,
                    size: formatFileSize(file.size),
                    date: new Date().toLocaleDateString(),
                    file: file
                });
            }

            renderFiles();
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function renderFiles() {
            const container = document.getElementById('filesContainer');
            const filesList = document.getElementById('filesList');
            const fileCount = document.getElementById('fileCount');
            const submitBtn = document.getElementById('submitBtn');

            if (uploadedFiles.length > 0) {
                filesList.style.display = 'block';
                fileCount.textContent = uploadedFiles.length;
                submitBtn.disabled = false;

                container.innerHTML = uploadedFiles.map((file, index) => `
                    <div class="file-item">
                        <div class="file-info">
                            <div class="file-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="file-details">
                                <div class="file-name">${file.name}</div>
                                <div class="file-meta">${file.size} • Uploaded on ${file.date}</div>
                            </div>
                        </div>
                        <div class="file-actions">
                            <button class="action-btn btn-delete" onclick="deleteFile(${index})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                filesList.style.display = 'none';
            }
        }

        function deleteFile(index) {
            uploadedFiles.splice(index, 1);
            renderFiles();
        }

        function submitDocuments() {
            if (uploadedFiles.length > 0) {
                // In a real app, you would upload files to server here
                alert('POPS Plan documents submitted successfully!');
                window.location.href = '<?= base_url('/dashboard') ?>';
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
    </div>
</body>
</html>
