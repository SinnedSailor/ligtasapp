<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<!-- styles removed: migrated to Tailwind utilities -->
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Province</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Municipality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <?= htmlspecialchars(
                                            mb_convert_case(
                                                trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                                                MB_CASE_TITLE, 'UTF-8'
                                            )
                                        ) ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
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
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['province'] ?? '-') ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['municipality'] ?? '-') ?></td>
                                    <td class="px-4 py-4 text-sm flex gap-2">
                                        <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="openAssignRoleModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')">
                                            Assign Role
                                        </button>
                                        <?php if ($user['id'] != session()->get('user_id')): ?>
                                            <button type="button" class="px-3 py-1.5 rounded-md text-sm <?= $user['is_admin'] ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600   hover:bg-green-700' ?>" onclick="<?= $user['is_admin'] ? 'revokeAdmin' : 'grantAdmin' ?>(<?= $user['id'] ?>)">
                                                <?= $user['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?>
                                            </button>
                                        <?php else: ?>
                                            <a href="#" id="selfModifyInfo" class="text-muted small" onclick="showSelfModifyModal('You cannot change your own role or admin status'); return false;">You cannot change your own role or admin status</a>
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

<!-- Self-modify information modal -->
<div class="modal fade" id="selfModifyModal" tabindex="-1" aria-labelledby="selfModifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selfModifyModalLabel">Action not allowed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="selfModifyMessage" class="mb-0">You cannot change your own role or admin status.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function setUserData(userId, userName) {
        document.getElementById('userId').value = userId;
        document.getElementById('userNameDisplay').textContent = userName;
        document.getElementById('roleId').value = '';
    }

    function openAssignRoleModal(userId, userName) {
        setUserData(userId, userName);
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

        if (!userId || !roleId) {
            alert('Please select a role');
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('role_id', roleId);

        fetch('<?= base_url('admin/assignRole') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Role assigned successfully');
                location.reload();
            } else {
                const msg = data.message || '';
                if (msg.includes('cannot change your own') || msg.includes('cannot clear your own') || msg.includes('You cannot revoke your own') || msg.includes('You cannot change your own')) {
                    showSelfModifyModal(msg);
                } else {
                    alert('Error: ' + msg);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        })
        .finally(() => {
            closeAssignRoleModal();
        });
    }

    function grantAdmin(userId) {
        if (!confirm('Make this user an admin?')) return;

        const formData = new FormData();
        formData.append('user_id', userId);

        fetch('<?= base_url('admin/grantAdmin') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Admin privileges granted');
                location.reload();
            } else {
                const msg = data.message || '';
                if (msg.includes('cannot change your own') || msg.includes('cannot clear your own') || msg.includes('You cannot revoke your own') || msg.includes('You cannot change your own')) {
                    showSelfModifyModal(msg);
                } else {
                    alert('Error: ' + msg);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    function revokeAdmin(userId) {
        if (!confirm('Revoke admin privileges from this user?')) return;

        const formData = new FormData();
        formData.append('user_id', userId);

        fetch('<?= base_url('admin/revokeAdmin') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Admin privileges revoked');
                location.reload();
            } else {
                const msg = data.message || '';
                if (msg.includes('cannot change your own') || msg.includes('cannot clear your own') || msg.includes('You cannot revoke your own') || msg.includes('You cannot change your own')) {
                    showSelfModifyModal(msg);
                } else {
                    alert('Error: ' + msg);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    function showSelfModifyModal(message) {
        document.getElementById('selfModifyMessage').textContent = message || 'You cannot change your own role or admin status.';
        const modalEl = document.getElementById('selfModifyModal');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }
</script>
<?= $this->endSection() ?>
