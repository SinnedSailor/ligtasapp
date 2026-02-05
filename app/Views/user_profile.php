<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #1C4D8D;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 60px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-title {
            font-size: 24px;
            font-weight: 600;
        }

        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-email {
            font-size: 14px;
            opacity: 0.9;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 60px);
            overflow: hidden;
            margin-top: 60px;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 250px;
            background: #1C4D8D;
            padding: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            position: fixed;
            height: calc(100vh - 60px);
            left: 0;
            display: flex;
            flex-direction: column;
        }

        /* Profile Section */
        .profile-section {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            position: relative;
        }

        .profile-avatar:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
        }

        .profile-avatar:hover::after {
            content: 'Edit Profile';
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }

        .profile-edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #ffd700;
            color: #1C4D8D;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid white;
        }

        .profile-name {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 14px;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #ffd700;
        }

        .nav-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #ffd700;
        }

        .nav-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            margin-left: 250px;
            height: calc(100vh - 60px);
        }

        @media (max-width: 768px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .nav-menu {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .nav-item {
                padding: 12px 15px;
                font-size: 12px;
                gap: 8px;
            }

            .nav-item span:last-child {
                display: none;
            }
        }

        .container {
            max-width: 100%;
            margin: 20px auto;
            padding: 0 40px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .content-card h1 {
            color: #1C4D8D;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .profile-form {
            background: white;
            border-radius: 10px;
            padding: 25px 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin: 0 15px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .profile-header h1 {
            color: #1C4D8D;
            font-size: 24px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .profile-picture-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-picture-upload {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
        }

        .profile-picture-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #1C4D8D;
            border: 4px solid #1C4D8D;
            overflow: hidden;
            position: relative;
        }

        .profile-picture-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(28, 77, 141, 0.9);
            color: white;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-overlay:hover {
            background: rgba(28, 77, 141, 1);
        }

        .profile-picture-input {
            display: none;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #1C4D8D;
            font-size: 18px;
            margin-bottom: 20px;
            text-align: left;
            border-bottom: 2px solid #1C4D8D;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 13px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1C4D8D;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #1C4D8D;
            color: white;
        }

        .btn-primary:hover {
            background: #2563A8;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .forgot-password-link {
            display: inline-block;
            margin-top: 10px;
            color: #1C4D8D;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .forgot-password-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .content-card p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .coming-soon {
            font-size: 48px;
            margin: 30px 0;
            color: #1C4D8D;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 14px;
            margin-top: 40px;
        }
        /* Confirmation Modal Styles */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .confirmation-modal.show {
            display: flex;
        }

        .confirmation-dialog {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            min-width: 350px;
        }

        .confirmation-dialog h2 {
            color: #1C4D8D;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .confirmation-dialog p {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .confirmation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-yes, .btn-no {
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            min-width: 120px;
        }

        .btn-yes {
            background: #1C4D8D;
            color: white;
        }

        .btn-yes:hover {
            background: #2563A8;
        }

        .btn-no {
            background: #f5222d;
            color: white;
        }

        .btn-no:hover {
            background: #d9001b;
        }    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Integrated Water Safety Program</div>
        <div class="navbar-right">
            <a href="#" class="logout-btn" onclick="showLogoutConfirmation(event)">Logout</a>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="confirmation-modal" id="logoutModal">
        <div class="confirmation-dialog">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            <div class="confirmation-buttons">
                <button class="btn-yes" onclick="confirmLogout()">Yes</button>
                <button class="btn-no" onclick="cancelLogout()">No</button>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <!-- Profile Section -->
            <div class="profile-section">
                <a href="<?= base_url('/user-profile') ?>" class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                    <span class="profile-edit-icon"><i class="bi bi-pencil-fill"></i></span>
                </a>
                <div class="profile-name"><?= strtoupper(session()->get('username') ?? 'User') ?></div>
                <div class="profile-email"><?= session()->get('role_name') ?? 'No Role' ?></div>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="<?= base_url('/dashboard') ?>" class="nav-item">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('/ordinance') ?>" class="nav-item">
                    <i class="bi bi-file-earmark-arrow-up nav-icon"></i>
                    <span>Ordinance</span>
                </a>
                <a href="<?= base_url('/pops') ?>" class="nav-item">
                    <i class="bi bi-shield-check nav-icon"></i>
                    <span>POPS Plan</span>
                </a>
                <a href="<?= base_url('/incident-report') ?>" class="nav-item">
                    <i class="bi bi-exclamation-triangle nav-icon"></i>
                    <span>Incident Report</span>
                </a>
                <?php if (session()->get('is_admin')): ?>
                <a href="<?= base_url('/admin-panel') ?>" class="nav-item">
                    <i class="bi bi-shield-lock nav-icon"></i>
                    <span>Admin Panel</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="content-wrapper">

    <div class="container">
        <div class="profile-form">
            <div class="profile-header">
                <h1><i class="bi bi-person-circle"></i> My Profile</h1>
                <p style="color: #666;">Manage your account information and settings</p>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <span>Keep your profile information up to date for better communication.</span>
            </div>

            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <div class="profile-picture-upload">
                    <div class="profile-picture-preview" id="profilePreview">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="upload-overlay" onclick="document.getElementById('profilePicture').click()">
                        <i class="bi bi-camera"></i> Change Photo
                    </div>
                    <input type="file" id="profilePicture" class="profile-picture-input" accept="image/*" onchange="previewImage(event)">
                </div>
            </div>

            <form id="profileForm" onsubmit="saveProfile(event)">
                <!-- Personal Information -->
                <div class="form-section">
                    <h3><i class="bi bi-person-badge"></i> Personal Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" value="<?= session()->get('first_name') ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" value="<?= session()->get('last_name') ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" value="<?= session()->get('username') ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number *</label>
                            <input type="tel" id="contactNumber" placeholder="e.g., 09123456789" pattern="[0-9]{11}" title="Please enter 11-digit phone number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" value="<?= session()->get('email') ?? '' ?>" disabled>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="form-section">
                    <h3><i class="bi bi-geo-alt"></i> Location Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="province">Province *</label>
                            <select id="province" required>
                                <option value="">Select Province</option>
                                <option value="Ilocos Norte" <?= session()->get('province') == 'Ilocos Norte' ? 'selected' : '' ?>>Ilocos Norte</option>
                                <option value="Ilocos Sur" <?= session()->get('province') == 'Ilocos Sur' ? 'selected' : '' ?>>Ilocos Sur</option>
                                <option value="La Union" <?= session()->get('province') == 'La Union' ? 'selected' : '' ?>>La Union</option>
                                <option value="Pangasinan" <?= session()->get('province') == 'Pangasinan' ? 'selected' : '' ?>>Pangasinan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="municipality">Municipality *</label>
                            <select id="municipality" required>
                                <option value="<?= session()->get('municipality') ?? '' ?>"><?= session()->get('municipality') ?? 'Select Municipality' ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="form-section">
                    <h3><i class="bi bi-key"></i> Password Settings</h3>
                    <div class="form-group">
                        <p style="color: #666; margin-bottom: 15px;">Need to change your password?</p>
                        <a href="#" class="forgot-password-link" onclick="forgotPassword(event)" style="font-size: 16px;">
                            <i class="bi bi-shield-lock"></i> Reset Password via Email
                        </a>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('/dashboard') ?>'">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture">`;
                }
                reader.readAsDataURL(file);
            }
        }

        function saveProfile(event) {
            event.preventDefault();
            
            // Validate contact number
            const contactNumber = document.getElementById('contactNumber').value;
            if (contactNumber && !/^[0-9]{11}$/.test(contactNumber)) {
                alert('Please enter a valid 11-digit contact number.');
                return;
            }

            // In production, this would send data to the server
            alert('Profile updated successfully!');
            // window.location.href = '<?= base_url('/dashboard') ?>';
        }

        function forgotPassword(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            if (confirm(`Send password reset link to ${email}?`)) {
                alert('Password reset link has been sent to your email. Please check your inbox.');
                // In production, this would call the password reset endpoint
            }
        }

        // Province-Municipality mapping (same as register page)
        const municipalities = {
            'Ilocos Norte': ['Laoag City', 'Batac City', 'Pagudpud', 'Bangui', 'Pasuquin', 'Burgos', 'Bacarra', 'Vintar', 'Dumalneg', 'Solsona', 'Dingras', 'Nueva Era', 'Marcos', 'Banna', 'Sarrat', 'Carasi', 'Piddig', 'Pinili', 'San Nicolas', 'Badoc', 'Currimao', 'Paoay'],
            'Ilocos Sur': ['Vigan City', 'Candon City', 'Santa Cruz', 'Santa Maria', 'Narvacan', 'Santiago', 'Bantay', 'Caoayan', 'Santa Catalina', 'Magsingal', 'San Vicente', 'San Ildefonso', 'San Juan', 'Cabugao', 'Sinait', 'San Esteban', 'Burgos', 'Santa Lucia', 'Lidlidda', 'Tagudin', 'Suyo', 'Alilem', 'Sugpon', 'Sudipen', 'Banayoyo', 'Galimuyod', 'Gregorio del Pilar', 'Sigay', 'Salcedo', 'Santa', 'Quirino', 'Cervantes'],
            'La Union': ['San Fernando City', 'Bauang', 'Naguilian', 'San Juan', 'Bacnotan', 'Balaoan', 'Luna', 'Bangar', 'Santol', 'San Gabriel', 'Sudipen', 'Caba', 'Aringay', 'Tubao', 'Pugo', 'Rosario', 'Santo Tomas', 'Agoo', 'Burgos'],
            'Pangasinan': ['Dagupan City', 'San Carlos City', 'Urdaneta City', 'Alaminos City', 'Lingayen', 'Mangaldan', 'Manaoag', 'Pozorrubio', 'Sison', 'Binalonan', 'Laoac', 'San Fabian', 'San Jacinto', 'Rosales', 'Umingan', 'Balungao', 'Santa Maria', 'Alcala', 'Bautista', 'Bayambang', 'Bugallon', 'Infanta', 'Labrador', 'Mabini', 'Malasiqui', 'Mapandan', 'Natividad', 'San Manuel', 'San Nicolas', 'San Quintin', 'Santa Barbara', 'Tayug', 'Uyong', 'Villasis', 'Asingan', 'Binmaley', 'Bolinao', 'Burgos', 'Dasol', 'Sual']
        };

        // Update municipality dropdown when province changes
        document.getElementById('province').addEventListener('change', function() {
            const province = this.value;
            const municipalitySelect = document.getElementById('municipality');
            
            municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
            
            if (province && municipalities[province]) {
                municipalities[province].forEach(mun => {
                    const option = document.createElement('option');
                    option.value = mun;
                    option.textContent = mun;
                    municipalitySelect.appendChild(option);
                });
            }
        });
    </script>

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById('logoutModal').classList.add('show');
        }

        function confirmLogout() {
            window.location.href = '<?= base_url('/logout') ?>';
        }

        function cancelLogout() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        // Close modal when clicking outside of it
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                cancelLogout();
            }
        });
    </script>

    <div class="footer">
        <p>&copy; 2026 Integrated Water Safety Program. All rights reserved.</p>
    </div>
        </div>
    </div>
</body>
</html>
