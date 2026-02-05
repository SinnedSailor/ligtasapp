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

        return view('admin/create_first_admin');
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
}
