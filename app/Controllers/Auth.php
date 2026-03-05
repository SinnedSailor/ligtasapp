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
            return redirect()->back()->withInput()->with('error', 'Please provide email/username and password');
        }

        // Check user in database by email (hashed) or username
        $userModel = new UserModel();

        // If input looks like an email, compute deterministic hash and compare to stored hashed email
        if (strpos($emailOrUsername, '@') !== false) {
            $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
            $emailHash = hash_hmac('sha256', mb_strtolower(trim($emailOrUsername)), $key);

            $user = $userModel->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->groupStart()
                    ->where('users.email_hash', $emailHash)
                    ->orWhere('users.username', $emailOrUsername)
                ->groupEnd()
                ->first();
        } else {
            $user = $userModel->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.username', $emailOrUsername)
                ->first();
        }

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid email/username or password');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email/username or password');
        }

        // Decrypt user fields for session display
        $userModel = new \App\Models\UserModel();
        $user = $userModel->decryptUserRow($user);

        // Prefer decrypted plaintext email (from `email_enc`) for session storage if available
        $emailPlain = $userModel->decryptValue($user['email_enc'] ?? '');

        // Store session data
        session()->set([
            'user_id' => $user['id'],
            'email' => $emailPlain ?: ($user['email'] ?? ''),
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

        session()->setFlashdata('login_success', true);
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
        // Log incoming POST for debugging when validation fails in the UI
        log_message('debug', '[Auth::store_register] POST keys: ' . json_encode(array_keys($this->request->getPost())));

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

        // Validate incoming plaintext against the model's rules BEFORE we remove plaintext fields.
        $valid = $userModel->validate($data);
        log_message('debug', '[Auth::store_register] model->validate => ' . ($valid ? 'true' : 'false'));
        if (! $valid) {
            $errors = $userModel->errors();
            log_message('debug', '[Auth::store_register] validation errors: ' . json_encode($errors));
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Validation failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        // Ensure email uniqueness using the deterministic hash lookup
        if ($userModel->getUserByEmail($data['email'])) {
            return redirect()->back()->with('error', 'This email is already registered.')->withInput();
        }

        // Prepare encrypted PII and remove plaintext keys (DB stores encrypted PII only)
        $data = $userModel->prepareForInsert($data);

        // We've already validated plaintext — skip validation for the insert since
        // validation rules require plaintext fields that are intentionally removed.
        $userModel->skipValidation(true);

        $insertId = $userModel->insert($data);
        if ($insertId === false) {
            $errors = $userModel->errors();
            log_message('debug', '[Auth::store_register] insert failed, errors: ' . json_encode($errors));
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Registration failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
        log_message('debug', '[Auth::store_register] user inserted id=' . (int) $insertId);

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

        // legacy access point; forward to documents page
        return redirect()->to('/documents');
    }

    public function incident_report()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $query = $db->table('incident_reports ir')
            ->select('ir.*, COUNT(ira.id) as attachments_count')
            ->join('incident_report_attachments ira', 'ira.incident_n = ir.n', 'left')
            ->groupBy('ir.n')
            ->orderBy('ir.n', 'asc');

        // Focal users should only see incidents that have been approved.
        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName === 'FOCAL') {
            $query->where('ir.review_status', 'approved');
        }

        $rows = $query->get()->getResultArray();

        // Decrypt victim names for display when possible
        $incidentModel = new \App\Models\IncidentReportModel();
        foreach ($rows as &$r) {
            $r = $incidentModel->decryptRow($r);
            // convert stored gender codes to human-readable labels so the
            // frontend doesn't have to worry about normalization artifacts
            if (isset($r['gender']) && is_string($r['gender'])) {
                $g = trim(strtolower($r['gender']));
                if ($g === 'm') {
                    $r['gender'] = 'Male';
                } elseif ($g === 'f') {
                    $r['gender'] = 'Female';
                }
            }
        }
        unset($r);

        // apply role-based filtering once decryption is done as an extra safety
        $roleName = strtoupper(trim((string) session()->get('role_name')));
        $incidentCtrl = new \App\Controllers\IncidentReport();
        $rows = $incidentCtrl->filterRowsForRole($rows, $roleName);

        // gather known location categories, occasions, and occupations for autocomplete suggestions
        $locationModel = new \App\Models\IncidentReportModel();
        $locationCategories = $locationModel->getDistinctLocationCategories();
        $occasions = $locationModel->getDistinctOccasions();
        $occupations = $locationModel->getDistinctOccupations();
        $otherFactors = $locationModel->getDistinctFactors();

        return view('incident_report', [
            'initialRows' => $rows,
            'roleName' => strtoupper(trim((string) session()->get('role_name'))),
            'isAdmin' => (bool) session()->get('is_admin'),
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
            'locationCategories' => $locationCategories,
            'occasions' => $occasions,
            'occupations' => $occupations,
            'otherFactors' => $otherFactors,
        ]);
    }

    public function pops()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // POPS is now part of the documents workflow
        return redirect()->to('/documents');
    }

    public function user_profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userId = (int) session()->get('user_id');
        $profile = $userModel->find($userId) ?? [];
        if (!empty($profile)) {
            // ask for plaintext email this time; controller will still apply
            // further fallback logic below based on session values.
            $profile = $userModel->decryptUserRow($profile, true);

            // compute a safe value we can show in the form.  decryptUserRow with
            // revealEmail=true will already have placed plaintext into
            // $profile['email'] when possible; if it contains a hash we clear it
            // so the form shows an empty field and forces the user to re-enter.
            if (isset($profile['email']) && preg_match('/^[0-9a-f]{64}$/i', (string) $profile['email'])) {
                $profile['display_email'] = '';
            } else {
                $profile['display_email'] = $profile['email'] ?? '';
            }
        }

        // if still empty, try session value (e.g. freshly logged in)
        if (empty($profile['display_email'])) {
            $sessionEmail = session()->get('email') ?? '';
            if ($sessionEmail && !preg_match('/^[0-9a-f]{64}$/i', (string) $sessionEmail)) {
                $profile['display_email'] = $sessionEmail;
            }
        }

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

        // allow email address to be updated as well; user may need to re-enter if
        // the stored value was replaced by a hash earlier.
        $emailInput = trim((string) $this->request->getPost('email'));
        if ($emailInput !== '') {
            $data['email'] = $emailInput;
        }

        // basic required-checks
        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '') {
            return redirect()->back()->with('error', 'Please complete all required fields.')->withInput();
        }

        if ($data['contact_number'] !== '' && !preg_match('/^[0-9]{11}$/', $data['contact_number'])) {
            return redirect()->back()->with('error', 'Please enter a valid 11-digit contact number.')->withInput();
        }

        // validate email if supplied (field is required on form)
        if (isset($data['email'])) {
            if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Please provide a valid email address.')->withInput();
            }
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

        // if email is being updated, ensure uniqueness as well
        if (isset($data['email'])) {
            $existingEmail = $userModel->getUserByEmail($data['email']);
            if ($existingEmail && (int) $existingEmail['id'] !== $userId) {
                return redirect()->back()->with('error', 'This email is already registered.')->withInput();
            }
        }

        // Ensure encrypted PII columns are set when users update their profile.
        $data = $userModel->prepareForInsert($data);

        $userModel->skipValidation(true);
        if (!$userModel->update($userId, $data)) {
            $errors = $userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Profile update failed.';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        // Update session using decrypted values where available
        $profile = $userModel->find($userId);
        $profile = $userModel->decryptUserRow($profile);

        // put plaintext email into session if possible, otherwise keep existing value
        $emailPlain = $userModel->decryptValue($profile['email_enc'] ?? ($profile['email'] ?? ''));

        session()->set([
            'first_name' => $profile['first_name'] ?? '',
            'last_name' => $profile['last_name'] ?? '',
            'username' => $profile['username'] ?? '',
            'province' => $profile['province'] ?? '',
            'municipality' => $profile['municipality'] ?? '',
            'contact_number' => $profile['contact_number'] ?? '',
            'email' => $emailPlain ?: session()->get('email'),
        ]);

        return redirect()->to('/user-profile')->with('profile_success', 'Profile updated successfully!');
    }
}
