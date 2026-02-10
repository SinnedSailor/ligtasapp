<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .admin-setup {
        min-height: calc(100vh - 40px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 15px;
    }

    .admin-card {
        width: 100%;
        max-width: 520px;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        overflow: hidden;
    }

    .admin-card .card-header {
        background-color: #09637E;
        color: #fff;
        border: none;
        padding: 2rem;
    }

    .admin-card .card-header h3 {
        margin: 0;
        font-weight: 600;
    }

    .admin-card .card-body {
        padding: 2rem;
    }

    .admin-card .btn-primary {
        background-color: #09637E;
        border: none;
    }

    .admin-card .btn-primary:hover {
        background-color: #075267;
    }

    .admin-card .form-label {
        font-weight: 500;
        color: #333;
    }

    .admin-card .form-control:focus {
        border-color: #09637E;
        box-shadow: 0 0 0 0.2rem rgba(9, 99, 126, 0.25);
    }

    .password-requirements {
        font-size: 0.875rem;
        line-height: 1.6;
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }

    .password-requirements strong {
        display: block;
        margin-bottom: 0.5rem;
    }

    .requirement {
        display: flex;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .requirement-icon {
        margin-right: 0.5rem;
        display: inline-block;
        width: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-setup">
    <div class="card admin-card">
        <div class="card-header">
            <h3>Create Administrator Account</h3>
            <p class="mb-0 text-white-50">Set up the first admin user for your system</p>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/storeFirstAdmin') ?>" id="adminForm">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name *</label>
                    <input type="text" class="form-control" id="firstName" name="first_name" required value="<?= old('first_name') ?>" placeholder="Enter first name">
                </div>

                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" id="lastName" name="last_name" required value="<?= old('last_name') ?>" placeholder="Enter last name">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" required value="<?= old('username') ?>" placeholder="Enter username">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?= old('email') ?>" placeholder="Enter email address">
                </div>

                <div class="mb-3">
                    <label for="province" class="form-label">Province</label>
                    <select class="form-control" id="province" name="province" required>
                        <option value="">Select province</option>
                        <?php foreach (($provinces ?? []) as $province): ?>
                            <option value="<?= esc($province) ?>" <?= (old('province') === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="municipality" class="form-label">Municipality</label>
                    <select class="form-control" id="municipality" name="municipality" required>
                        <option value="">Select municipality</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter a strong password">
                </div>

                <div class="mb-3">
                    <label for="passwordConfirm" class="form-label">Confirm Password *</label>
                    <input type="password" class="form-control" id="passwordConfirm" name="password_confirm" required placeholder="Confirm your password">
                </div>

                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <div class="requirement">
                        <span class="requirement-icon">✓</span>
                        <span>At least 8 characters</span>
                    </div>
                    <div class="requirement">
                        <span class="requirement-icon">✓</span>
                        <span>One uppercase letter (A-Z)</span>
                    </div>
                    <div class="requirement">
                        <span class="requirement-icon">✓</span>
                        <span>One lowercase letter (a-z)</span>
                    </div>
                    <div class="requirement">
                        <span class="requirement-icon">✓</span>
                        <span>One number (0-9)</span>
                    </div>
                    <div class="requirement">
                        <span class="requirement-icon">✓</span>
                        <span>One special character (!@#$%^&* etc.)</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4">Create Admin Account</button>

                <div class="text-center mt-3">
                    <p class="text-muted">
                        Already have an account? <a href="<?= base_url('login') ?>" class="text-primary text-decoration-none">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const municipalities = <?= json_encode($municipalities ?? []) ?>;

    function updateMunicipalities() {
        const provinceInput = document.getElementById('province');
        const municipalityInput = document.getElementById('municipality');
        const province = provinceInput.value;
        const previousValue = municipalityInput.value;

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
            municipalityInput.value = '';
        }
    }

    document.getElementById('province').addEventListener('change', updateMunicipalities);
    document.getElementById('province').addEventListener('input', updateMunicipalities);
    updateMunicipalities();
</script>
<?= $this->endSection() ?>
