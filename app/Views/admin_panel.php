<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
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
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.025em;
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

    .role-norole {
        background: #6B7280;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
    }

    .stat-item {
        background: #ffffff;
        border: 1px solid rgba(0, 44, 118, 0.12);
        border-radius: 12px;
        text-align: center;
        padding: 16px 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 44, 118, 0.08);
    }

    .stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #002C76;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table thead th {
        color: #002C76;
        font-weight: 600;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 2000;
        justify-content: center;
        align-items: center;
        padding: 16px;
        overflow-y: auto;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        max-width: 500px;
        width: 100%;
        animation: modalSlideIn 0.2s ease-out;
        margin: auto;
        position: relative;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-16px);
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
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-body p {
        color: #64748b;
        margin-bottom: 14px;
        font-size: 14px;
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
        transition: opacity 0.15s ease;
    }

    .modal-btn:hover {
        opacity: 0.9;
    }

    .modal-btn-primary {
        background: #002C76;
        color: #fff;
    }

    .modal-btn-secondary {
        background: #e2e8f0;
        color: #334155;
    }

    .success-icon {
        width: 56px;
        height: 56px;
        background: #10B981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .success-message {
        text-align: center;
        color: #002C76;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .success-detail {
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 16px;
    }

    .modal-error {
        background: #fef2f2;
        color: #dc2626;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 13px;
        border-left: 4px solid #dc2626;
        margin-bottom: 12px;
        display: none;
    }

    .modal-error.show {
        display: block;
    }

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
<div class="page-header flex flex-wrap justify-between items-center mb-6 mx-auto px-4 sm:px-6 lg:px-8" style="max-width:1200px;">
    <div>
        <h3 class="page-title mb-1 flex items-center">
            <?= svg_icon('shield', 'w-6 h-6 mr-2 text-blue-900') ?>
            <span class="text-xl font-bold text-gray-900">User Management</span>
        </h3>
        <div class="text-sm text-gray-500">Manage user roles, account activation status, and administrative privileges.</div>
    </div>
</div>

<!-- Admin Statistics Section -->
<div class="mx-auto px-4 sm:px-6 lg:px-8 mb-6" style="max-width:1200px;">
    <div id="admin-stats" class="stats-grid">
        <div class="stat-item"><div class="stat-number">—</div><div class="stat-label">Total Users</div></div>
        <div class="stat-item"><div class="stat-number">—</div><div class="stat-label">Admin Users</div></div>
        <div class="stat-item"><div class="stat-number">—</div><div class="stat-label">Regular Users</div></div>
        <div class="stat-item"><div class="stat-number">—</div><div class="stat-label">No Role Assigned</div></div>
    </div>
</div>

