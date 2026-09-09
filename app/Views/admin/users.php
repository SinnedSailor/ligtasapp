<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4">
            <div class="flex items-start justify-between gap-4 bg-green-50 border border-green-200 text-green-800 rounded-md p-4">
                <div><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="text-green-800 font-bold" onclick="this.closest('.mb-4').remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4">
            <div class="flex items-start justify-between gap-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-4">
                <div><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="text-red-800 font-bold" onclick="this.closest('.mb-4').remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow">
        <div class="border-b px-6 py-4">
            <h5 class="text-lg font-semibold text-gray-700 mb-0">All Users</h5>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Province</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Municipality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-400">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <?php $isActive = isset($user['is_active']) ? (int) $user['is_active'] === 1 : true; ?>
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <?= htmlspecialchars(
                                            mb_convert_case(
                                                trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                                                MB_CASE_TITLE, 'UTF-8'
                                            )
                                        ) ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['email'] ?? '—') ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="px-4 py-4 text-sm">
                                        <?php if ($user['role_name']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= htmlspecialchars($user['role_name']) ?></span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Role</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <?php if ($user['is_admin']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Regular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <?php if ($isActive): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['province'] ?? '-') ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['municipality'] ?? '-') ?></td>
                                    <td class="px-4 py-4 text-sm flex flex-wrap gap-2">
                                        <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="openAssignRoleModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username'])) ?>', <?= $user['role_id'] ?? 'null' ?>)">
                                            Assign Role
                                        </button>
                                        <button type="button" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700" onclick="openEditUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['first_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($user['last_name'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($user['email'] ?? '')) ?>')">
                                            Edit
                                        </button>
                                        <button type="button" class="px-3 py-1.5 bg-slate-700 text-white rounded-md text-sm hover:bg-slate-800" onclick="openResetPasswordModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username'])) ?>', '<?= htmlspecialchars(addslashes($user['email'] ?? '')) ?>')">
                                            Reset Pass
                                        </button>
                                        <?php if ((int) $user['id'] !== (int) session()->get('user_id')): ?>
                                            <button type="button" class="px-3 py-1.5 rounded-md text-sm <?= $user['is_admin'] ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' ?>" onclick="<?= $user['is_admin'] ? 'revokeAdmin' : 'grantAdmin' ?>(<?= $user['id'] ?>)">
                                                <?= $user['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?>
                                            </button>
                                            <?php if (strtolower($user['username']) !== 'admin'): ?>
                                                <button type="button" class="px-3 py-1.5 rounded-md text-sm <?= $isActive ? 'bg-amber-600 text-white hover:bg-amber-700' : 'bg-emerald-600 text-white hover:bg-emerald-700' ?>" onclick="toggleStatus(<?= $user['id'] ?>)">
                                                    <?= $isActive ? 'Disable' : 'Enable' ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs italic self-center">Current Account</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tailwind modal (hidden by default) -->
<div id="assignRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="closeAssignRoleModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-xl mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-lg font-semibold">Assign Role to <span id="userNameDisplay"></span></h5>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeAssignRoleModal()">&times;</button>
        </div>
        <div class="p-6">
            <form id="assignRoleForm">
                <input type="hidden" id="userId" name="user_id">
                <div class="mb-4">
                    <label for="roleId" class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                    <select id="roleId" name="role_id" required class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm leading-5 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">-- Select a Role --</option>
                        <option value="0">-- No Role (Clear Role) --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?> (<?= htmlspecialchars($role['description']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
            <button type="button" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" onclick="closeAssignRoleModal()">Cancel</button>
            <button type="button" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700" onclick="assignRoleSubmit()">Assign Role</button>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="closeEditUserModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-lg font-semibold text-gray-800">Edit User Details</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeEditUserModal()">&times;</button>
        </div>
        <div class="p-6">
            <div id="editUserModalError" class="hidden mb-4 p-3 rounded-md bg-red-50 text-red-700 text-sm border border-red-200"></div>
            <form id="editUserForm" onsubmit="event.preventDefault(); submitEditUser();">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="editFirstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="editFirstName" name="first_name" required class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="editLastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="editLastName" name="last_name" required class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="editEmail" name="email" required class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </form>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t bg-gray-50">
            <button type="button" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm font-medium" onclick="closeEditUserModal()">Cancel</button>
            <button type="button" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium" onclick="submitEditUser()">Save Changes</button>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="closeResetPasswordModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-lg font-semibold text-gray-800">Reset User Password</h5>
            <button type="button" class="text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeResetPasswordModal()">&times;</button>
        </div>
        <div class="p-6">
            <div id="resetPasswordModalError" class="hidden mb-4 p-3 rounded-md bg-red-50 text-red-700 text-sm border border-red-200"></div>
            <input type="hidden" id="resetUserId" name="user_id">
            
            <div class="mb-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-sm font-bold text-gray-800" id="resetTargetName">User Name</div>
                <div class="text-xs text-gray-500" id="resetTargetEmail">user@example.com</div>
            </div>

            <!-- Option 1: Direct Set -->
            <div class="mb-5">
                <label for="manualNewPassword" class="block text-sm font-semibold text-gray-700 mb-1">Set New Password Manually</label>
                <div class="flex gap-2">
                    <input type="text" id="manualNewPassword" placeholder="Min 8 chars, 1 upper, 1 lower, 1 digit" class="block flex-1 rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" class="px-3 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-md text-xs hover:bg-gray-200 whitespace-nowrap" onclick="generateRandomPassword()">Auto-Gen</button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Must contain uppercase, lowercase, and a number.</p>
                <div class="mt-2">
                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700" onclick="submitResetPassword('manual')">Set Password Now</button>
                </div>
            </div>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase font-medium">Or Send Email</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <!-- Option 2: Email Reset Link -->
            <div class="mt-3">
                <p class="text-xs text-gray-600 mb-2">Send an automated password reset link directly to their registered email address.</p>
                <button type="button" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-medium hover:bg-emerald-700" onclick="submitResetPassword('email')">Send Reset Link via Email</button>
            </div>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t bg-gray-50">
            <button type="button" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm font-medium" onclick="closeResetPasswordModal()">Close</button>
        </div>
    </div>
</div>

<!-- Self-modify information modal -->
<div id="selfModifyModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="background:#fff; padding:24px; border-radius:12px; max-width:480px; margin:auto;">
        <h5 class="text-lg font-semibold text-gray-800 mb-2">Action not allowed</h5>
        <p id="selfModifyMessage" class="text-gray-600 text-sm mb-4">You cannot change your own role or admin status.</p>
        <div class="flex justify-end">
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm" onclick="closeSelfModifyModal()">Close</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function setCsrfToken(token) {
        if (!token) return;
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', token);
    }

    function setUserData(userId, userName, currentRoleId) {
        document.getElementById('userId').value = userId;
        document.getElementById('userNameDisplay').textContent = userName;
        document.getElementById('roleId').value = (currentRoleId !== null && currentRoleId !== undefined) ? String(currentRoleId) : '';
    }

    function openAssignRoleModal(userId, userName, currentRoleId) {
        setUserData(userId, userName, currentRoleId);
        document.getElementById('assignRoleModal').classList.remove('hidden');
        document.getElementById('assignRoleModal').classList.add('flex');
    }

    function closeAssignRoleModal() {
        const modal = document.getElementById('assignRoleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function assignRoleSubmit() {
        const userId = document.getElementById('userId').value;
        const roleId = document.getElementById('roleId').value;

        if (!userId || roleId === '') {
            alert('Please select a role');
            return;
        }

        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('role_id', roleId);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('admin/assignRole') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data.success) {
                alert(data.message || 'Role assigned successfully');
                location.reload();
            } else {
                const msg = (data && data.message) ? data.message : '';
                if (msg.toLowerCase().includes('own role') || msg.toLowerCase().includes('own admin')) {
                    showSelfModifyModal(msg);
                } else {
                    alert('Error: ' + msg);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning role');
        })
        .finally(() => {
            closeAssignRoleModal();
        });
    }

    function grantAdmin(userId) {
        if (!confirm('Make this user an admin?')) return;

        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('admin/grantAdmin') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data.success) {
                alert(data.message || 'Admin privileges granted');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || 'Failed'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    function revokeAdmin(userId) {
        if (!confirm('Revoke admin privileges from this user?')) return;

        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('admin/revokeAdmin') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data.success) {
                alert(data.message || 'Admin privileges revoked');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || 'Failed'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    function toggleStatus(userId) {
        if (!confirm('Change activation status for this user account?')) return;

        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('admin/toggleStatus') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data.success) {
                alert(data.message || 'Status updated successfully');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || 'Failed to update status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // ==========================================
    // EDIT USER DETAILS
    // ==========================================
    function openEditUserModal(userId, firstName, lastName, email) {
        document.getElementById('editUserId').value = userId;
        document.getElementById('editFirstName').value = firstName || '';
        document.getElementById('editLastName').value = lastName || '';
        document.getElementById('editEmail').value = email || '';
        const errEl = document.getElementById('editUserModalError');
        errEl.textContent = '';
        errEl.classList.add('hidden');
        const modal = document.getElementById('editUserModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitEditUser() {
        const userId = document.getElementById('editUserId').value;
        const firstName = document.getElementById('editFirstName').value.trim();
        const lastName = document.getElementById('editLastName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const errorDiv = document.getElementById('editUserModalError');

        if (!firstName || !lastName || !email) {
            errorDiv.textContent = 'Please complete First Name, Last Name, and Email Address.';
            errorDiv.classList.remove('hidden');
            return;
        }

        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        formData.append('email', email);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= base_url('admin/updateUser') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data.success) {
                closeEditUserModal();
                alert(data.message || 'User updated successfully');
                location.reload();
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : 'Failed to update user details.';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error('Update user error:', err);
            errorDiv.textContent = 'A network or server error occurred.';
            errorDiv.classList.remove('hidden');
        });
    }

    // ==========================================
    // RESET USER PASSWORD
    // ==========================================
    function openResetPasswordModal(userId, fullName, email) {
        document.getElementById('resetUserId').value = userId;
        document.getElementById('resetTargetName').textContent = fullName;
        document.getElementById('resetTargetEmail').textContent = email || 'No email on file';
        document.getElementById('manualNewPassword').value = '';
        const errEl = document.getElementById('resetPasswordModalError');
        errEl.textContent = '';
        errEl.classList.add('hidden');
        const modal = document.getElementById('resetPasswordModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeResetPasswordModal() {
        const modal = document.getElementById('resetPasswordModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function generateRandomPassword() {
        const letters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
        const numbers = '23456789';
        const specials = '!@#$%^&*';
        let pwd = '';
        pwd += 'ABCDEFGHJKLMNPQRSTUVWXYZ'[Math.floor(Math.random() * 24)];
        pwd += 'abcdefghjkmnpqrstuvwxyz'[Math.floor(Math.random() * 23)];
        pwd += numbers[Math.floor(Math.random() * numbers.length)];
        pwd += specials[Math.floor(Math.random() * specials.length)];
        const allChars = letters + numbers + specials;
        for (let i = 0; i < 6; i++) {
            pwd += allChars[Math.floor(Math.random() * allChars.length)];
        }
        document.getElementById('manualNewPassword').value = pwd;
    }

    function submitResetPassword(mode) {
        const userId = document.getElementById('resetUserId').value;
        const errorDiv = document.getElementById('resetPasswordModalError');
        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('mode', mode);
        formData.append('<?= csrf_token() ?>', csrfToken);

        if (mode === 'manual') {
            const newPassword = document.getElementById('manualNewPassword').value.trim();
            if (!newPassword) {
                errorDiv.textContent = 'Please enter or generate a new password.';
                errorDiv.classList.remove('hidden');
                return;
            }
            formData.append('new_password', newPassword);
        }

        fetch('<?= base_url('admin/resetUserPassword') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data.success) {
                closeResetPasswordModal();
                alert(data.message || 'Password reset successfully.');
                location.reload();
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : 'Failed to reset password.';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error('Reset password error:', err);
            errorDiv.textContent = 'A network or server error occurred.';
            errorDiv.classList.remove('hidden');
        });
    }

    function showSelfModifyModal(message) {
        alert(message || 'You cannot modify your own administrator status');
    }
</script>
<?= $this->endSection() ?>
