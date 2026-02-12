<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/staradmin') ?>
<?= $this->section('pageStyles') ?>
<style>
    .auth-page {
        min-height: calc(100vh - 40px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 15px;
        background: url('<?= base_url('assets/staradmin/images/water.jpg') ?>') no-repeat center center;
        background-size: cover;
    }
    .split-container {
        display: flex;
        width: 900px;
        max-width: 100vw;
        min-height: 600px;
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 10px 25px rgba(9, 99, 126, 0.15);
        overflow: hidden;
        position: relative;
    }
    .split-left {
        flex: 1;
        background: #F6F5F5;
        display: flex;
        align-items: center;
        justify-content: center;
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
        padding: 40px;
    }
    .split-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        border-top-right-radius: 30px;
        border-bottom-right-radius: 30px;
        background: #fff;
    }
    .login-form-wrapper {
        width: 100%;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .iwas-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #002C76;
    }
    .iwas-header h1 {
        color: #002C76;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    .iwas-header p {
        color: #002C76;
        font-size: 13px;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .form-group input {
        width: 100%;
        max-width: 100%;
        border: 1.5px solid #e8e8e8;
        font-size: 14px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    .form-group input:focus {
        border-color: #09637E;
        box-shadow: 0 0 0 3px rgba(9, 99, 111, 0.1);
        background-color: #f8fafb;
    }
    .form-group input::placeholder {
        color: #999;
    }
    .auth-form-btn {
        background: linear-gradient(135deg, #002C76 0%, #001F4D 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(9, 99, 126, 0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        display: block;
        margin: 0 auto;
    }
    .auth-form-btn:hover {
        background: linear-gradient(135deg, #0246bb  0%, #0B4F63 100%);
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
        color: #002C76;
        border-bottom-color: #000C76;
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
        font-size: 11px;
        font-weight: 400;
    }
    .split-left img {
        width: 100%;
        max-width: 500px;
        border-radius: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page">
    <div class="split-container">
        <div class="split-left">
            <img src="<?= base_url('assets/staradmin/images/lifeguards_blue.png') ?>" alt="login-illustration" />
        </div>
        <div class="split-right">
            <div class="login-form-wrapper">
                <div class="iwas-header">
                    <h1><b>LIGTAS</b></h1>

                    <p>Local Incident Gathering and Tracking for Aquatic Safety</p>
                </div>
                <div class="mb-4">
                    <h4 class="welcome-text">Welcome!</h4>
                </div>
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="ti-alert mr-2"></i><?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <form class="pt-2" action="<?= base_url('/authenticate') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg" name="email" id="email" placeholder="Email or Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Sign In</button>
                    </div>
                    <div class="text-center mt-4">
                        Don't have an account? <a href="<?= base_url('/register') ?>">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
