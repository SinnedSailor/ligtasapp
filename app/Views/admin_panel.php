<?php
// app/Views/admin_panel.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - IWAS</title>
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
            z-index: 999;
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
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            position: relative;
        }

        .profile-avatar:hover {
            background: rgba(255, 222, 21, 0.5);
            transform: scale(1.05);
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

        /* Navigation Menu */
        .nav-menu {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            gap: 15px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #FFDE15;
            color: white;
        }

        .nav-item.active {
            background: rgba(255, 222, 21, 0.1);
            border-left-color: #FFDE15;
            color: white;
            font-weight: 600;
        }

        .nav-icon {
            font-size: 18px;
            min-width: 20px;
        }

        /* Content Wrapper */
        .content-wrapper {
            margin-left: 250px;
            flex: 1;
            overflow-y: auto;
        }

        .container {
            padding: 40px;
            max-width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #002C76;
            font-size: 32px;
        }

        .back-btn {
            background-color: #666;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: #555;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .admin-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 5px solid #002C76;
        }

        .admin-card h4 {
            color: #002C76;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .admin-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .btn {
            background-color: #002C76;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
            width: 100%;
        }

        .btn:hover {
            background-color: #001a47;
        }

        .badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .badge-admin { background-color: #DC2626; color: white; }
        .badge-focal { background-color: #0891B2; color: white; }
        .badge-lgu { background-color: #2563EB; color: white; }
        .badge-provincial { background-color: #7C3AED; color: white; }

        .roles-container {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
        }

        .user-table-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 40px;
        }

        .user-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .user-table-header h3 {
            color: #002C76;
            margin: 0;
        }

        .search-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 250px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #002C76;
        }

        .search-input::placeholder {
            color: #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            border-bottom: 2px solid #E5E7EB;
            background-color: #f9fafb;
        }

        th {
            padding: 15px 12px;
            text-align: left;
            color: #002C76;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        .btn-small {
            background-color: #2563EB;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
            transition: background 0.3s;
        }

        .btn-small:hover {
            background-color: #1d4ed8;
        }

        .btn-small-danger {
            background-color: #EF4444;
        }

        .btn-small-danger:hover {
            background-color: #DC2626;
        }

        .btn-small-success {
            background-color: #10B981;
        }

        .btn-small-success:hover {
            background-color: #059669;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .error {
            color: #DC2626;
            text-align: center;
            padding: 20px;
            background-color: #fee;
            border-radius: 5px;
        }

        .footer {
            background: #002C76;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            margin-left: 250px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .stat-item {
            background-color: #f0f0f0;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 600;
            color: #002C76;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #002C76;
            margin: 0;
            font-size: 20px;
        }

        .modal-body {
            margin-bottom: 25px;
        }

        .modal-body p {
            color: #666;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #002C76;
            font-weight: 500;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background-color: white;
            color: #333;
        }

        .form-group select:focus {
            outline: none;
            border-color: #002C76;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .modal-btn-primary {
            background-color: #002C76;
            color: white;
        }

        .modal-btn-primary:hover {
            background-color: #001a4d;
        }

        .modal-btn-secondary {
            background-color: #e5e7eb;
            color: #333;
        }

        .modal-btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Success Modal */
        .success-icon {
            width: 60px;
            height: 60px;
            background-color: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon i {
            font-size: 30px;
            color: white;
        }

        .success-message {
            text-align: center;
            color: #002C76;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .success-detail {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        /* Error Message in Modal */
        .modal-error {
            background-color: #fee;
            color: #DC2626;
            padding: 10px 12px;
            border-radius: 5px;
            font-size: 13px;
            border-left: 4px solid #DC2626;
            margin-bottom: 15px;
            display: none;
        }

        .modal-error.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-title">Integrated Water Safety Program</div>
        <div class="navbar-right">
            <span><?= session()->get('first_name') . ' ' . session()->get('last_name') ?></span>
            <a href="<?= base_url('/logout') ?>" class="logout-btn" onclick="return confirm('Are you sure you want to logout?');">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <!-- Profile Section -->
            <div class="profile-section">
                <a href="<?= base_url('/user-profile') ?>" class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
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
                <?php if (session()->get('is_admin')): ?>
                    <a href="<?= base_url('/admin-panel') ?>" class="nav-item active">
                        <i class="bi bi-shield-lock nav-icon"></i>
                        <span>Admin Panel</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="container">
                <div class="header">
                    <h1><i class="bi bi-shield-lock" style="margin-right: 10px;"></i>Admin Panel</h1>
                    <a href="<?= base_url('/dashboard') ?>" class="back-btn">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Admin Cards -->
                <div class="admin-grid">
                    <!-- User Management Card -->
                    <div class="admin-card">
                        <h4><i class="bi bi-people-fill" style="margin-right: 8px;"></i>User Management</h4>
                        <p>Manage all users in the system, assign roles, and control admin privileges.</p>
                        <button class="btn" onclick="document.getElementById('user-management-section').scrollIntoView({behavior: 'smooth'});">
                            View Users
                        </button>
                    </div>

                    <!-- System Roles Card -->
                    <div class="admin-card">
                        <h4><i class="bi bi-shield-lock" style="margin-right: 8px;"></i>System Roles</h4>
                        <p>Available roles in the system:</p>
                        <div class="roles-container">
                            <span class="badge badge-admin">ADMIN</span>
                            <span class="badge badge-focal">FOCAL</span>
                            <span class="badge badge-lgu">LGU</span>
                            <span class="badge badge-provincial">PROVINCIAL</span>
                        </div>
                    </div>

                    <!-- System Statistics Card -->
                    <div class="admin-card">
                        <h4><i class="bi bi-bar-chart-fill" style="margin-right: 8px;"></i>System Statistics</h4>
                        <div id="admin-stats" class="stats-row">
                            <div class="stat-item">
                                <div class="stat-number">-</div>
                                <div class="stat-label">Total Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">-</div>
                                <div class="stat-label">Admin Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">-</div>
                                <div class="stat-label">Regular Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">-</div>
                                <div class="stat-label">Unassigned</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management Table -->
                <div id="user-management-section" class="user-table-section">
                    <div class="user-table-header">
                        <h3>User Management</h3>
                        <div class="search-controls">
                            <input type="text" id="userSearch" class="search-input" placeholder="Search by name, email, or username..." onkeyup="filterUsers()">
                            <button class="btn-small" onclick="loadUsers()" style="width: auto;">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-tbody">
                            <tr><td colspan="5" class="loading">Loading users...</td></tr>
                        </tbody>
            </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2026 Integrated Water Safety Program. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Assign Role Modal -->
    <div id="assignRoleModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-person-badge"></i> Assign Role</h3>
            </div>
            <div class="modal-body">
                <p>Assign a role to <strong id="modalUserName"></strong></p>
                <div id="roleModalError" class="modal-error"></div>
                <div class="form-group">
                    <label for="roleSelect">Select Role:</label>
                    <select id="roleSelect" class="form-control">
                        <option value="">-- Select a role --</option>
                        <option value="1">ADMIN</option>
                        <option value="2">FOCAL</option>
                        <option value="3">LGU</option>
                        <option value="4">PROVINCIAL</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="closeRoleModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="submitRoleAssignment()">Assign Role</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-body">
                <div class="success-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="success-message" id="successMessage">Role assigned successfully!</div>
                <div class="success-detail" id="successDetail"></div>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button class="modal-btn modal-btn-primary" onclick="closeSuccessModal()">OK</button>
            </div>
        </div>
    </div>

    <!-- Revoke Admin Confirmation Modal -->
    <div id="revokeAdminModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-exclamation-triangle" style="color: #DC2626;"></i> Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong style="color: #DC2626;">revoke admin privileges</strong> from <strong id="revokeUserName"></strong>?</p>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">This will remove their admin status and clear their role assignment.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="closeRevokeModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" style="background-color: #DC2626;" onclick="confirmRevokeAdmin()">Revoke Admin</button>
            </div>
        </div>
    </div>

    <!-- Grant Admin Confirmation Modal -->
    <div id="grantAdminModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-shield-check" style="color: #10B981;"></i> Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong style="color: #10B981;">grant admin privileges</strong> to <strong id="grantUserName"></strong>?</p>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">This will give them full admin access and assign the ADMIN role.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="closeGrantModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" style="background-color: #10B981;" onclick="confirmGrantAdmin()">Grant Admin</button>
            </div>
        </div>
    </div>

    <!-- Clear Role Confirmation Modal -->
    <div id="clearRoleModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-exclamation-triangle" style="color: #F59E0B;"></i> Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong style="color: #F59E0B;">clear the role</strong> for <strong id="clearUserName"></strong>?</p>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">The user will no longer have any assigned role in the system.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="closeClearRoleModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" style="background-color: #F59E0B;" onclick="confirmClearRole()">Clear Role</button>
            </div>
        </div>
    </div>

    <script>
        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            loadAdminStats();
        });

        // Filter users based on search input
        function filterUsers() {
            const searchValue = document.getElementById('userSearch').value.toLowerCase();
            const tbody = document.getElementById('user-tbody');
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                
                if (cells.length > 0) {
                    const name = cells[0].textContent.toLowerCase();
                    const email = cells[1].textContent.toLowerCase();
                    const username = cells[2].textContent.toLowerCase();
                    
                    if (name.includes(searchValue) || email.includes(searchValue) || username.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }

        function loadUsers() {
            fetch('<?= base_url('admin/getUsers') ?>')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('user-tbody');
                    if (data.users && data.users.length > 0) {
                        tbody.innerHTML = data.users.map(user => `
                            <tr>
                                <td><strong>${user.first_name} ${user.last_name}</strong></td>
                                <td>${user.email}</td>
                                <td>${user.username}</td>
                                <td>
                                    ${user.role_name ? `<span class="badge badge-focal">${user.role_name}</span>` : '<span style="color: #999; font-size: 12px;">No Role</span>'}
                                </td>
                                <td>
                                    <button class="btn-small" onclick="openRoleModal(${user.id}, '${user.first_name} ${user.last_name}')">
                                        Assign Role
                                    </button>
                                    ${user.role_id ? `
                                        <button class="btn-small btn-small-danger" onclick="clearRoleConfirm(${user.id}, '${user.first_name} ${user.last_name}')">
                                            Clear Role
                                        </button>
                                    ` : ''}
                                    ${user.id !== '<?= session()->get('user_id') ?>' && user.is_admin === 1 ? `
                                        <button class="btn-small btn-small-danger" onclick="revokeAdminConfirm(${user.id}, '${user.first_name} ${user.last_name}')">
                                            Revoke Admin
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="loading">No users found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('user-tbody').innerHTML = '<tr><td colspan="6" class="error">Error loading users</td></tr>';
                });
        }

        let currentUserId = null;
        let currentToggleUserId = null;
        let currentToggleUserName = '';
        let isCurrentlyAdmin = false;

        function openRoleModal(userId, userName) {
            currentUserId = userId;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('roleSelect').value = '';
            document.getElementById('assignRoleModal').classList.add('active');
        }

        function closeRoleModal() {
            document.getElementById('assignRoleModal').classList.remove('active');
            currentUserId = null;
        }

        function submitRoleAssignment() {
            const roleId = document.getElementById('roleSelect').value;
            const errorDiv = document.getElementById('roleModalError');
            
            if (!roleId) {
                errorDiv.textContent = 'Please select a role';
                errorDiv.classList.add('show');
                return;
            }
            
            errorDiv.classList.remove('show');
            assignRole(currentUserId, roleId);
            closeRoleModal();
        }

        // Close modal when clicking outside
        document.getElementById('assignRoleModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeRoleModal();
            }
        });

        function showSuccessModal(message, detail = '') {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successDetail').textContent = detail;
            document.getElementById('successModal').classList.add('active');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
            loadUsers();
            loadAdminStats();
        }

        // Close success modal when clicking outside
        document.getElementById('successModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeSuccessModal();
            }
        });

        function assignRole(userId, roleId) {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('role_id', roleId);

            fetch('<?= base_url('admin/assignRole') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const roleNames = {1: 'ADMIN', 2: 'FOCAL', 3: 'LGU', 4: 'PROVINCIAL'};
                    showSuccessModal('Role assigned successfully!', `User has been assigned the ${roleNames[roleId]} role.`);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Clear Role Functions
        function clearRoleConfirm(userId, userName) {
            currentToggleUserId = userId;
            document.getElementById('clearUserName').textContent = userName;
            document.getElementById('clearRoleModal').classList.add('active');
        }

        function closeClearRoleModal() {
            document.getElementById('clearRoleModal').classList.remove('active');
        }

        function confirmClearRole() {
            closeClearRoleModal();
            clearRole(currentToggleUserId);
        }

        function clearRole(userId) {
            const formData = new FormData();
            formData.append('user_id', userId);

            fetch('<?= base_url('admin/clearRole') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Role cleared successfully!', `User's role has been removed.`);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Close clear role modal when clicking outside
        document.getElementById('clearRoleModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeClearRoleModal();
            }
        });

        // Revoke/Grant Admin Modal Functions  
        function revokeAdminConfirm(userId, userName) {
            currentToggleUserId = userId;
            document.getElementById('revokeUserName').textContent = userName;
            document.getElementById('revokeAdminModal').classList.add('active');
        }

        function toggleAdmin(userId, isAdmin, userName) {
            currentToggleUserId = userId;
            currentToggleUserName = userName;
            isCurrentlyAdmin = isAdmin;

            if (isAdmin) {
                // Show revoke confirmation modal
                document.getElementById('revokeUserName').textContent = userName;
                document.getElementById('revokeAdminModal').classList.add('active');
            } else {
                // Show grant confirmation modal
                document.getElementById('grantUserName').textContent = userName;
                document.getElementById('grantAdminModal').classList.add('active');
            }
        }

        function closeRevokeModal() {
            document.getElementById('revokeAdminModal').classList.remove('active');
        }

        function closeGrantModal() {
            document.getElementById('grantAdminModal').classList.remove('active');
        }

        function confirmRevokeAdmin() {
            closeRevokeModal();
            executeToggleAdmin('revokeAdmin', 'revoked');
        }

        function confirmGrantAdmin() {
            closeGrantModal();
            executeToggleAdmin('grantAdmin', 'granted');
        }

        function executeToggleAdmin(endpoint, actionText) {
            const formData = new FormData();
            formData.append('user_id', currentToggleUserId);

            fetch(`<?= base_url('admin/') ?>${endpoint}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Admin status updated!', `Admin privileges have been ${actionText}.`);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Close modals when clicking outside
        document.getElementById('revokeAdminModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeRevokeModal();
            }
        });

        document.getElementById('grantAdminModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeGrantModal();
            }
        });

        function loadAdminStats() {
            fetch('<?= base_url('admin/getStats') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statsDiv = document.getElementById('admin-stats');
                        statsDiv.innerHTML = `
                            <div class="stat-item">
                                <div class="stat-number">${data.totalUsers}</div>
                                <div class="stat-label">Total Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">${data.adminUsers}</div>
                                <div class="stat-label">Admin Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">${data.regularUsers}</div>
                                <div class="stat-label">Regular Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">${data.unassignedRoles}</div>
                                <div class="stat-label">No Role Assigned</div>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error loading stats:', error));
        }
    </script>
</body>
</html>
