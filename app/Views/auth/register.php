<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f7fa;
        padding: 0;
    }

    .perfect-fit-register-container {
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 10px 25px rgba(9, 99, 126, 0.15);
        padding: 40px 32px;
        width: 100%;
        max-width: 700px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .perfect-fit-register-container form {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        width: 100%;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-group {
        width: 100%;
    }

    .form-group input, .form-group select {
        width: 100%;
        box-sizing: border-box;
    }

    .iwas-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #002C76;
    }

    .iwas-logo-circle {
        width: 70px;
        height: 70px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #002C76 0%, #0B4F63 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(9, 99, 126, 0.3);
    }

    .iwas-logo-circle i {
        color: #fff;
        font-size: 32px;
    }

    .iwas-header h3 {
        color: #002C76;
        font-weight: 700;
        margin: 0.5rem 0;
        font-size: 28px;
    }

    .iwas-header p {
        color: #666;
        font-size: 13px;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .form-group input {
        border: 1.5px solid #e8e8e8;
        font-size: 14px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: #002C76;
        box-shadow: 0 0 0 3px rgba(9, 99, 126, 0.1);
        background-color: #f8fafb;
    }

    .form-group input::placeholder {
        color: #999;
    }

    .auth-form-btn {
        background: linear-gradient(135deg, #002C76 0%, #0B4F63 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(9, 99, 126, 0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        width: 100%;
        margin: 0 auto;
    }

    .auth-form-btn:hover {
        background: linear-gradient(135deg, #0B4F63 0%, #002C76 100%);
        box-shadow: 0 6px 20px rgba(9, 99, 126, 0.3);
        color: #fff;
        transform: translateY(-2px);
    }

    .text-center.mt-4 {
        color: #666;
        font-size: 13px;
    }

    .text-center.mt-4 a {
        color: #002C76;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border-bottom: 2px solid transparent;
    }

    .text-center.mt-4 a:hover {
        color: #0B4F63;
        border-bottom-color: #0B4F63;
    }

    .alert-danger {
        background-color: rgba(201, 74, 74, 0.1);
        border: 1px solid #C94A4A;
        color: #8B2D2D;
        border-radius: 6px;
    }

    .welcome-text {
        color: #002C76;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .subtitle-text {
        color: #999;
        font-size: 13px;
        font-weight: 400;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page">
    <div class="perfect-fit-register-container">
        <div class="iwas-header">
            <div class="iwas-logo-circle">
                <i class="ti-water"></i>
            </div>
            <h3>LIGTAS</h3>
            <p>Local Incident Gathering and Tracking for Aquatic Safety</p>
        </div>
        <div class="mb-4">
            <h4 class="welcome-text">Create your account</h4>
            <p class="subtitle-text m-0">Fill in the details to register</p>
        </div>
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger mt-3">
                <i class="ti-alert mr-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <form class="pt-2" action="<?= base_url('/store-register') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="first_name" placeholder="First Name" value="<?= old('first_name') ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="last_name" placeholder="Last Name" value="<?= old('last_name') ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="username" placeholder="Username" value="<?= old('username') ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" value="<?= old('email') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control form-control-md" id="province" name="province" required>
                            <option value="">Select province</option>
                            <?php foreach (($provinces ?? []) as $province): ?>
                                <option value="<?= esc($province) ?>" <?= (old('province') === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control form-control-md" id="municipality" name="municipality" required>
                            <option value="">Select municipality</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control form-control-lg" name="password_confirm" placeholder="Confirm Password" required>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Create Account</button>
            </div>
            <div class="text-center mt-4">
                Already have an account? <a href="<?= base_url('/login') ?>">Sign in</a>
            </div>
        </form>
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
