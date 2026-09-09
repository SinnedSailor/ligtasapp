<?php

namespace App\Controllers;

use App\Models\IncidentReportModel;
use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Libraries\AuthMailer;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('is_admin') ? '/admin-panel' : '/dashboard');
        }

        return view('auth/login', [
            'hideNavbar'  => true,
            'hideSidebar' => true,
            'hideFooter'  => true,
        ]);
    }

    public function authenticate()
    {
        $emailOrUsername = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($emailOrUsername) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Please provide credentials');
        }

        $userModel = new UserModel();

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
            return redirect()->back()->withInput()->with('error', 'Invalid email/username or password');
        }

        // Check if account has been disabled/deactivated
        if (isset($user['is_active']) && (int) $user['is_active'] === 0) {
            return redirect()->back()->withInput()->with('error', 'Your account has been deactivated. Please contact the system administrator.');
        }

        // 3. Generate and Store OTP
        $otp = random_int(100000, 999999);
        $otpData = [
            'otp'            => (string) $otp,
            'otp_expiration' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ];

        if (!$userModel->insert_otp((int) $user['id'], $otpData)) {
            return redirect()->back()->with('error', 'System error: Could not generate OTP.');
        }

        // 4. Send OTP to Email
        $user = $userModel->decryptUserRow($user);
        $emailTo = $userModel->decryptValue($user['email_enc'] ?? '') ?: ($user['email'] ?? '');
        $firstName = $user['first_name'] ?: ($user['username'] ?? 'User');

        $this->dispatchOtpEmail($emailTo, $firstName, (string) $otp);

        // For local development convenience if SMTP delay occurs
        if (ENVIRONMENT === 'development') {
            session()->setFlashdata('dev_otp_preview', (string) $otp);
            log_message('info', "[AUTH OTP] Generated OTP for user {$user['id']} ({$emailTo}): {$otp}");
        }

        // 5. Setup Temporary Session (Not fully logged in yet)
        session()->set([
            'temp_user_id' => $user['id'],
            'otp_pending'  => true,
        ]);

        return redirect()->to('/verify-otp')->with('message', 'A 6-digit code has been sent to your email.');
    }

    public function verify_otp_form()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('is_admin') ? '/admin-panel' : '/dashboard');
        }

        $userId = session()->get('temp_user_id');
        if (!$userId || !session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
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
            return redirect()->to(session()->get('is_admin') ? '/admin-panel' : '/dashboard');
        }

        $userId = session()->get('temp_user_id');
        if (!$userId || !session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        $otp = trim((string) $this->request->getPost('otp'));
        if (empty($otp) || strlen($otp) !== 6) {
            return redirect()->back()->with('error', 'Please enter a valid 6-digit verification code.');
        }

        $userModel = new UserModel();
        if (!$userModel->verify_otp((int) $userId, $otp)) {
            return redirect()->back()->with('error', 'Invalid or expired verification code. Please try again.');
        }

        // OTP valid — build full session
        $user = $userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->find((int) $userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $user = $userModel->decryptUserRow($user, true);
        $emailPlain = $userModel->decryptValue($user['email_enc'] ?? '') ?: ($user['email'] ?? '');

        session()->remove(['temp_user_id', 'otp_pending']);
        session()->set([
            'logged_in'      => true,
            'user_id'        => $user['id'],
            'username'       => $user['username'] ?? '',
            'first_name'     => $user['first_name'] ?? '',
            'last_name'      => $user['last_name'] ?? '',
            'email'          => $emailPlain,
            'province'       => $user['province'] ?? '',
            'municipality'   => $user['municipality'] ?? '',
            'contact_number' => $user['contact_number'] ?? '',
            'role_id'        => $user['role_id'] ?? null,
            'role_name'      => $user['role_name'] ?? 'No Role',
            'is_admin'       => (bool) ($user['is_admin'] ?? false),
            'is_active'      => (int) ($user['is_active'] ?? 1),
        ]);

        session()->setFlashdata('login_success', true);

        if (!empty($user['is_admin'])) {
            return redirect()->to('/admin-panel');
        }

        return redirect()->to('/dashboard');
    }

    public function resend_otp()
    {
        $userId = session()->get('temp_user_id');
        if (!$userId || !session()->get('otp_pending')) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $userId);
        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $otp = random_int(100000, 999999);
        $otpData = [
            'otp'            => (string) $otp,
            'otp_expiration' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ];

        if (!$userModel->insert_otp((int) $userId, $otpData)) {
            return redirect()->back()->with('error', 'System error: Could not regenerate code.');
        }

        $user = $userModel->decryptUserRow($user);
        $emailTo = $userModel->decryptValue($user['email_enc'] ?? '') ?: ($user['email'] ?? '');
        $firstName = $user['first_name'] ?: ($user['username'] ?? 'User');

        $this->dispatchOtpEmail($emailTo, $firstName, (string) $otp);

        if (ENVIRONMENT === 'development') {
            session()->setFlashdata('dev_otp_preview', (string) $otp);
            log_message('info', "[AUTH RESEND OTP] Generated OTP for user {$userId} ({$emailTo}): {$otp}");
        }

        return redirect()->back()->with('success', 'A new 6-digit code has been sent to your email.');
    }

    protected function dispatchOtpEmail(string $emailTo, string $firstName, string $otp): bool
    {
        try {
            $emailService = \Config\Services::email();
            $config = config('Email');
            $fromEmail = !empty($config->SMTPUser) ? $config->SMTPUser : (!empty($config->fromEmail) ? $config->fromEmail : 'mesiaswael@gmail.com');
            $fromName  = !empty($config->fromName) ? $config->fromName : 'IWAS-LIGTAS';

            $emailService->setFrom($fromEmail, $fromName);
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
            $sent = (bool) $emailService->send(false);
            if (!$sent) {
                log_message('error', '[Auth::dispatchOtpEmail] Email failed: ' . $emailService->printDebugger(['headers']));
            }
            return $sent;
        } catch (\Throwable $e) {
            log_message('error', '[Auth::dispatchOtpEmail] Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'hideNavbar'     => true,
            'hideSidebar'    => true,
            'hideFooter'     => true,
            'provinces'      => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
        ]);
    }

    public function store_register()
    {
        $agree = $this->request->getPost('agree');
        if (!$agree) {
            return redirect()->back()->with('error', 'You must agree to the Data Privacy Act terms in order to register')->withInput();
        }

        $data = [
            'first_name'   => $this->request->getPost('first_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'province'     => $this->request->getPost('province'),
            'municipality' => $this->request->getPost('municipality'),
            'password'     => $this->request->getPost('password'),
        ];

        $password_confirm = $this->request->getPost('password_confirm');

        if ($data['password'] !== $password_confirm) {
            return redirect()->back()->with('error', 'Passwords do not match')->withInput();
        }

        $hasUppercase = preg_match('/[A-Z]/', $data['password']);
        $hasLowercase = preg_match('/[a-z]/', $data['password']);
        $hasNumber    = preg_match('/[0-9]/', $data['password']);
        $hasSpecial   = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $data['password']);

        if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial || strlen($data['password']) < 8) {
            return redirect()->back()->with('error', 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character')->withInput();
        }

        if (!$this->isValidRegion1Location((string) $data['province'], (string) $data['municipality'])) {
            return redirect()->back()->with('error', 'Please select a valid Region 1 province and municipality')->withInput();
        }

        $userModel = new UserModel();

        $valid = $userModel->validate($data);
        if (!$valid) {
            $errors = $userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Validation failed';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        if ($userModel->getUserByEmail($data['email'])) {
            return redirect()->back()->with('error', 'This email is already registered.')->withInput();
        }

        $data = $userModel->prepareForInsert($data);
        $userModel->skipValidation(true);

        $insertId = $userModel->insert($data);
        if ($insertId === false) {
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
            'role_name',
            'is_admin',
            'is_active',
            'logged_in',
            'temp_user_id',
            'otp_pending',
        ]);
        $session->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function ordinance()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

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

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName === 'FOCAL') {
            $query->where('ir.review_status', 'approved');
        }

        $provinceFilter = $this->getProvinceFilter();
        if ($provinceFilter !== null) {
            $query->where('ir.province', $provinceFilter);
        }

        $rows = $query->get()->getResultArray();

        $incidentModel = new IncidentReportModel();
        foreach ($rows as &$r) {
            $r = $incidentModel->decryptRow($r);
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

        $incidentCtrl = new IncidentReport();
        $rows = $incidentCtrl->filterRowsForRole($rows, $roleName);

        $locationModel = new IncidentReportModel();
        $locationCategories = $locationModel->getDistinctLocationCategories();
        $occasions = $locationModel->getDistinctOccasions();
        $occupations = $locationModel->getDistinctOccupations();
        $otherFactors = $locationModel->getDistinctFactors();

        return view('incident_report', [
            'initialRows'        => $rows,
            'roleName'           => strtoupper(trim((string) session()->get('role_name'))),
            'isAdmin'            => (bool) session()->get('is_admin'),
            'provinces'          => $this->getRegion1Provinces(),
            'municipalities'     => $this->getRegion1Municipalities(),
            'locationCategories' => $locationCategories,
            'occasions'          => $occasions,
            'occupations'        => $occupations,
            'otherFactors'       => $otherFactors,
        ]);
    }

    public function pops()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

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
            $profile = $userModel->decryptUserRow($profile, true);

            if (isset($profile['email']) && preg_match('/^[0-9a-f]{64}$/i', (string) $profile['email'])) {
                $profile['display_email'] = '';
            } else {
                $profile['display_email'] = $profile['email'] ?? '';
            }
        }

        if (empty($profile['display_email'])) {
            $sessionEmail = session()->get('email') ?? '';
            if ($sessionEmail && !preg_match('/^[0-9a-f]{64}$/i', (string) $sessionEmail)) {
                $profile['display_email'] = $sessionEmail;
            }
        }

        return view('user_profile', [
            'provinces'      => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
            'profile'        => $profile,
        ]);
    }

    public function update_profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = (int) session()->get('user_id');
        $data = [
            'first_name'     => trim((string) $this->request->getPost('first_name')),
            'last_name'      => trim((string) $this->request->getPost('last_name')),
            'username'       => trim((string) $this->request->getPost('username')),
            'contact_number' => trim((string) $this->request->getPost('contact_number')),
            'province'       => trim((string) $this->request->getPost('province')),
            'municipality'   => trim((string) $this->request->getPost('municipality')),
        ];

        $emailInput = trim((string) $this->request->getPost('email'));
        if ($emailInput !== '') {
            $data['email'] = $emailInput;
        }

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '') {
            return redirect()->back()->with('error', 'Please complete all required fields.')->withInput();
        }

        if ($data['contact_number'] !== '' && !preg_match('/^[0-9]{11}$/', $data['contact_number'])) {
            return redirect()->back()->with('error', 'Please enter a valid 11-digit contact number.')->withInput();
        }

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

        if (isset($data['email'])) {
            $existingEmail = $userModel->getUserByEmail($data['email']);
            if ($existingEmail && (int) $existingEmail['id'] !== $userId) {
                return redirect()->back()->with('error', 'This email is already registered.')->withInput();
            }
        }

        $data = $userModel->prepareForInsert($data);

        $userModel->skipValidation(true);
        if (!$userModel->update($userId, $data)) {
            $errors = $userModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Profile update failed.';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        $profile = $userModel->find($userId);
        $profile = $userModel->decryptUserRow($profile);
        $emailPlain = $userModel->decryptValue($profile['email_enc'] ?? ($profile['email'] ?? ''));

        session()->set([
            'first_name'     => $profile['first_name'] ?? '',
            'last_name'      => $profile['last_name'] ?? '',
            'username'       => $profile['username'] ?? '',
            'province'       => $profile['province'] ?? '',
            'municipality'   => $profile['municipality'] ?? '',
            'contact_number' => $profile['contact_number'] ?? '',
            'email'          => $emailPlain ?: session()->get('email'),
        ]);

        return redirect()->to('/user-profile')->with('profile_success', 'Profile updated successfully!');
    }

    // ==========================================
    // PASSWORD RESET VIA EMAIL
    // ==========================================

    public function forgotPassword()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('is_admin') ? '/admin-panel' : '/dashboard');
        }

        return view('auth/forgot_password', [
            'hideNavbar'  => true,
            'hideSidebar' => true,
            'hideFooter'  => true,
        ]);
    }

    public function sendResetLink()
    {
        $email = trim((string) $this->request->getPost('email'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Please provide a valid email address.');
        }

        $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
        $emailHash = hash_hmac('sha256', mb_strtolower(trim($email)), $key);

        $userModel = new UserModel();
        $user = $userModel->where('email_hash', $emailHash)->first();

        if ($user && (!isset($user['is_active']) || (int) $user['is_active'] === 1)) {
            $user = $userModel->decryptUserRow($user);
            $resetModel = new PasswordResetModel();
            $rawToken = $resetModel->createToken((int) $user['id'], $emailHash);

            $mailer = new AuthMailer();
            $mailer->sendPasswordReset($email, $user['first_name'] ?: $user['username'], $rawToken);
        }

        return redirect()->back()->with('success', 'If an account exists with that email, instructions have been sent to reset your password.');
    }

    public function showResetPassword(string $token)
    {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('is_admin') ? '/admin-panel' : '/dashboard');
        }

        $resetModel = new PasswordResetModel();
        $record = $resetModel->validateToken($token);

        if (!$record) {
            return redirect()->to('/forgot-password')->with('error', 'Password reset link is invalid or has expired. Please request a new one.');
        }

        return view('auth/reset_password', [
            'token'       => $token,
            'hideNavbar'  => true,
            'hideSidebar' => true,
            'hideFooter'  => true,
        ]);
    }

    public function processResetPassword()
    {
        $token = trim((string) $this->request->getPost('token'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        $resetModel = new PasswordResetModel();
        $record = $resetModel->validateToken($token);

        if (!$record) {
            return redirect()->to('/forgot-password')->with('error', 'Password reset link is invalid or has expired. Please request a new one.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumber    = preg_match('/[0-9]/', $password);
        $hasSpecial   = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password);

        if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial || strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.');
        }

        $userModel = new UserModel();
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $userModel->update($record['user_id'], ['password' => $newHash]);

        $resetModel->invalidateToken($token);

        return redirect()->to('/login')->with('success', 'Your password has been successfully updated! You can now log in.');
    }
}
