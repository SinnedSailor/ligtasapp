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

        $incidentReportModel = new IncidentReportModel();
        $rows = $incidentReportModel
            ->orderBy('n', 'asc')
            ->findAll();

        return view('incident_report', [
            'initialRows' => $rows,
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

        return view('user_profile');
    }
}
