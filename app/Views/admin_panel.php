<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, rgba(0, 44, 118, 0.15), rgba(0, 44, 118, 0.04));
        border-radius: 12px;
        padding: 16px 20px;
    }

    .admin-card {
        border-left: 4px solid #002C76;
        box-shadow: 0 2px 10px rgba(9, 99, 126, 0.1);
    }

    .roles-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    .role-admin {
        background: #DC2626;
    }

    .role-focal {
        background: #0891B2;
    }

    .role-lgu {
        background: #002C76;
    }

    .role-province {
        background: #7C3AED;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .stat-item {
        background: rgba(9, 99, 126, 0.06);
        border: 1px solid rgba(9, 99, 126, 0.12);
        border-radius: 8px;
        text-align: center;
        padding: 12px;
    }

    .stat-number {
        font-size: 22px;
        font-weight: 700;
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
    }

    .table thead th {
        color: #002C76;
        font-weight: 600;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        max-width: 520px;
        width: 100%;
        animation: modalSlideIn 0.25s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header h3 {
        margin: 0;
        color: #002C76;
        font-size: 20px;
    }

    .modal-body p {
        color: #6c757d;
        margin-bottom: 12px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .modal-btn-primary {
        background: #002C76;
        color: #fff;
    }

    .modal-btn-secondary {
        background: #e5e7eb;
        color: #1f2937;
    }

    .success-icon {
        width: 64px;
        height: 64px;
        background: #10B981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .success-icon i {
        font-size: 30px;
        color: #fff;
    }

    .success-message {
        text-align: center;
        color: #002C76;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .success-detail {
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 16px;
    }

    .modal-error {
        background: #fee;
        color: #DC2626;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 13px;
        border-left: 4px solid #DC2626;
        margin-bottom: 12px;
        display: none;
    }

    .modal-error.show {
        display: block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header flex flex-wrap justify-between items-center mb-4">
    <div>
        <h3 class="page-title mb-1"><?= svg_icon('shield', 'w-5 h-5 mr-3 text-blue-900') ?><span class="text-xl font-semibold">User Management</span></h3>
        <div class="text-gray-500">Manage users, roles, and administrative access.</div>
    </div>
</div>

<div id="user-management-section" class="bg-white rounded-2xl shadow admin-card">
    <div class="p-6">
        <h4 class="text-lg font-semibold mb-4">User Management</h4>

        <div class="flex flex-wrap items-center gap-3 mb-4 mt-2">
            <div class="flex items-center rounded-full bg-gray-50 border border-gray-200 overflow-hidden" style="max-width:320px;">
                <div class="px-3 text-gray-500"><?= svg_icon('search', 'w-4 h-4') ?></div>
                <input type="text" id="userSearch" class="py-2 px-3 text-sm bg-transparent outline-none" placeholder="Search" onkeyup="filterUsers()" />
            </div>

            <div class="flex items-center rounded-full bg-gray-50 border border-gray-200 overflow-hidden" style="max-width:180px;">
                <div class="px-3 text-gray-500"><?= svg_icon('users', 'w-4 h-4') ?></div>
                <select id="roleFilter" class="py-2 pr-3 pl-1 text-sm bg-transparent outline-none" onchange="filterUsers()">
                    <option value="">Role</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="FOCAL">FOCAL</option>
                    <option value="LGU">LGU</option>
                    <option value="PROVINCE">PROVINCE</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody id="user-tbody" class="bg-white divide-y divide-gray-100">
                    <tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-400">Loading users...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="assignRoleModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3><?= svg_icon('users', 'w-5 h-5') ?> Assign Role</h3>
        </div>
        <div class="modal-body">
            <p>Assign a role to <strong id="modalUserName"></strong></p>
            <div id="roleModalError" class="modal-error"></div>
            <div class="form-group">
                <label for="roleSelect" class="text-sm font-medium text-gray-600 block mb-1">Select Role:</label>
                <select id="roleSelect" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">-- Select a role --</option>
                    <option value="1">ADMIN</option>
                    <option value="2">FOCAL</option>
                    <option value="3">LGU</option>
                    <option value="4">PROVINCE</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-secondary" onclick="closeRoleModal()">Cancel</button>
            <button class="modal-btn modal-btn-primary" onclick="submitRoleAssignment()">Assign Role</button>
        </div>
    </div>
</div>

<div id="successModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-body">
            <div class="success-icon">
                <?= svg_icon('check', 'w-4 h-4 text-white') ?>
            </div>
            <div class="success-message" id="successMessage">Role assigned successfully!</div>
            <div class="success-detail" id="successDetail"></div>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button class="modal-btn modal-btn-primary" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>
</div>

<div id="revokeAdminModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3><?= svg_icon('alert', 'w-5 h-5 text-red-600') ?> Confirm Action</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to <strong style="color: #DC2626;">revoke admin privileges</strong> from <strong id="revokeUserName"></strong>?</p>
            <p class="small">This will remove their admin status and clear their role assignment.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-secondary" onclick="closeRevokeModal()">Cancel</button>
            <button class="modal-btn modal-btn-primary" style="background-color: #DC2626;" onclick="confirmRevokeAdmin()">Revoke Admin</button>
        </div>
    </div>
</div>

<div id="grantAdminModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3><?= svg_icon('shield', 'w-5 h-5 text-green-500') ?> Confirm Action</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to <strong style="color: #10B981;">grant admin privileges</strong> to <strong id="grantUserName"></strong>?</p>
            <p class="small">This will give them full admin access and assign the ADMIN role.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-secondary" onclick="closeGrantModal()">Cancel</button>
            <button class="modal-btn modal-btn-primary" style="background-color: #10B981;" onclick="confirmGrantAdmin()">Grant Admin</button>
        </div>
    </div>
</div>

<div id="clearRoleModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3><?= svg_icon('alert', 'w-5 h-5 text-yellow-500') ?> Confirm Action</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to <strong style="color: #F59E0B;">clear the role</strong> for <strong id="clearUserName"></strong>?</p>
            <p class="small">The user will no longer have any assigned role in the system.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-secondary" onclick="closeClearRoleModal()">Cancel</button>
            <button class="modal-btn modal-btn-primary" style="background-color: #F59E0B;" onclick="confirmClearRole()">Clear Role</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadUsers();
        loadAdminStats();
    });

    function getRoleBadge(roleName) {
        if (!roleName) {
            return '<span class="text-muted small">No Role</span>';
        }

        const normalized = roleName.toUpperCase();
        const roleClassMap = {
            'ADMIN': 'role-badge role-admin',
            'FOCAL': 'role-badge role-focal',
            'LGU': 'role-badge role-lgu',
            'PROVINCE': 'role-badge role-province'
        };
        const badgeClass = roleClassMap[normalized] || 'role-badge role-focal';

        return `<span class="${badgeClass}">${normalized}</span>`;
    }

    function loadUsers() {
        fetch('<?= base_url('admin/getUsers') ?>')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('user-tbody');
                if (data.users && data.users.length > 0) {
                    tbody.innerHTML = data.users.map(user => {
                        const roleBadge = getRoleBadge(user.role_name);
                        // Only show edit and disable icons for actions
                        return `
                            <tr>
                                <td class="px-4 py-4"><strong>${user.first_name} ${user.last_name}</strong></td>
                                <td class="px-4 py-4">${user.email}</td>
                                <td class="px-4 py-4">${user.username}</td>
                                <td class="px-4 py-4">${roleBadge}</td>
                                <td class="px-4 py-4 flex flex-wrap gap-2">
                                    <button class="px-2.5 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-100" title="Edit User" onclick="editUser(${user.id})">
                                        <?= svg_icon('pencil', 'w-4 h-4') ?>
                                    </button>
                                    <button class="px-2.5 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-100" title="Disable User" onclick="disableUser(${user.id})">
                                        <?= svg_icon('users', 'w-4 h-4') ?>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No users found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('user-tbody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading users</td></tr>';
            });
    }

    let currentUserId = null;
    let currentToggleUserId = null;

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
                const roleNames = {1: 'ADMIN', 2: 'FOCAL', 3: 'LGU', 4: 'PROVINCE'};
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
                showSuccessModal('Role cleared successfully!', "User's role has been removed.");
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    document.getElementById('clearRoleModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeClearRoleModal();
        }
    });

    function revokeAdminConfirm(userId, userName) {
        currentToggleUserId = userId;
        document.getElementById('revokeUserName').textContent = userName;
        document.getElementById('revokeAdminModal').classList.add('active');
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

    function filterUsers() {
        const searchValue = document.getElementById('userSearch').value.toLowerCase();
        const roleValue = document.getElementById('roleFilter').value;
        const tbody = document.getElementById('user-tbody');
        const rows = tbody.getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            if (cells.length > 0) {
                const name = cells[0].textContent.toLowerCase();
                const email = cells[1].textContent.toLowerCase();
                const username = cells[2].textContent.toLowerCase();
                const role = cells[3].textContent.trim().toUpperCase();
                const matchesSearch = name.includes(searchValue) || email.includes(searchValue) || username.includes(searchValue);
                const matchesRole = !roleValue || role === roleValue;
                row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
            }
        }
    }

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

    function editUser(userId) {
        // Find the user row and get the name
        const row = Array.from(document.getElementById('user-tbody').getElementsByTagName('tr')).find(tr => {
            const editBtn = tr.querySelector('button[onclick^="editUser("]');
            return editBtn && editBtn.getAttribute('onclick') === `editUser(${userId})`;
        });
        if (!row) return;
        const name = row.querySelector('td').textContent.trim();
        document.getElementById('modalUserName').textContent = name;
        document.getElementById('roleSelect').value = '';
        currentUserId = userId;
        document.getElementById('assignRoleModal').classList.add('active');
    }

    function disableUser(userId) {
        // Find the user row and get the role
        const row = Array.from(document.getElementById('user-tbody').getElementsByTagName('tr')).find(tr => {
            const disableBtn = tr.querySelector('button[onclick^="disableUser("]');
            return disableBtn && disableBtn.getAttribute('onclick') === `disableUser(${userId})`;
        });
        if (!row) return;
        const role = row.querySelectorAll('td')[3].textContent.trim().toUpperCase();
        if (role === 'ADMIN') {
            showSuccessModal('Cannot disable admin account!', 'Admin accounts cannot be disabled.');
            return;
        }
        if (!confirm('Are you sure you want to disable this user account?')) return;
        // Call backend to disable
        const formData = new FormData();
        formData.append('user_id', userId);
        fetch('<?= base_url('admin/disableUser') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('User disabled!', 'The user account has been disabled.');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
</script>
<?= $this->endSection() ?>