<div id="user-management-section" class="bg-white rounded-2xl shadow admin-card mx-auto px-4 sm:px-6 lg:px-8 mb-8" style="max-width:1200px; border-left: none;">
    <div class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h4 class="text-lg font-bold text-gray-800">Registered Users</h4>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center rounded-lg bg-gray-50 border border-gray-200 overflow-hidden px-3 py-1.5 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500">
                    <div class="text-gray-400 mr-2"><?= svg_icon('search', 'w-4 h-4') ?></div>
                    <input type="text" id="userSearch" class="text-sm bg-transparent outline-none w-48 sm:w-60" placeholder="Search by name, email, user..." onkeyup="filterUsers()" />
                </div>

                <div class="flex items-center rounded-lg bg-gray-50 border border-gray-200 overflow-hidden px-2.5 py-1.5">
                    <div class="text-gray-400 mr-1.5"><?= svg_icon('users', 'w-4 h-4') ?></div>
                    <select id="roleFilter" class="text-sm bg-transparent outline-none pr-2 text-gray-700 cursor-pointer" onchange="filterUsers()">
                        <option value="">All Roles</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="FOCAL">FOCAL</option>
                        <option value="LGU">LGU</option>
                        <option value="PROVINCE">PROVINCE</option>
                        <option value="NO ROLE">NO ROLE</option>
                    </select>
                </div>

                <div class="flex items-center rounded-lg bg-gray-50 border border-gray-200 overflow-hidden px-2.5 py-1.5">
                    <select id="statusFilter" class="text-sm bg-transparent outline-none pr-2 text-gray-700 cursor-pointer" onchange="filterUsers()">
                        <option value="">All Status</option>
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="DISABLED">DISABLED</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="min-w-full w-full divide-y divide-gray-200">
                <thead style="background-color:#002c76;">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">USERNAME</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">FULL NAME</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">EMAIL</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">ROLE</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">STATUS</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-white tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="user-tbody" class="bg-white divide-y divide-gray-100">
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Loading users...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div id="assignRoleModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 600px; width: 100%; padding: 0; border-radius: 16px; display: flex; flex-direction: column; max-height: min(92vh, 680px); overflow: hidden; margin: auto;">
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
            <button type="button" class="text-white/70 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10" onclick="closeRoleModal()" title="Close modal">
                <?= svg_icon('x', 'w-5 h-5') ?>
            </button>
        </div>

        <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4" style="scrollbar-width: thin;">
            <!-- Target User Info Card -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center flex-shrink-0 text-base shadow-sm">
                        👤
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-sm truncate" id="modalUserName">User Name</div>
                        <div class="text-xs text-gray-500 truncate" id="modalUserSubinfo">@username • user@example.com</div>
                    </div>
                </div>
                <div id="modalCurrentRoleBadge" class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">No Role</span>
                </div>
            </div>

            <div id="roleModalError" class="modal-error"></div>
            
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Choose System Role</label>
                <span class="text-xs text-gray-400">Click a card to select</span>
            </div>

            <!-- Hidden input maintaining full compatibility with submitRoleAssignment() -->
            <select id="roleSelect" class="hidden">
                <option value="">-- Select a role --</option>
                <option value="1">ADMIN (Full System Administrator)</option>
                <option value="2">FOCAL (Regional / Focal Point)</option>
                <option value="3">LGU (Local Government Unit)</option>
                <option value="4">PROVINCE (Provincial Office)</option>
                <option value="0">No Role (Clear Role)</option>
            </select>

            <!-- Interactive Visual Role Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="roleCardsContainer">
                
                <!-- Role Card: ADMIN -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-red-300" data-role-id="1" onclick="selectRoleCard('1')">
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
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-amber-300" data-role-id="2" onclick="selectRoleCard('2')">
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
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-emerald-300" data-role-id="3" onclick="selectRoleCard('3')">
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
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white hover:border-blue-300" data-role-id="4" onclick="selectRoleCard('4')">
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

                <!-- Role Card: NO ROLE (span 2 on sm screens) -->
                <div class="role-option-card cursor-pointer p-3.5 rounded-xl border-2 transition-all flex items-start gap-3 bg-white sm:col-span-2 hover:border-gray-400" data-role-id="0" onclick="selectRoleCard('0')">
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

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex-shrink-0">
            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold transition" onclick="closeRoleModal()">Cancel</button>
            <button type="button" class="px-5 py-2 rounded-lg bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold shadow-md transition flex items-center gap-1.5" onclick="submitRoleAssignment()">
                <?= svg_icon('check', 'w-4 h-4') ?>
                <span>Update Role</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit User Details Modal -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3><?= svg_icon('pencil', 'w-5 h-5 text-blue-900') ?> Edit User Details</h3>
        </div>
        <div class="modal-body">
            <div id="editUserModalError" class="modal-error"></div>
            <input type="hidden" id="editUserId" />
            <div class="form-group mb-3">
                <label for="editFirstName" class="text-xs font-semibold text-gray-600 uppercase tracking-wider block mb-1">First Name *</label>
                <input type="text" id="editFirstName" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
            </div>
            <div class="form-group mb-3">
                <label for="editLastName" class="text-xs font-semibold text-gray-600 uppercase tracking-wider block mb-1">Last Name *</label>
                <input type="text" id="editLastName" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
            </div>
            <div class="form-group mb-3">
                <label for="editEmail" class="text-xs font-semibold text-gray-600 uppercase tracking-wider block mb-1">Email Address *</label>
                <input type="email" id="editEmail" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
                <p class="text-xs text-gray-400 mt-1">Used for authentication, 2FA security codes, and password reset links.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeEditUserModal()">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary" onclick="submitEditUser()">Save Changes</button>
        </div>
    </div>
