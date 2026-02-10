<?php

namespace App\Controllers;

use App\Models\IncidentReportModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'hideNavbar' => true,
            'hideSidebar' => true,
            'hideFooter' => true,
        ]);
    }

    public function authenticate()
    {
        $emailOrUsername = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate inputs
        if (empty($emailOrUsername) || empty($password)) {
            return redirect()->back()->with('error', 'Please provide email/username and password');
        }

        // Check user in database by email or username
        $userModel = new UserModel();
        $user = $userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->groupStart()
                ->where('users.email', $emailOrUsername)
                ->orWhere('users.username', $emailOrUsername)
            ->groupEnd()
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid email/username or password');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email/username or password');
        }

        // Store session data
        session()->set([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'province' => $user['province'],
            'municipality' => $user['municipality'],
            'contact_number' => $user['contact_number'] ?? null,
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'] ?? 'No Role',
            'is_admin' => $user['is_admin'],
            'logged_in' => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'hideNavbar' => true,
            'hideSidebar' => true,
            'hideFooter' => true,
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
        ]);
    }

    public function store_register()
    {
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'province' => $this->request->getPost('province'),
            'municipality' => $this->request->getPost('municipality'),
            'password' => $this->request->getPost('password'),
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

        // Save to database
        $userModel = new UserModel();
        
        if (!$userModel->insert($data)) {
            $errors = $userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Registration failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->to('/login')->with('success', 'Registration successful! Please log in.');
    }

    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('dashboard');
    }

    public function logout()
    {
        $session = session();
        $session->remove([
            'user_id',
            'email',
            'username',
            'first_name',
            'last_name',
            'province',
            'municipality',
            'contact_number',
            'role_id',
            'is_admin',
            'logged_in'
        ]);
        $session->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function ordinance()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('ordinance');
    }

    public function incident_report()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $rows = $db->table('incident_reports ir')
            ->select('ir.*, COUNT(ira.id) as attachments_count')
            ->join('incident_report_attachments ira', 'ira.incident_n = ir.n', 'left')
            ->groupBy('ir.n')
            ->orderBy('ir.n', 'asc')
            ->get()
            ->getResultArray();

        return view('incident_report', [
            'initialRows' => $rows,
            'roleName' => strtoupper(trim((string) session()->get('role_name'))),
            'isAdmin' => (bool) session()->get('is_admin'),
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
        ]);
    }

    public function pops()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('pops');
    }

    public function user_profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userId = (int) session()->get('user_id');
        $profile = $userModel->find($userId) ?? [];

        return view('user_profile', [
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
            'profile' => $profile,
        ]);
    }

    public function update_profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = (int) session()->get('user_id');
        $data = [
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'last_name' => trim((string) $this->request->getPost('last_name')),
            'username' => trim((string) $this->request->getPost('username')),
            'contact_number' => trim((string) $this->request->getPost('contact_number')),
            'province' => trim((string) $this->request->getPost('province')),
            'municipality' => trim((string) $this->request->getPost('municipality')),
        ];

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '') {
            return redirect()->back()->with('error', 'Please complete all required fields.')->withInput();
        }

        if ($data['contact_number'] !== '' && !preg_match('/^[0-9]{11}$/', $data['contact_number'])) {
            return redirect()->back()->with('error', 'Please enter a valid 11-digit contact number.')->withInput();
        }

        if (!$this->isValidRegion1Location($data['province'], $data['municipality'])) {
            return redirect()->back()->with('error', 'Please select a valid Region 1 province and municipality.')->withInput();
        }

        $userModel = new UserModel();

        $existingUsername = $userModel
            ->where('username', $data['username'])
            ->where('id !=', $userId)
            ->first();
        if ($existingUsername) {
            return redirect()->back()->with('error', 'This username is already taken.')->withInput();
        }

        $userModel->skipValidation(true);
        if (!$userModel->update($userId, $data)) {
            $errors = $userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Profile update failed.';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        session()->set([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'province' => $data['province'],
            'municipality' => $data['municipality'],
            'contact_number' => $data['contact_number'],
        ]);

        return redirect()->to('/user-profile')->with('profile_success', 'Profile updated successfully!');
    }
}
