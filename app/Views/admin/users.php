<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .container-fluid {
        padding: 20px;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 20px;
    }

    .table-responsive {
        border-radius: 0.25rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.5rem 0.75rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">User Management</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">All Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Admin</th>
                            <th>Province</th>
                            <th>Municipality</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars(mb_convert_case(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), MB_CASE_TITLE, 'UTF-8')) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td>
                                        <?php if ($user['role_name']): ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($user['role_name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Role</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['is_admin']): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">Regular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['province'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($user['municipality'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($user['id'] != session()->get('user_id')): ?>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignRoleModal" onclick="setUserData(<?= $user['id'] ?>, '<?= htmlspecialchars(mb_convert_case(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), MB_CASE_TITLE, 'UTF-8')) ?>')">
                                                Assign Role
                                            </button>
                                            <button type="button" class="btn btn-sm btn-<?= $user['is_admin'] ? 'danger' : 'success' ?>" onclick="<?= $user['is_admin'] ? 'revokeAdmin' : 'grantAdmin' ?>(<?= $user['id'] ?>)">
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

<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role to <span id="userNameDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignRoleForm">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="mb-3">
                        <label for="roleId" class="form-label">Select Role</label>
                        <select class="form-select" id="roleId" name="role_id" required>
                            <option value="">-- Select a Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?> (<?= htmlspecialchars($role['description']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="assignRoleSubmit()">Assign Role</button>
            </div>
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
        });

        bootstrap.Modal.getInstance(document.getElementById('assignRoleModal')).hide();
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
