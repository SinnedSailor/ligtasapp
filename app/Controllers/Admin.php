<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

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
     * Check if user is admin
     */
    private function checkAdminAccess()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['is_admin']) {
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

        return view('admin_panel');
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

        // Decrypt user fields for display. For admin UI/API show plaintext email
        foreach ($users as &$u) {
            $u = $this->userModel->decryptUserRow($u);
            // If we have an encrypted email, decrypt it for display (admin-only)
            $plainEmail = $this->userModel->decryptValue($u['email_enc'] ?? '');
            if ($plainEmail) {
                $u['email'] = $plainEmail;
            }
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
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');
        $roleId = $this->request->getPost('role_id');

        if (!$userId || $roleId === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user or role'
            ])->setStatusCode(400);
        }

        // Verify user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        // Prevent changing own role/admin status
        if ($userId == session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You cannot change your own role or admin status'
            ])->setStatusCode(400);
        }

        // Verify role exists
        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ])->setStatusCode(404);
        }

        // Update user role and admin status
        // If assigning ADMIN role (id=1), set is_admin=1, otherwise set is_admin=0
        $updateData = [
            'role_id' => $roleId,
            'is_admin' => ($roleId == 1) ? 1 : 0
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role assigned successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to assign role'
        ])->setStatusCode(500);
    }

    /**
     * Clear role from a user
     */
    public function clearRole()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user'
            ])->setStatusCode(400);
        }

        // Verify user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        // Prevent clearing own role/admin status
        if ($userId == session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You cannot clear your own role or revoke your own admin status'
            ])->setStatusCode(400);
        }

        // Clear role and admin status
        $updateData = [
            'role_id' => null,
            'is_admin' => 0
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role cleared successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to clear role'
        ])->setStatusCode(500);
    }

    /**
     * Create the first admin user
     */
    public function createFirstAdmin()
    {
        // Check if any admin exists
        $adminExists = $this->userModel->where('is_admin', 1)->first();
        if ($adminExists) {
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
        // Check if any admin exists
        $adminExists = $this->userModel->where('is_admin', 1)->first();
        if ($adminExists) {
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

        return redirect()->to('/login')->with('success', 'Admin user created successfully! Please log in.');
    }

    /**
     * Grant admin privileges to a user
     */
    public function grantAdmin()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        // Grant admin privileges and assign ADMIN role
        if ($this->userModel->update($userId, ['is_admin' => 1, 'role_id' => 1])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Admin privileges granted'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to grant admin privileges'
        ])->setStatusCode(500);
    }

    /**
     * Revoke admin privileges from a user
     */
    public function revokeAdmin()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $userId = $this->request->getPost('user_id');

        // Prevent revoking your own admin privileges
        if ($userId == session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You cannot revoke your own admin privileges'
            ])->setStatusCode(400);
        }

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        // Revoke admin privileges and clear role
        if ($this->userModel->update($userId, ['is_admin' => 0, 'role_id' => null])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Admin privileges revoked'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to revoke admin privileges'
        ])->setStatusCode(500);
    }

    /**
     * Get all users as JSON (for admin dashboard)
     */
    public function getUsers()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        $users = $this->userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        // Decrypt before returning JSON and include plaintext email for admin consumers
        foreach ($users as &$u) {
            $u = $this->userModel->decryptUserRow($u);
            $plainEmail = $this->userModel->decryptValue($u['email_enc'] ?? '');
            if ($plainEmail) {
                $u['email'] = $plainEmail;
            }
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

    // Debugging helper — show raw DB values and decrypted values for user id=1
    public function debugDecryptUser(int $id = 1)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return $this->response->setJSON(['found' => false])->setStatusCode(404);
        }

        $decrypted = $this->userModel->decryptUserRow($user);

        // Additional raw decryption attempts for debugging
        $encrypter = \Config\Services::encrypter();
        $debug = [];
        foreach (['first_name','last_name','email','contact_number'] as $f) {
            $debug[$f] = ['raw' => $user[$f] ?? null, 'decrypted_attempt' => null, 'error' => null];
            if (!empty($user[$f])) {
                try {
                    $decoded = base64_decode($user[$f], true);
                    $plain = $encrypter->decrypt($decoded);
                    $debug[$f]['decrypted_attempt'] = $plain === false ? null : $plain;
                } catch (\Throwable $e) {
                    $debug[$f]['error'] = $e->getMessage();
                }
            }
        }

        return $this->response->setJSON([
            'found' => true,
            'raw' => $user,
            'decrypted' => $decrypted,
            'debug_decrypt' => $debug,
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

        $this->userModel->update($id, [
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email ?: ($user['username'] . '@example.local'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Re-encrypted user data']);
    }
}

