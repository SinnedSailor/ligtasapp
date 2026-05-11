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

    if (empty($emailOrUsername) || empty($password)) {
        return redirect()->back()->withInput()->with('error', 'Please provide credentials');
    }

    $userModel = new \App\Models\UserModel();

    // 1. Find User (Deterministic Hash or Username)
    if (strpos($emailOrUsername, '@') !== false) {
        $key = env('encryption.key') ?: getenv('encryption.key');
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

    // 2. Validate User & Password
    if (!$user || !password_verify($password, $user['password'])) {
        return redirect()->back()->withInput()->with('error', 'Invalid credentials');
    }

    // 3. Generate and Store OTP
    $otp = random_int(100000, 999999);
    $otpData = [
        'otp'            => $otp,
        'otp_expiration' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
    ];

    if (!$userModel->insert_otp($user['id'], $otpData)) {
        return redirect()->back()->with('error', 'System error: Could not generate OTP.');
    }

    // 4. Send OTP to Email
    $emailTo   = $userModel->decryptValue($user['email_enc'] ?? '');
    $firstName = $userModel->decryptValue($user['first_name_enc'] ?? '') ?: ($user['username'] ?? 'User');
    $emailService = \Config\Services::email();
    $emailService->setFrom('mesiaswael@gmail.com', 'IWAS-LIGTAS');
    $emailService->setTo($emailTo);
    $emailService->setSubject('Your IWAS-LIGTAS Login Code');
    $emailService->setMailType('html');
    $otpDigits = implode('</td><td style="width:40px;height:48px;background:#f0f4ff;border:2px solid #c7d2fe;border-radius:8px;text-align:center;font-size:26px;font-weight:700;color:#1635d1;font-family:monospace;">',
        str_split((string) $otp));
    $emailBody = '
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:30px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(22,53,209,0.10);">

        <!-- Header logo strip -->
        <tr>
          <td align="center" style="padding:28px 0 18px;">
            <img src="' . base_url('assets/images/ligtas.png') . '" alt="LIGTAS" width="64" height="64"
                 style="border-radius:50%;border:3px solid #1635d1;display:block;" />
          </td>
        </tr>

        <!-- Blue banner -->
        <tr>
          <td style="background:linear-gradient(135deg,#04f2ff 0%,#1635d1 100%);padding:32px 0;text-align:center;">
            <div style="display:inline-block;width:68px;height:68px;border-radius:50%;background:#fff;line-height:68px;text-align:center;">
              <span style="font-size:34px;">&#128274;</span>
            </div>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:36px 48px 12px;">
            <h1 style="margin:0 0 20px;font-size:26px;color:#1635d1;">Login Verification</h1>
            <p style="margin:0 0 8px;font-size:15px;color:#374151;">Hi ' . esc($firstName) . ',</p>
            <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.6;">
              Use the one-time code below to complete your sign-in to <strong>IWAS-LIGTAS</strong>.
              This code expires in <strong>5 minutes</strong>. Do not share it with anyone.
            </p>
          </td>
        </tr>

        <!-- OTP digit boxes -->
        <tr>
          <td align="center" style="padding:0 48px 32px;">
            <table cellpadding="0" cellspacing="6" style="margin:0 auto;">
              <tr>
                <td style="width:40px;height:48px;background:#f0f4ff;border:2px solid #c7d2fe;border-radius:8px;text-align:center;font-size:26px;font-weight:700;color:#1635d1;font-family:monospace;">' . $otpDigits . '</td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Divider -->
        <tr><td style="padding:0 48px;"><hr style="border:none;border-top:1px solid #e5e7eb;"></td></tr>

        <!-- Footer -->
        <tr>
          <td style="padding:20px 48px 32px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">If you did not attempt to log in, please ignore this email. Your account remains secure.</p>
            <p style="margin:8px 0 0;font-size:12px;color:#9ca3af;">&copy; ' . date('Y') . ' IWAS-LIGTAS &mdash; Local Incident Gathering and Tracking for Aquatic Safety</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
    $emailService->setMessage($emailBody);
    if (! $emailService->send()) {
        log_message('error', '[Auth::authenticate] OTP email failed: ' . $emailService->printDebugger(['headers']));
        return redirect()->back()->with('error', 'Could not send OTP email. Please try again.');
    }

    // 5. Setup Temporary Session (Not fully logged in yet)
    session()->set([
        'temp_user_id' => $user['id'],
        'otp_pending'  => true
    ]);

    return redirect()->to('/verify-otp')->with('message', 'A 6-digit code has been sent to your email.');
}

    public function verify_otp_form()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        if (! session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }
        return view('auth/otp_verify', [
            'hideNavbar'  => true,
            'hideSidebar' => true,
            'hideFooter'  => true,
        ]);
    }

    public function verify_otp()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        $userId = session()->get('temp_user_id');
        if (! $userId || ! session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        $otp = trim((string) $this->request->getPost('otp'));
        if (empty($otp)) {
            return redirect()->back()->with('error', 'Please enter the verification code.');
        }

        $userModel = new \App\Models\UserModel();
        if (! $userModel->verify_otp((int) $userId, $otp)) {
            return redirect()->back()->with('error', 'Invalid or expired verification code. Please try again.');
        }

        // OTP valid — build full session
        $user = $userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->find((int) $userId);

        if (! $user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $user = $userModel->decryptUserRow($user, true);

        session()->remove(['temp_user_id', 'otp_pending']);
        session()->set([
            'logged_in'    => true,
            'user_id'      => $user['id'],
            'username'     => $user['username'] ?? '',
            'first_name'   => $user['first_name'] ?? '',
            'last_name'    => $user['last_name'] ?? '',
            'email'        => $user['email'] ?? '',
            'province'     => $user['province'] ?? '',
            'municipality' => $user['municipality'] ?? '',
            'contact_number' => $user['contact_number'] ?? '',
            'role_id'      => $user['role_id'] ?? null,
            'role_name'    => $user['role_name'] ?? 'No Role',
            'is_admin'     => (bool) ($user['is_admin'] ?? false),
        ]);

        return redirect()->to('/dashboard');
    }

    public function resend_otp()
    {
        $userId = session()->get('temp_user_id');
        if (! $userId || ! session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find((int) $userId);
        if (! $user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $otp = random_int(100000, 999999);
        $otpData = [
            'otp'            => $otp,
            'otp_expiration' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ];

        if (! $userModel->insert_otp((int) $userId, $otpData)) {
            return redirect()->back()->with('error', 'Could not generate a new code. Please try again.');
        }

        $emailTo   = $userModel->decryptValue($user['email_enc'] ?? '');
        $firstName = $userModel->decryptValue($user['first_name_enc'] ?? '') ?: ($user['username'] ?? 'User');
        $emailService = \Config\Services::email();
        $emailService->setFrom('mesiaswael@gmail.com', 'IWAS-LIGTAS');
        $emailService->setTo($emailTo);
        $emailService->setSubject('Your IWAS-LIGTAS Login Code');
        $emailService->setMailType('html');
        $otpDigits = implode('</td><td style="width:40px;height:48px;background:#f0f4ff;border:2px solid #c7d2fe;border-radius:8px;text-align:center;font-size:26px;font-weight:700;color:#1635d1;font-family:monospace;">',
            str_split((string) $otp));
        $emailBody = '
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:30px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(22,53,209,0.10);">
        <tr>
          <td align="center" style="padding:28px 0 18px;">
            <img src="' . base_url('assets/images/ligtas.png') . '" alt="LIGTAS" width="64" height="64"
                 style="border-radius:50%;border:3px solid #1635d1;display:block;" />
          </td>
        </tr>
        <tr>
          <td style="background:linear-gradient(135deg,#04f2ff 0%,#1635d1 100%);padding:32px 0;text-align:center;">
            <div style="display:inline-block;width:68px;height:68px;border-radius:50%;background:#fff;line-height:68px;text-align:center;">
              <span style="font-size:34px;">&#128274;</span>
            </div>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 48px 12px;">
            <h1 style="margin:0 0 20px;font-size:26px;color:#1635d1;">Login Verification</h1>
            <p style="margin:0 0 8px;font-size:15px;color:#374151;">Hi ' . esc($firstName) . ',</p>
            <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.6;">
              Use the one-time code below to complete your sign-in to <strong>IWAS-LIGTAS</strong>.
              This code expires in <strong>5 minutes</strong>. Do not share it with anyone.
            </p>
          </td>
        </tr>
        <tr>
          <td align="center" style="padding:0 48px 32px;">
            <table cellpadding="0" cellspacing="6" style="margin:0 auto;">
              <tr>
                <td style="width:40px;height:48px;background:#f0f4ff;border:2px solid #c7d2fe;border-radius:8px;text-align:center;font-size:26px;font-weight:700;color:#1635d1;font-family:monospace;">' . $otpDigits . '</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr><td style="padding:0 48px;"><hr style="border:none;border-top:1px solid #e5e7eb;"></td></tr>
        <tr>
          <td style="padding:20px 48px 32px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">If you did not attempt to log in, please ignore this email. Your account remains secure.</p>
            <p style="margin:8px 0 0;font-size:12px;color:#9ca3af;">&copy; ' . date('Y') . ' IWAS-LIGTAS &mdash; Local Incident Gathering and Tracking for Aquatic Safety</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
        $emailService->setMessage($emailBody);
        if (! $emailService->send()) {
            log_message('error', '[Auth::resend_otp] email failed: ' . $emailService->printDebugger(['headers']));
            return redirect()->back()->with('error', 'Could not send the code. Please try again.');
        }

        return redirect()->to('/verify-otp')->with('success', 'A new code has been sent to your email.');
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
        
        $agree = $this->request->getPost('agree');

        // If agreement checkbox isn't sent, we need to log it too
        log_message('debug', '[Auth::store_register] agree: ' . json_encode($agree));
        if (!$agree) {
            return redirect()->back()->with('error', 'You must agree to the Data Privacy Act terms in order to register')->withInput();
        }

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

        // Province-based restriction: non-FOCAL, non-Admin users only see
        // incidents that belong to their own province.
        $provinceFilter = $this->getProvinceFilter();
        if ($provinceFilter !== null) {
            $query->where('ir.province', $provinceFilter);
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