</div>

<!-- Admin Reset Password Modal -->
<div id="resetPasswordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3 class="flex items-center text-blue-900 font-bold"><?= svg_icon('shield', 'w-5 h-5 mr-1.5 text-blue-900') ?> Reset User Password</h3>
        </div>
        <div class="modal-body">
            <p class="text-sm text-gray-700 mb-3">Reset password for <strong id="resetTargetName" class="text-gray-900"></strong>:</p>
            <div id="resetPasswordModalError" class="modal-error"></div>
            <input type="hidden" id="resetUserId" />

            <!-- Option 1: Send Reset Link via Email -->
            <div class="p-3.5 mb-4 rounded-xl bg-blue-50 border border-blue-200">
                <div class="text-xs font-bold text-blue-950 uppercase tracking-wide mb-1">Option 1: Send Reset Link via Email</div>
                <p class="text-xs text-blue-800 mb-2.5">Dispatches an email with a secure 30-minute password reset link to <span id="resetTargetEmail" class="font-semibold underline"></span>.</p>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow-sm transition" onclick="submitResetPassword('email')">
                    📨 Send Password Reset Email
                </button>
            </div>

            <!-- Option 2: Set New Password Manually -->
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <div class="text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">Option 2: Set New Password Directly</div>
                <p class="text-xs text-gray-500 mb-2">Instantly change the user's password now.</p>
                <div class="flex gap-2 mb-2">
                    <input type="text" id="manualNewPassword" placeholder="Enter new password (min 8 chars)" class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="button" class="px-2.5 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg" onclick="generateRandomPassword()">
                        🎲 Generate
                    </button>
                </div>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-semibold shadow-sm transition" onclick="submitResetPassword('manual')">
                    💾 Save New Password
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeResetPasswordModal()">Close</button>
        </div>
    </div>
</div>

<!-- Success Feedback Modal -->
<div id="successModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-body">
            <div class="success-icon">
                <?= svg_icon('check', 'w-6 h-6 text-white') ?>
            </div>
            <div class="success-message" id="successMessage">Action Completed</div>
            <div class="success-detail" id="successDetail"></div>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button class="modal-btn modal-btn-primary px-6" onclick="closeSuccessModal()">Done</button>
        </div>
    </div>
</div>

