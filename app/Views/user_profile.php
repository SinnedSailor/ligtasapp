<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .profile-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .profile-picture-section {
        display: flex;
        justify-content: center;
        margin-bottom: 48px; /* increased to prevent overlap with alert */
    }

    .profile-picture-upload {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .profile-picture-preview {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #09637E;
        overflow: hidden;
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
        background: rgba(9, 99, 126, 0.85);
        color: #fff;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        cursor: pointer;
        border-bottom-left-radius: 50%;
        border-bottom-right-radius: 50%;
    }

    .form-section {
        margin-top: 24px;
        margin-bottom: 16px;
    }

    .form-section h5 {
        color: #09637E;
        margin-bottom: 10px;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .form-row .form-group {
        flex: 1 1 220px;
        min-width: 220px;
        margin-bottom: 0;
    }

    .forgot-password-link {
        text-decoration: none;
        color: #09637E;
        font-weight: 600;
    }

    .success-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 3000;
    }

    .success-modal.show {
        display: flex;
    }

    .success-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        max-width: 420px;
        width: 100%;
        text-align: center;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.25);
    }

    .success-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #16a34a;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
    }

    .success-message {
        font-size: 18px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .success-subtext {
        color: #64748b;
        margin-bottom: 16px;
    }

    /* profile info alert - centered and spaced properly */
    .info-alert {
        max-width: 560px;
        margin: 8px auto 20px auto;
        text-align: center;
        box-shadow: none;
    }

    @media (max-width: 576px) {
        .profile-picture-section { margin-bottom: 28px; }
        .info-alert { margin: 12px; max-width: 100%; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $profileSuccess = session()->getFlashdata('profile_success');
    $profileError = session()->getFlashdata('error');
    $selectedProvince = trim((string) ($profile['province'] ?? session()->get('province') ?? ''));
    $selectedMunicipality = trim((string) ($profile['municipality'] ?? session()->get('municipality') ?? ''));
    $provinceList = $provinces ?? [];
    $provinceSelectedInList = $selectedProvince !== '' && in_array($selectedProvince, $provinceList, true);
?>
<div class="page-header">
    <h3 class="page-title">User Profile</h3>
    <p class="text-muted">Manage your account information and settings.</p>
</div>

<?php if (!empty($profileError)): ?>
    <div class="alert alert-danger">
        <?= esc($profileError) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 col-md-10 grid-margin mx-auto">
        <div class="profile-card">
            <div class="profile-picture-section">
                <div class="profile-picture-upload">
                    <div class="profile-picture-preview" id="profilePreview">
                        <i class="ti-user"></i>
                    </div>
                    <div class="upload-overlay" onclick="document.getElementById('profilePicture').click()">
                        <i class="ti-camera"></i> Change Photo
                    </div>
                    <input type="file" id="profilePicture" class="d-none" accept="image/*" onchange="previewImage(event)">
                </div>
            </div>

            <div class="alert alert-info info-alert">
                Keep your profile information up to date for better communication.
            </div>

            <form id="profileForm" action="<?= base_url('/user-profile/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-section">
                    <h5>Personal Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?= esc($profile['first_name'] ?? session()->get('first_name') ?? '') ?>" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?= esc($profile['last_name'] ?? session()->get('last_name') ?? '') ?>" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= esc($profile['username'] ?? session()->get('username') ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number *</label>
                            <input type="tel" class="form-control" id="contactNumber" name="contact_number" value="<?= esc($profile['contact_number'] ?? session()->get('contact_number') ?? '') ?>" placeholder="e.g., 09123456789" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" title="Please enter 11-digit phone number (numbers only)" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" class="form-control" id="email" value="<?= esc($profile['email'] ?? session()->get('email') ?? '') ?>" disabled>
                    </div>
                </div>

                <div class="form-section">
                    <h5>Location Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="province">Province *</label>
                            <select class="form-control" id="province" name="province" required>
                                <option value="">Select province</option>
                                <?php if (!$provinceSelectedInList && $selectedProvince !== ''): ?>
                                    <option value="<?= esc($selectedProvince) ?>" selected>
                                        <?= esc($selectedProvince) ?>
                                    </option>
                                <?php endif; ?>
                                <?php foreach ($provinceList as $province): ?>
                                    <option value="<?= esc($province) ?>" <?= ($selectedProvince === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="municipality">Municipality *</label>
                            <select class="form-control" id="municipality" name="municipality" required>
                                <option value="">Select municipality</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5>Password Settings</h5>
                    <p class="text-muted">Need to change your password?</p>
                    <a href="#" class="forgot-password-link" onclick="forgotPassword(event)">
                        <i class="ti-lock"></i> Reset Password via Email
                    </a>
                </div>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti-check"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('/dashboard') ?>'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="success-modal" id="profileSuccessModal" aria-hidden="true">
    <div class="success-card">
        <div class="success-icon">
            <i class="ti-check"></i>
        </div>
        <div class="success-message">Profile updated successfully!</div>
        <div class="success-subtext">Your changes have been saved.</div>
        <button type="button" class="btn btn-primary" id="closeSuccessModal">OK</button>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture">`;
            };
            reader.readAsDataURL(file);
        }
    }

    function forgotPassword(event) {
        event.preventDefault();
        const email = document.getElementById('email').value;
        if (confirm(`Send password reset link to ${email}?`)) {
            alert('Password reset link has been sent to your email. Please check your inbox.');
        }
    }

    const municipalities = <?= json_encode($municipalities ?? []) ?>;

    function updateMunicipalities() {
        const provinceInput = document.getElementById('province');
        const municipalityInput = document.getElementById('municipality');
        const province = provinceInput.value;
        const previousValue = municipalityInput.value || '<?= addslashes($selectedMunicipality) ?>';

        municipalityInput.innerHTML = '<option value="">Select municipality</option>';

        if (province && municipalities[province]) {
            municipalities[province].forEach((mun) => {
                const option = document.createElement('option');
                option.value = mun;
                option.textContent = mun;
                if (mun === previousValue) {
                    option.selected = true;
                }
                municipalityInput.appendChild(option);
            });
        }

        if (previousValue && (!municipalities[province] || !municipalities[province].includes(previousValue))) {
            const option = document.createElement('option');
            option.value = previousValue;
            option.textContent = previousValue;
            option.selected = true;
            municipalityInput.appendChild(option);
        }
    }

    document.getElementById('province').addEventListener('change', updateMunicipalities);
    document.getElementById('province').addEventListener('input', updateMunicipalities);
    updateMunicipalities();

    const successModal = document.getElementById('profileSuccessModal');
    const closeSuccessModal = document.getElementById('closeSuccessModal');

    if (<?= $profileSuccess ? 'true' : 'false' ?>) {
        successModal.classList.add('show');
        successModal.setAttribute('aria-hidden', 'false');
    }

    closeSuccessModal.addEventListener('click', () => {
        successModal.classList.remove('show');
        successModal.setAttribute('aria-hidden', 'true');
    });

    successModal.addEventListener('click', (event) => {
        if (event.target === successModal) {
            successModal.classList.remove('show');
            successModal.setAttribute('aria-hidden', 'true');
        }
    });
</script>
<?= $this->endSection() ?>
