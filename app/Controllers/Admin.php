<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\PasswordResetModel;
use App\Libraries\AuthMailer;

class Admin extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Check if user is admin. Returns null if authorized, or a Response/Redirect if not.
     */
    private function checkAdminAccess(bool $isAjax = false)
    {
        $isRequestAjax = $isAjax || $this->request->isAJAX();

        if (!session()->get('logged_in')) {
            if ($isRequestAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.'
                ])->setStatusCode(401);
            }
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['is_admin']) {
            if ($isRequestAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized: Administrator access required.'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page');
        }

        return null;
    }

    /**
     * Display admin panel page
     */
    public function panel()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        return view('admin_panel', [
            'roles' => $this->roleModel->getAllRoles()
        ]);
    }

    /**
     * View all users and manage roles
     */
    public function users()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $users = $this->userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        // Decrypt user fields for display. Admin panel may show plaintext
        // email so request it explicitly from the model.
        foreach ($users as &$u) {
            $u = $this->userModel->decryptUserRow($u, true);
            // sanitize: if decryptUserRow returned a hash by mistake clear it
            if (isset($u['email']) && preg_match('/^[0-9a-f]{64}$/i', (string) $u['email'])) {
                $u['email'] = '';
            }
            $u['is_active'] = isset($u['is_active']) ? (int) $u['is_active'] : 1;
        }
        unset($u);

        $roles = $this->roleModel->getAllRoles();

        $data = [
            'users' => $users,
            'roles' => $roles,
            'title' => 'User Management'
        ];

        return view('admin/users', $data);
    }

    /**
     * Assign role to user
     */
    public function assignRole()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');
        $roleId = $this->request->getPost('role_id');

        if (!$userId || $userId === 'null' || $userId === 'undefined' || !is_numeric($userId) || $roleId === null || $roleId === '') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user or role selection',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;

        // Verify user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Prevent changing own role/admin status
        if ($userId === (int) session()->get('user_id')) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'You cannot change your own role or admin status',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Check if clearing role
        if ($roleId === '0' || $roleId === 'none' || $roleId === 'null') {
            $updateData = [
                'role_id'  => null,
                'is_admin' => 0
            ];
            $assignedRoleName = 'No Role';
        } else {
            // Verify role exists
            $role = $this->roleModel->find($roleId);
            if (!$role) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'Selected role does not exist',
                    'csrf_token' => csrf_hash(),
                ])->setStatusCode(400);
            }

            // Update user role and admin status
            // If assigning ADMIN role (id=1), set is_admin=1, otherwise set is_admin=0
            $updateData = [
                'role_id'  => (int) $roleId,
                'is_admin' => ((int) $roleId === 1) ? 1 : 0
            ];
            $assignedRoleName = $role['name'] ?? 'Role';
        }

        if ($this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success'    => true,
                'role_id'    => $updateData['role_id'],
                'role_name'  => $assignedRoleName,
                'is_admin'   => $updateData['is_admin'],
                'message'    => "Role assigned successfully: {$assignedRoleName}",
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to assign role',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Toggle active/disabled status for a user
     */
    public function toggleStatus()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId || $userId === 'null' || $userId === 'undefined' || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;

        $currentAdminId = (int) session()->get('user_id');
        if ($userId === $currentAdminId) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'You cannot disable your own administrator account',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Prevent disabling primary admin username
        if (strtolower($user['username'] ?? '') === 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'The primary administrator account cannot be disabled',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $currentStatus = isset($user['is_active']) ? (int) $user['is_active'] : 1;
        $newStatus = ($currentStatus === 1) ? 0 : 1;

        if ($this->userModel->update($userId, ['is_active' => $newStatus])) {
            $actionLabel = ($newStatus === 1) ? 'enabled' : 'disabled';
            return $this->response->setJSON([
                'success'    => true,
                'is_active'  => $newStatus,
                'message'    => "User account has been {$actionLabel} successfully",
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to update user status',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Admin: Edit user's name and email
     */
    public function updateUser()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;
        $firstName = trim((string) $this->request->getPost('first_name'));
        $lastName  = trim((string) $this->request->getPost('last_name'));
        $email     = trim((string) $this->request->getPost('email'));

        if ($firstName === '' || $lastName === '' || $email === '') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'First name, last name, and email are all required.',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Please provide a valid email address.',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(404);
        }

        // Check email uniqueness if email is changed
        $existing = $this->userModel->getUserByEmail($email);
        if ($existing && (int) $existing['id'] !== $userId) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'This email address is already registered to another user.',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Prepare encrypted PII
        $updateData = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
        ];
        $prepared = $this->userModel->prepareForInsert($updateData);

        $this->userModel->skipValidation(true);
        if ($this->userModel->update($userId, $prepared)) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'User details updated successfully.',
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to update user details.',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Admin: Reset password for another user (via email link or direct manual password)
     */
    public function resetUserPassword()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(404);
        }

        $mode = $this->request->getPost('mode') ?: 'manual';

        if ($mode === 'email') {
            $user = $this->userModel->decryptUserRow($user);
            $email = $this->userModel->decryptValue($user['email_enc'] ?? '') ?: ($user['email'] ?? '');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'User does not have a valid email address on file.',
                    'csrf_token' => csrf_hash(),
                ])->setStatusCode(400);
            }

            $key = env('encryption.key') ?: getenv('encryption.key');
            $emailHash = hash_hmac('sha256', mb_strtolower(trim($email)), $key);
            $resetModel = new PasswordResetModel();
            $rawToken = $resetModel->createToken($userId, $emailHash);

            $mailer = new AuthMailer();
            $mailer->sendPasswordReset($email, $user['first_name'] ?: $user['username'], $rawToken);

            return $this->response->setJSON([
                'success'    => true,
                'message'    => "Password reset link has been sent to {$email}.",
                'csrf_token' => csrf_hash(),
            ]);
        }

        // Manual password change
        $newPassword = (string) $this->request->getPost('new_password');
        if (empty($newPassword)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Password cannot be empty.',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $hasUppercase = preg_match('/[A-Z]/', $newPassword);
        $hasLowercase = preg_match('/[a-z]/', $newPassword);
        $hasNumber    = preg_match('/[0-9]/', $newPassword);
        $hasSpecial   = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $newPassword);

        if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial || strlen($newPassword) < 8) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($this->userModel->update($userId, ['password' => $hash])) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Password has been successfully updated for this user.',
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to update password.',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Clear role from a user
     */
    public function clearRole()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId || $userId === 'null' || $userId === 'undefined' || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;

        // Verify user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Prevent clearing own role/admin status
        if ($userId === (int) session()->get('user_id')) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'You cannot clear your own role or revoke your own admin status',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Clear role and admin status
        $updateData = [
            'role_id'  => null,
            'is_admin' => 0
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Role cleared successfully',
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to clear role',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Create the first admin user
     */
    public function createFirstAdmin()
    {
        // Permanent lock: once an admin has ever been created, this endpoint is sealed
        $lockFile = WRITEPATH . 'admin_bootstrap.lock';
        if (is_file($lockFile)) {
            return redirect()->to('/login')->with('error', 'Initial setup has already been completed');
        }

        // Check if any admin exists
        $adminExists = $this->userModel->where('is_admin', 1)->first();
        if ($adminExists) {
            // Create the lock file retroactively
            @file_put_contents($lockFile, date('c'));
            return redirect()->to('/login')->with('error', 'Admin user already exists');
        }

        return view('admin/create_first_admin', [
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
        ]);
    }

    /**
     * Store the first admin user
     */
    public function storeFirstAdmin()
    {
        // Permanent lock: once an admin has ever been created, this endpoint is sealed
        $lockFile = WRITEPATH . 'admin_bootstrap.lock';
        if (is_file($lockFile)) {
            return redirect()->to('/login')->with('error', 'Initial setup has already been completed');
        }

        // Check if any admin exists
        $adminExists = $this->userModel->where('is_admin', 1)->first();
        if ($adminExists) {
            @file_put_contents($lockFile, date('c'));
            return redirect()->to('/login')->with('error', 'Admin user already exists');
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'province' => $this->request->getPost('province'),
            'municipality' => $this->request->getPost('municipality'),
            'is_admin' => 1,
            'role_id' => null, // Admins don't have a specific role
        ];

        $password_confirm = $this->request->getPost('password_confirm');

        // Password validation
        if ($data['password'] !== $password_confirm) {
            return redirect()->back()->with('error', 'Passwords do not match')->withInput();
        }

        // Password strength validation
        $hasUppercase = preg_match('/[A-Z]/', $data['password']);
        $hasLowercase = preg_match('/[a-z]/', $data['password']);
        $hasNumber = preg_match('/[0-9]/', $data['password']);
        $hasSpecial = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $data['password']);

        if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial || strlen($data['password']) < 8) {
            return redirect()->back()->with('error', 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character')->withInput();
        }

        if (!$this->isValidRegion1Location((string) $data['province'], (string) $data['municipality'])) {
            return redirect()->back()->with('error', 'Please select a valid Region 1 province and municipality')->withInput();
        }

        // Ensure email uniqueness using deterministic hash lookup
        if ($this->userModel->getUserByEmail($data['email'])) {
            return redirect()->back()->with('error', 'This email is already registered.')->withInput();
        }

        // Validate plaintext fields using model rules before we remove them
        if (! $this->userModel->validate($data)) {
            $errors = $this->userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Validation failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        // Prepare encrypted PII and remove plaintext keys (DB stores encrypted PII only)
        $data = $this->userModel->prepareForInsert($data);

        // Skip validation here because we validated plaintext above
        $this->userModel->skipValidation(true);

        // Save admin to database
        if (!$this->userModel->insert($data)) {
            $errors = $this->userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Admin creation failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        // Seal the bootstrap endpoint permanently
        @file_put_contents(WRITEPATH . 'admin_bootstrap.lock', date('c'));

        return redirect()->to('/login')->with('success', 'Admin user created successfully! Please log in.');
    }

    /**
     * Grant admin privileges to a user
     */
    public function grantAdmin()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId || $userId === 'null' || $userId === 'undefined' || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Grant admin privileges and assign ADMIN role
        if ($this->userModel->update($userId, ['is_admin' => 1, 'role_id' => 1])) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Admin privileges granted successfully',
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to grant admin privileges',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Revoke admin privileges from a user
     */
    public function revokeAdmin()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId || $userId === 'null' || $userId === 'undefined' || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid user ID',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $userId = (int) $userId;

        // Prevent revoking your own admin privileges
        if ($userId === (int) session()->get('user_id')) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'You cannot revoke your own admin privileges',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'User not found in system',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Prevent revoking primary admin
        if (strtolower($user['username'] ?? '') === 'admin') {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'You cannot revoke privileges from the primary administrator',
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(400);
        }

        // Revoke admin privileges and clear role
        if ($this->userModel->update($userId, ['is_admin' => 0, 'role_id' => null])) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Admin privileges revoked successfully',
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'    => false,
            'message'    => 'Failed to revoke admin privileges',
            'csrf_token' => csrf_hash(),
        ])->setStatusCode(500);
    }

    /**
     * Get all users as JSON (for admin dashboard)
     */
    public function getUsers()
    {
        $accessCheck = $this->checkAdminAccess(true);
        if ($accessCheck) {
            return $accessCheck;
        }

        $users = $this->userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        // Decrypt before returning JSON and include plaintext email for admin consumers
        foreach ($users as &$u) {
            $u = $this->userModel->decryptUserRow($u, true);
            if (isset($u['email']) && preg_match('/^[0-9a-f]{64}$/i', (string) $u['email'])) {
                $u['email'] = '';
            }
            $u['is_active'] = isset($u['is_active']) ? (int) $u['is_active'] : 1;
            // SECURITY: Strip password hash and internal ciphertexts before sending to client
            unset(
                $u['password'],
                $u['email_hash'],
                $u['email_enc'],
                $u['first_name_enc'],
                $u['last_name_enc'],
                $u['contact_number_enc']
            );
        }
        unset($u);

        return $this->response->setJSON([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Get admin statistics
     */
    public function getStats()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        $totalUsers = $this->userModel->countAll();
        $adminUsers = $this->userModel->where('is_admin', 1)->countAllResults();
        $regularUsers = $totalUsers - $adminUsers;
        $unassignedRoles = $this->userModel->where('role_id', null)->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'totalUsers' => $totalUsers,
            'adminUsers' => $adminUsers,
            'regularUsers' => $regularUsers,
            'unassignedRoles' => $unassignedRoles
        ]);
    }



    // Repair encryption for a user by re-saving plaintext through the model callbacks.
    // This will encrypt fields using the current application encryption key.
    public function repairEncryption(int $id = 1)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(404);
        }

        // Attempt to recover plausible plaintext from existing values if possible
        // Prefer decrypting from the encrypted columns; do not rely on legacy `email`/`first_name` plaintext.
        $email = $this->userModel->decryptValue($user['email_enc'] ?? ($user['email'] ?? ''));
        $first = $this->userModel->decryptValue($user['first_name_enc'] ?? ($user['first_name'] ?? ''));
        $last = $this->userModel->decryptValue($user['last_name_enc'] ?? ($user['last_name'] ?? ''));

        // If name fields are still not decryptable, fall back to reasonable defaults
        if (empty($first) || preg_match('/^[A-F0-9]{64}$/i', $first)) {
            $first = 'Admin';
        }
        if (empty($last) || preg_match('/^[A-F0-9]{64}$/i', $last)) {
            $last = 'User';
        }

        $reData = [
            'first_name' => $first,
            'last_name'  => $last,
            'email'      => $email ?: ($user['username'] . '@example.local'),
        ];
        $reData = $this->userModel->prepareForInsert($reData);
        $this->userModel->skipValidation(true)->update($id, $reData);

        return $this->response->setJSON(['success' => true, 'message' => 'Re-encrypted user data']);
    }

    /**
     * Show the backup/restore page.
     */
    public function backup()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        // read last backup timestamp from writable folder
        $last = null;
        $path = WRITEPATH . 'backup_timestamp';
        if (is_file($path)) {
            $ts = intval(file_get_contents($path));
            if ($ts > 0) {
                $last = date('F j, Y g:i a', $ts);
            }
        }

        return view('admin/backup', ['lastBackup' => $last]);
    }

    /**
     * Export a JSON backup of the administrator record.
     *
     * The download is triggered directly, the file contains the raw row as
     * returned by the `users` table (encrypted fields already present).
     */
    public function exportBackup()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $admin = $this->userModel
            ->where('username', 'admin')
            ->orWhere('id', 1)
            ->first();

        if (! $admin) {
            return redirect()->to('/admin/backup')->with('error', 'Administrator record not found');
        }

        $json = json_encode($admin, JSON_PRETTY_PRINT);

        // record backup time
        @file_put_contents(WRITEPATH . 'backup_timestamp', time());

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="admin-backup-'.date('Ymd-His').'.json"')
            ->setBody($json);
    }

    /**
     * Restore an administrator record from an uploaded JSON file.
     */
    public function restoreBackup()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $file = $this->request->getFile('backup_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'No valid file uploaded');
        }

        $contents = file_get_contents($file->getTempName());
        $data = json_decode($contents, true);
        if (! is_array($data)) {
            return redirect()->back()->with('error', 'Invalid JSON file');
        }

        // Ensure we are importing an admin (simple sanity check)
        if (empty($data['username']) || $data['username'] !== 'admin') {
            return redirect()->back()->with('error', 'Backup does not appear to contain an admin account');
        }

        // Whitelist only known safe columns to prevent arbitrary field injection
        $allowed = [
            'id', 'username', 'password', 'email', 'email_hash', 'email_enc',
            'first_name', 'first_name_enc', 'last_name', 'last_name_enc',
            'contact_number_enc', 'agency', 'province', 'municipality',
            'is_admin', 'is_active', 'role_id', 'created_at', 'updated_at',
        ];
        $safeData = array_intersect_key($data, array_flip($allowed));

        // Force admin and active flags – backup restore must not downgrade the admin record
        $safeData['is_admin'] = 1;
        $safeData['is_active'] = 1;

        try {
            // Use table builder replace to bypass model callbacks/validation, since
            // the row already contains hashed/encrypted values.
            $builder = $this->userModel->builder();
            $builder->replace($safeData);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to restore backup: ' . $e->getMessage());
        }

        return redirect()->to('/admin/backup')->with('success', 'Administrator record restored from backup');
    }
}