<!-- Self-modify Information Modal -->
<div id="selfModifyModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header mb-3">
            <h3 class="text-amber-600"><?= svg_icon('alert', 'w-5 h-5 text-amber-500') ?> Action Restricted</h3>
        </div>
        <div class="modal-body">
            <p id="selfModifyMessage">You cannot change your own role or deactivate your own administrator account.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-primary" onclick="closeSelfModifyModal()">Understood</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const currentLoggedInUserId = <?= (int) (session()->get('user_id') ?? 0) ?>;

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function setCsrfToken(newToken) {
        if (!newToken) return;
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', newToken);
    }

    async function parseJsonResponse(response) {
        const contentType = response.headers.get('content-type') || '';
        let data = null;
        if (contentType.includes('application/json')) {
            try {
                data = await response.json();
            } catch (e) {
                data = null;
            }
        }
        return { status: response.status, ok: response.ok, data };
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadUsers();
        loadAdminStats();
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getRoleBadge(roleName) {
        if (!roleName) {
            return '<span class="role-badge role-norole">NO ROLE</span>';
        }

        const normalized = String(roleName).trim().toUpperCase();
        const roleClassMap = {
            'ADMIN': 'role-badge role-admin',
            'FOCAL': 'role-badge role-focal',
            'LGU': 'role-badge role-lgu',
            'PROVINCE': 'role-badge role-province'
        };
        const badgeClass = roleClassMap[normalized] || 'role-badge role-norole';
        return `<span class="${badgeClass}">${escapeHtml(normalized)}</span>`;
    }

    let usersList = [];

    function loadUsers() {
        fetch('<?= base_url('admin/getUsers') ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('user-tbody');
            if (data.users && data.users.length > 0) {
                usersList = data.users;
                renderUserRows(usersList);
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No users found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            document.getElementById('user-tbody').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-rose-500 font-medium">Failed to load users. Please refresh the page.</td></tr>';
        });
    }

    function renderUserRows(users) {
        const tbody = document.getElementById('user-tbody');
        if (!users || users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No matching users found</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => {
            const roleBadge = getRoleBadge(user.role_name);
            const first = (user.first_name || '').trim();
            const last = (user.last_name || '').trim();
            const fullName = (first + ' ' + last).trim() || user.username;
            const isSelf = Number(user.id) === currentLoggedInUserId;
            const isRootAdmin = (user.username || '').toLowerCase() === 'admin';
            const isActive = user.is_active !== undefined ? Number(user.is_active) === 1 : true;

            const statusBadge = isActive
                ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">Active</span>`
                : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">Disabled</span>`;

            let actionHtml = '';

            if (isSelf) {
                actionHtml = `<span class="text-xs text-blue-900 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md font-medium">Current Account</span>`;
            } else {
                // Edit Name & Email button
                actionHtml += `
                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-900 transition-colors shadow-sm" title="Edit Name & Email" onclick="openEditUserModal(${user.id})">
                        <?= svg_icon('pencil', 'w-3.5 h-3.5 mr-1') ?> Edit
                    </button>
                `;

                // Edit Role button
                actionHtml += `
                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-900 transition-colors shadow-sm" title="Change Role" onclick="openRoleModal(${user.id}, '${escapeHtml(fullName)}', ${user.role_id || 'null'})">
                        <?= svg_icon('users', 'w-3.5 h-3.5 mr-1') ?> Role
                    </button>
                `;

                // Reset Password button
                actionHtml += `
                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-indigo-200 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors shadow-sm" title="Reset Password" onclick="openResetPasswordModal(${user.id})">
                        🔒 Reset Pass
                    </button>
                `;

                // Toggle Status (Disable/Enable) button
                if (!isRootAdmin) {
                    if (isActive) {
                        actionHtml += `
                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-rose-200 rounded-md text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 transition-colors shadow-sm" title="Disable User Account" onclick="toggleStatus(${user.id}, '${escapeHtml(fullName)}', 0)">
                                <?= svg_icon('x-circle', 'w-3.5 h-3.5 mr-1') ?> Disable
                            </button>
                        `;
                    } else {
                        actionHtml += `
                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-emerald-200 rounded-md text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors shadow-sm" title="Enable User Account" onclick="toggleStatus(${user.id}, '${escapeHtml(fullName)}', 1)">
                                <?= svg_icon('check', 'w-3.5 h-3.5 mr-1') ?> Enable
                            </button>
                        `;
                    }
                }
            }

            return `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-4 py-4 text-sm font-semibold text-gray-900">${escapeHtml(user.username)}</td>
                    <td class="px-4 py-4 text-sm text-gray-800">${escapeHtml(fullName)}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">${escapeHtml(user.email || '—')}</td>
                    <td class="px-4 py-4 text-sm">${roleBadge}</td>
                    <td class="px-4 py-4 text-sm">${statusBadge}</td>
                    <td class="px-4 py-4 text-sm flex flex-wrap items-center gap-2">
                        ${actionHtml}
                    </td>
                </tr>
            `;
        }).join('');
    }

    function loadAdminStats() {
        fetch('<?= base_url('admin/getStats') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statsDiv = document.getElementById('admin-stats');
                if (!statsDiv) return;
                statsDiv.innerHTML = `
                    <div class="stat-item">
                        <div class="stat-number">${data.totalUsers ?? 0}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number text-rose-600">${data.adminUsers ?? 0}</div>
                        <div class="stat-label">Admin Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number text-blue-700">${data.regularUsers ?? 0}</div>
                        <div class="stat-label">Regular Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number text-amber-600">${data.unassignedRoles ?? 0}</div>
                        <div class="stat-label">No Role Assigned</div>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error loading admin stats:', error));
    }

    let currentRoleIdTargetUserId = null;

    function selectRoleCard(roleId) {
        const roleStr = (roleId !== null && roleId !== undefined && String(roleId) !== 'null') ? String(roleId) : '';
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect) {
            roleSelect.value = roleStr;
        }
        document.querySelectorAll('#roleCardsContainer .role-option-card').forEach(card => {
            if (card.getAttribute('data-role-id') === roleStr) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
        const errorDiv = document.getElementById('roleModalError');
        if (errorDiv) {
            errorDiv.classList.remove('show');
        }
    }

    function openRoleModal(userId, userName, currentRoleId) {
        currentRoleIdTargetUserId = userId;
        document.getElementById('modalUserName').textContent = userName || 'User';

        const user = usersList.find(u => Number(u.id) === Number(userId));
        const subInfo = document.getElementById('modalUserSubinfo');
        if (subInfo) {
            if (user) {
                const parts = [];
                if (user.username) parts.push('@' + user.username);
                if (user.email) parts.push(user.email);
                subInfo.textContent = parts.join(' • ') || 'Target Account';
            } else {
                subInfo.textContent = 'Target Account';
            }
        }

        const currentBadgeContainer = document.getElementById('modalCurrentRoleBadge');
        if (currentBadgeContainer) {
            const roleName = user ? user.role_name : null;
            currentBadgeContainer.innerHTML = getRoleBadge(roleName);
        }

        let normalizedRoleId = '0';
        if (currentRoleId !== null && currentRoleId !== undefined && String(currentRoleId) !== 'null' && String(currentRoleId) !== '') {
            normalizedRoleId = String(currentRoleId);
        }
        selectRoleCard(normalizedRoleId);

        document.getElementById('roleModalError').classList.remove('show');
        document.getElementById('assignRoleModal').classList.add('active');
    }

    function closeRoleModal() {
        document.getElementById('assignRoleModal').classList.remove('active');
        currentRoleIdTargetUserId = null;
    }

    function submitRoleAssignment() {
        const roleSelect = document.getElementById('roleSelect');
        const roleId = roleSelect.value;
        const errorDiv = document.getElementById('roleModalError');

        if (roleId === '') {
            errorDiv.textContent = 'Please choose a role or select "NO ROLE (Clear / Revoke Assignment)".';
            errorDiv.classList.add('show');
            return;
        }

        const targetUserId = currentRoleIdTargetUserId;
        if (!targetUserId) {
            errorDiv.textContent = 'No user target selected. Please close and re-open the modal.';
            errorDiv.classList.add('show');
            return;
        }

        errorDiv.classList.remove('show');
        const userName = document.getElementById('modalUserName').textContent;
        const roleNames = {
            '1': 'ADMIN (Full Administrator)',
            '2': 'FOCAL (Regional / Focal Point)',
            '3': 'LGU (Local Government Unit)',
            '4': 'PROVINCE (Provincial Office)',
            '0': 'NO ROLE (Clear Role)'
        };
        const selectedRoleText = roleNames[roleId] || (roleSelect.options[roleSelect.selectedIndex]?.text || roleId);

        closeRoleModal();

        Swal.fire({
            title: 'Confirm Role Update',
            html: `Change role for <strong>${escapeHtml(userName)}</strong> to <strong>${escapeHtml(selectedRoleText)}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update role',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#002c76',
            cancelButtonColor: '#94a3b8',
        }).then((result) => {
            if (result.isConfirmed) {
                assignRole(targetUserId, roleId);
            }
        });
    }

    function assignRole(userId, roleId) {
        if (!userId) {
            Swal.fire({
                title: 'Error',
                text: 'Invalid target user for role assignment.',
                icon: 'error',
                confirmButtonColor: '#002c76',
            });
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
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) {
                setCsrfToken(data.csrf_token);
            }
            if (ok && data && data.success) {
                Swal.fire({
                    title: 'Role Updated!',
                    text: data.message || 'User role has been successfully assigned.',
                    icon: 'success',
                    confirmButtonColor: '#002c76',
                });
                loadUsers();
                loadAdminStats();
            } else {
                const msg = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh the page.' : 'Failed to update role');
                if (msg.toLowerCase().includes('own role') || msg.toLowerCase().includes('own admin')) {
                    showSelfModifyModal(msg);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: msg,
                        icon: 'error',
                        confirmButtonColor: '#002c76',
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error assigning role:', error);
            Swal.fire({
                title: 'Request Failed',
                text: 'A network or server error occurred while assigning the role.',
                icon: 'error',
                confirmButtonColor: '#002c76',
            });
        });
    }

    function toggleStatus(userId, userName, targetStatus) {
        const isDisabling = (Number(targetStatus) === 0);
        const actionTitle = isDisabling ? 'Deactivate User Account?' : 'Activate User Account?';
        const actionText = isDisabling
            ? `Are you sure you want to disable <strong>${escapeHtml(userName)}</strong>? They will no longer be able to log in.`
            : `Are you sure you want to enable <strong>${escapeHtml(userName)}</strong>? They will regain access to the system.`;
        const confirmBtnText = isDisabling ? 'Yes, deactivate' : 'Yes, activate';
        const confirmColor = isDisabling ? '#dc2626' : '#059669';

        Swal.fire({
            title: actionTitle,
            html: actionText,
            icon: isDisabling ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Cancel',
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#94a3b8',
        }).then((result) => {
            if (result.isConfirmed) {
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
                .then(parseJsonResponse)
                .then(({ status, ok, data }) => {
                    if (data && data.csrf_token) {
                        setCsrfToken(data.csrf_token);
                    }
                    if (ok && data && data.success) {
                        Swal.fire({
                            title: isDisabling ? 'Account Deactivated' : 'Account Activated',
                            text: data.message || 'Status updated successfully.',
                            icon: 'success',
                            confirmButtonColor: '#002c76',
                        });
                        loadUsers();
                        loadAdminStats();
                    } else {
                        const msg = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh the page.' : 'Failed to update user status');
                        if (msg.toLowerCase().includes('own') || msg.toLowerCase().includes('primary')) {
                            showSelfModifyModal(msg);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: msg,
                                icon: 'error',
                                confirmButtonColor: '#002c76',
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error toggling status:', error);
                    Swal.fire({
                        title: 'Request Failed',
                        text: 'A network or server error occurred while updating status.',
                        icon: 'error',
                        confirmButtonColor: '#002c76',
                    });
                });
            }
        });
    }

    function filterUsers() {
        const searchValue = document.getElementById('userSearch').value.toLowerCase().trim();
        const roleFilter = document.getElementById('roleFilter').value.toUpperCase();
        const statusFilter = document.getElementById('statusFilter').value.toUpperCase();

        if (!usersList || usersList.length === 0) return;

        const filtered = usersList.filter(user => {
            const username = (user.username || '').toLowerCase();
            const fullName = ((user.first_name || '') + ' ' + (user.last_name || '')).toLowerCase();
            const email = (user.email || '').toLowerCase();
            const userRole = (user.role_name || 'NO ROLE').toUpperCase();
            const isActive = user.is_active !== undefined ? Number(user.is_active) === 1 : true;
            const userStatus = isActive ? 'ACTIVE' : 'DISABLED';

            const matchesSearch = !searchValue || username.includes(searchValue) || fullName.includes(searchValue) || email.includes(searchValue);
            const matchesRole = !roleFilter || userRole === roleFilter;
            const matchesStatus = !statusFilter || userStatus === statusFilter;

            return matchesSearch && matchesRole && matchesStatus;
        });

        renderUserRows(filtered);
    }

    function showSelfModifyModal(message) {
        document.getElementById('selfModifyMessage').textContent = message || 'You cannot change your own role or administrative status.';
        document.getElementById('selfModifyModal').classList.add('active');
    }

    function closeSelfModifyModal() {
        document.getElementById('selfModifyModal').classList.remove('active');
    }

    function closeSuccessModal() {
        document.getElementById('successModal').classList.remove('active');
        loadUsers();
        loadAdminStats();
    }

    // ==========================================
    // EDIT USER DETAILS (NAME & EMAIL)
    // ==========================================
    function openEditUserModal(userId) {
        const user = usersList.find(u => Number(u.id) === Number(userId));
        if (!user) return;

        document.getElementById('editUserId').value = user.id;
        document.getElementById('editFirstName').value = user.first_name || '';
        document.getElementById('editLastName').value = user.last_name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editUserModalError').classList.remove('show');
        document.getElementById('editUserModal').classList.add('active');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.remove('active');
    }

    function submitEditUser() {
        const userId = document.getElementById('editUserId').value;
        const firstName = document.getElementById('editFirstName').value.trim();
        const lastName = document.getElementById('editLastName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const errorDiv = document.getElementById('editUserModalError');

        if (!firstName || !lastName || !email) {
            errorDiv.textContent = 'Please complete First Name, Last Name, and Email Address.';
            errorDiv.classList.add('show');
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        formData.append('email', email);
        formData.append('<?= csrf_token() ?>', getCsrfToken());

        fetch('<?= base_url('admin/updateUser') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            }
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data && data.success) {
                closeEditUserModal();
                Swal.fire({
                    title: 'Updated!',
                    text: data.message || 'User details have been updated.',
                    icon: 'success',
                    confirmButtonColor: '#002c76',
                });
                loadUsers();
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh.' : 'Failed to update user details.');
                errorDiv.classList.add('show');
            }
        })
        .catch(err => {
            console.error('Update user error:', err);
            errorDiv.textContent = 'A network or server error occurred.';
            errorDiv.classList.add('show');
        });
    }

    // ==========================================
    // RESET USER PASSWORD
    // ==========================================
    function openResetPasswordModal(userId) {
        const user = usersList.find(u => Number(u.id) === Number(userId));
        if (!user) return;

        const fullName = ((user.first_name || '') + ' ' + (user.last_name || '')).trim() || user.username;
        document.getElementById('resetUserId').value = user.id;
        document.getElementById('resetTargetName').textContent = fullName;
        document.getElementById('resetTargetEmail').textContent = user.email || 'No email on file';
        document.getElementById('manualNewPassword').value = '';
        document.getElementById('resetPasswordModalError').classList.remove('show');
        document.getElementById('resetPasswordModal').classList.add('active');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.remove('active');
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
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('mode', mode);
        formData.append('<?= csrf_token() ?>', getCsrfToken());

        if (mode === 'manual') {
            const newPassword = document.getElementById('manualNewPassword').value.trim();
            if (!newPassword) {
                errorDiv.textContent = 'Please enter or generate a new password.';
                errorDiv.classList.add('show');
                return;
            }
            formData.append('new_password', newPassword);
        }

        fetch('<?= base_url('admin/resetUserPassword') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            }
        })
        .then(parseJsonResponse)
        .then(({ status, ok, data }) => {
            if (data && data.csrf_token) setCsrfToken(data.csrf_token);
            if (ok && data && data.success) {
                closeResetPasswordModal();
                Swal.fire({
                    title: 'Password Updated!',
                    text: data.message || 'The user password was successfully updated.',
                    icon: 'success',
                    confirmButtonColor: '#002c76',
                });
            } else {
                errorDiv.textContent = (data && data.message) ? data.message : (status === 403 ? 'Access forbidden or session expired. Please refresh.' : 'Failed to reset password.');
                errorDiv.classList.add('show');
            }
        })
        .catch(err => {
            console.error('Password reset error:', err);
            errorDiv.textContent = 'A network or server error occurred.';
            errorDiv.classList.add('show');
        });
    }

    // Modal background dismissals
    document.getElementById('assignRoleModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRoleModal();
    });
    document.getElementById('editUserModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditUserModal();
    });
    document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeResetPasswordModal();
    });
    document.getElementById('selfModifyModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSelfModifyModal();
    });
    document.getElementById('successModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSuccessModal();
    });
</script>
<?= $this->endSection() ?>
