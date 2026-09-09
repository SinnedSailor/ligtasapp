<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<style>
    /* Modern Role Option Cards */
    .role-option-card {
        border: 2px solid #e2e8f0;
        background: #ffffff;
        border-radius: 12px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .role-option-card:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .role-option-card.is-active {
        border-color: #002c76 !important;
        background-color: #f0f7ff !important;
        box-shadow: 0 0 0 2px #002c76, 0 6px 18px rgba(0, 44, 118, 0.15) !important;
    }
    .role-option-card .role-radio-dot {
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        border: 2px solid #cbd5e1;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .role-option-card.is-active .role-radio-dot {
        border-color: #002c76 !important;
        background-color: #002c76 !important;
        box-shadow: inset 0 0 0 3px #ffffff !important;
    }
</style>
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
                                        <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="openAssignRoleModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username'])) ?>', <?= $user['role_id'] ?? 'null' ?>, '<?= htmlspecialchars(addslashes($user['username'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($user['email'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($user['role_name'] ?? '')) ?>')">
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

<!-- Modern Assign Role Modal -->
<div id="assignRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAssignRoleModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden z-10 my-auto flex flex-col max-h-[min(92vh,680px)]">
        <!-- Header with Gradient Accent & Close Button -->
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-blue-200 border border-white/15 shadow-sm">
                    <?= svg_icon('shield', 'w-5 h-5 text-white') ?>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white leading-tight m-0">Assign & Update Role</h3>
                    <p class="text-xs text-blue-200 m-0 mt-0.5">Manage user permissions and system access</p>
                </div>
            </div>
            <button type="button" class="text-white/70 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10" onclick="closeAssignRoleModal()" title="Close modal">
                <?= svg_icon('x', 'w-5 h-5') ?>
            </button>
        </div>

        <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4" style="scrollbar-width: thin;">
            <!-- Target User Info Card -->
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center flex-shrink-0 text-base shadow-sm">
                        👤
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-sm truncate" id="userNameDisplay">User Name</div>
                        <div class="text-xs text-gray-500 truncate" id="userSubinfoDisplay">@username • email</div>
                    </div>
                </div>
                <div id="userCurrentRoleBadgeDisplay" class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">No Role</span>
                </div>
            </div>

            <form id="assignRoleForm">
                <input type="hidden" id="userId" name="user_id">
                <select id="roleId" name="role_id" class="hidden">
                    <option value="">-- Select a Role --</option>
                    <option value="1">ADMIN (Full Administrator)</option>
                    <option value="2">FOCAL (Regional / Focal Point)</option>
                    <option value="3">LGU (Local Government Unit)</option>
                    <option value="4">PROVINCE (Provincial Office)</option>
                    <option value="0">No Role (Clear Role)</option>
                </select>
            </form>

            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Choose System Role</label>
                <span class="text-xs text-gray-400">Click a card to select</span>
            </div>

            <!-- Interactive Visual Role Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="usersRoleCardsContainer">
                
                <!-- Role Card: ADMIN -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-red-300" data-role-id="1" onclick="selectUsersRoleCard('1')">
                    <div class="w-9 h-9 rounded-xl bg-red-100 text-red-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <?= svg_icon('shield', 'w-4 h-4') ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-gray-900">ADMIN</span>
                            <span class="role-radio-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-snug">Full system administrator; manage users, system settings, and all reports.</p>
                    </div>
                </div>

                <!-- Role Card: FOCAL -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-amber-300" data-role-id="2" onclick="selectUsersRoleCard('2')">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <?= svg_icon('users', 'w-4 h-4') ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-gray-900">FOCAL</span>
                            <span class="role-radio-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-snug">Regional focal point; review, validate, and monitor provincial reports.</p>
                    </div>
                </div>

                <!-- Role Card: LGU -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-emerald-300" data-role-id="3" onclick="selectUsersRoleCard('3')">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <?= svg_icon('home', 'w-4 h-4') ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-gray-900">LGU</span>
                            <span class="role-radio-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-snug">Municipal level user; encode, upload, and track municipal incident cases.</p>
                    </div>
                </div>

                <!-- Role Card: PROVINCE -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-blue-300" data-role-id="4" onclick="selectUsersRoleCard('4')">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <?= svg_icon('map-pin', 'w-4 h-4') ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-gray-900">PROVINCE</span>
                            <span class="role-radio-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-snug">Provincial office user; consolidate and oversee municipal incident records.</p>
                    </div>
                </div>

                <!-- Role Card: NO ROLE -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white sm:col-span-2 hover:border-gray-400" data-role-id="0" onclick="selectUsersRoleCard('0')">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <?= svg_icon('x-circle', 'w-4 h-4') ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-gray-700">NO ROLE (Clear / Revoke Assignment)</span>
                            <span class="role-radio-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 leading-snug">Removes current role permissions. The account cannot access role-restricted data.</p>
                    </div>
                </div>

            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2.5">
                <span class="text-amber-600 flex-shrink-0 font-bold text-sm">💡</span>
                <span class="leading-relaxed">Assigning <strong>ADMIN</strong> automatically grants full administrative rights. Role changes take effect immediately.</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex-shrink-0">
            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold transition" onclick="closeAssignRoleModal()">Cancel</button>
            <button type="button" class="px-5 py-2 rounded-lg bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold shadow-md transition flex items-center gap-1.5" onclick="assignRoleSubmit()">
                <?= svg_icon('check', 'w-4 h-4') ?>
                <span>Update Role</span>
            </button>
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
        return (meta && meta.content) ? meta.content : '<?= csrf_hash() ?>';
    }

    function setCsrfToken(token) {
        if (!token) return;
        document.querySelectorAll('meta[name="csrf-token"]').forEach(meta => {
            meta.setAttribute('content', token);
        });
    }

    async function parseJsonResponse(response) {
        let rawText = '';
        let data = null;
        try {
            rawText = await response.text();
            data = JSON.parse(rawText);
        } catch (e) {
            data = null;
        }
        if (!response.ok) {
            console.error('[AJAX Error Details]', {
                status: response.status,
                statusText: response.statusText,
                url: response.url,
                data: data,
                rawBody: rawText ? rawText.slice(0, 500) : '(empty)'
            });
        }
        return { status: response.status, ok: response.ok, data, rawText };
    }

    function selectUsersRoleCard(roleId) {
        const roleStr = (roleId !== null && roleId !== undefined && String(roleId) !== 'null') ? String(roleId) : '';
        const roleSelect = document.getElementById('roleId');
        if (roleSelect) {
            roleSelect.value = roleStr;
        }
        document.querySelectorAll('#usersRoleCardsContainer .role-option-card').forEach(card => {
            if (card.getAttribute('data-role-id') === roleStr) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
    }

    function openAssignRoleModal(userId, userName, currentRoleId, username, email, roleName) {
        document.getElementById('userId').value = userId;
        document.getElementById('userNameDisplay').textContent = userName || 'User';

        const subInfo = document.getElementById('userSubinfoDisplay');
        if (subInfo) {
            const parts = [];
            if (username) parts.push('@' + username);
            if (email) parts.push(email);
            subInfo.textContent = parts.join(' • ') || 'Target Account';
        }

        const badgeContainer = document.getElementById('userCurrentRoleBadgeDisplay');
        if (badgeContainer) {
            if (roleName) {
                const norm = String(roleName).trim().toUpperCase();
                badgeContainer.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">${norm}</span>`;
            } else {
                badgeContainer.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">No Role</span>`;
            }
        }

        let normalizedRoleId = '0';
        if (currentRoleId !== null && currentRoleId !== undefined && String(currentRoleId) !== 'null' && String(currentRoleId) !== '') {
            normalizedRoleId = String(currentRoleId);
        }
        selectUsersRoleCard(normalizedRoleId);

        const modal = document.getElementById('assignRoleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
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
            Swal.fire({
                title: 'Selection Required',
                text: 'Please select a role or choose "NO ROLE (Clear / Revoke Assignment)".',
                icon: 'warning',
                confirmButtonColor: '#002c76'
            });
            return;
        }

        const userName = document.getElementById('userNameDisplay').textContent;
        const roleNames = {
            '1': 'ADMIN (Full Administrator)',
            '2': 'FOCAL (Regional / Focal Point)',
            '3': 'LGU (Local Government Unit)',
            '4': 'PROVINCE (Provincial Office)',
            '0': 'NO ROLE (Clear Role)'
        };
        const roleLabel = roleNames[roleId] || roleId;

        Swal.fire({
            title: 'Confirm Role Update',
            html: `Change role for <strong>${userName}</strong> to <strong>${roleLabel}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update role',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#002c76',
            cancelButtonColor: '#94a3b8'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const csrfToken = getCsrfToken();
            const csrfName = '<?= csrf_token() ?>';
            const csrfHeader = '<?= csrf_header() ?>';

            const params = new URLSearchParams();
            params.append('user_id', userId);
            params.append('role_id', roleId);
            params.append(csrfName, csrfToken);

            const reqHeaders = {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            };
            reqHeaders[csrfHeader] = csrfToken;

            fetch('<?= base_url('admin-panel/assign-role') ?>', {
                method: 'POST',
                body: params.toString(),
                headers: reqHeaders
            })
            .then(parseJsonResponse)
            .then(({ status, ok, data, rawText }) => {
                if (data && data.csrf_token) {
                    setCsrfToken(data.csrf_token);
                }
                if (ok && data && data.success) {
                    Swal.fire({
                        title: 'Role Updated!',
                        text: data.message || 'Role assigned successfully',
                        icon: 'success',
                        confirmButtonColor: '#002c76'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let msg = (data && data.message) ? data.message : 'Failed to update role';
                    if (!data) {
                        if (status === 403) {
                            msg = 'Access forbidden (403). Session token was rejected. Please refresh the page.';
                        } else {
                            msg = 'Server error (HTTP ' + status + ')';
                        }
                    }
                    if (msg.toLowerCase().includes('own role') || msg.toLowerCase().includes('own admin')) {
                        showSelfModifyModal(msg);
                    } else {
                        Swal.fire({
                            title: (data && data.csrf_error) ? 'Security Token Notice' : 'Error',
                            text: msg,
                            icon: (data && data.csrf_error) ? 'warning' : 'error',
                            confirmButtonColor: '#002c76'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while assigning role',
                    icon: 'error',
                    confirmButtonColor: '#002c76'
                });
            })
            .finally(() => {
                closeAssignRoleModal();
            });
        });
    }

    function grantAdmin(userId) {
        if (!confirm('Make this user an admin?')) return;

        const csrfToken = getCsrfToken();
        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        const params = new URLSearchParams();
        params.append('user_id', userId);
        params.append(csrfName, csrfToken);

        const reqHeaders = {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };
        reqHeaders[csrfHeader] = csrfToken;

        fetch('<?= base_url('admin-panel/grant-admin') ?>', {
            method: 'POST',
            body: params.toString(),
            headers: reqHeaders
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data && data.success) {
                alert(data.message || 'Admin privileges granted');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || (status === 403 ? 'Access forbidden or session expired' : 'Failed')));
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
        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        const params = new URLSearchParams();
        params.append('user_id', userId);
        params.append(csrfName, csrfToken);

        const reqHeaders = {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };
        reqHeaders[csrfHeader] = csrfToken;

        fetch('<?= base_url('admin-panel/revoke-admin') ?>', {
            method: 'POST',
            body: params.toString(),
            headers: reqHeaders
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data && data.success) {
                alert(data.message || 'Admin privileges revoked');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || (status === 403 ? 'Access forbidden or session expired' : 'Failed')));
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
        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        const params = new URLSearchParams();
        params.append('user_id', userId);
        params.append(csrfName, csrfToken);

        const reqHeaders = {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };
        reqHeaders[csrfHeader] = csrfToken;

        fetch('<?= base_url('admin-panel/toggle-status') ?>', {
            method: 'POST',
            body: params.toString(),
            headers: reqHeaders
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data && data.success) {
                alert(data.message || 'Status updated');
                location.reload();
            } else {
                alert('Error: ' + ((data && data.message) || (status === 403 ? 'Access forbidden or session expired' : 'Failed to update user status')));
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
        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        const params = new URLSearchParams();
        params.append('user_id', userId);
        params.append('first_name', firstName);
        params.append('last_name', lastName);
        params.append('email', email);
        params.append(csrfName, csrfToken);

        const reqHeaders = {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };
        reqHeaders[csrfHeader] = csrfToken;

        fetch('<?= base_url('admin-panel/update-user') ?>', {
            method: 'POST',
            body: params.toString(),
            headers: reqHeaders
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data && data.success) {
                closeEditUserModal();
                alert(data.message || 'User updated successfully');
                location.reload();
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh.' : 'Failed to update user details.');
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
        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        const params = new URLSearchParams();
        params.append('user_id', userId);
        params.append('mode', mode);
        params.append(csrfName, csrfToken);

        if (mode === 'manual') {
            const newPassword = document.getElementById('manualNewPassword').value.trim();
            if (!newPassword) {
                errorDiv.textContent = 'Please enter or generate a new password.';
                errorDiv.classList.remove('hidden');
                return;
            }
            params.append('new_password', newPassword);
        }

        const reqHeaders = {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };
        reqHeaders[csrfHeader] = csrfToken;

        fetch('<?= base_url('admin-panel/reset-password') ?>', {
            method: 'POST',
            body: params.toString(),
            headers: reqHeaders
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data && data.success) {
                closeResetPasswordModal();
                alert(data.message || 'Password reset successfully.');
                location.reload();
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh.' : 'Failed to reset password.');
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
