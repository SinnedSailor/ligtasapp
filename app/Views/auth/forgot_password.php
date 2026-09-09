<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/main_tailwind') ?>
<?= $this->section('pageStyles') ?>
<style>
    .auth-bg {
        background: 
            linear-gradient(135deg, rgba(4, 242, 255, 0.6) 0%, rgba(22, 53, 209, 0.6) 60%),
            url('<?= base_url('assets/images/water.png') ?>') center/cover no-repeat;
        background-size: cover;
        background-position: center;
    }

    .glass-card {
        border-radius: 3rem;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-8 px-4">
    <div class="glass-card shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 md:p-8 flex items-center justify-center">
            <div class="w-full">
                <div class="flex flex-col items-center text-center border-b border-white/20 pb-6 mb-6">
                    <div class="mb-4 w-16 h-16 rounded-full overflow-hidden">
                        <img src="<?= base_url('assets/images/ligtas.png') ?>" alt="LIGTAS logo" class="w-full h-full object-cover" />
                    </div>
                    <h1 class="text-3xl font-extrabold text-white">LIGTAS</h1>
                    <p class="text-sm font-semibold text-white">Local Incident Gathering and Tracking for Aquatic Safety</p>
                </div>

                <h4 class="text-xl font-bold text-white mb-2">Reset Password</h4>
                <p class="text-sm text-white/80 mb-6">Enter your registered email address and we will send you a secure link to reset your password.</p>

                <?php if (ENVIRONMENT === 'development' && session()->getFlashdata('dev_reset_preview')): ?>
                    <div class="mb-4 rounded-xl bg-amber-500/30 border border-amber-300/60 p-3.5 text-amber-100 text-xs text-center backdrop-blur">
                        <span class="font-semibold block mb-1 uppercase tracking-wide">🛠️ Development Mode Preview</span>
                        <a href="<?= esc(session()->getFlashdata('dev_reset_preview')) ?>" class="text-white underline break-all font-mono font-medium">Click here to open password reset link</a>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-green-100 border border-green-200 text-green-800 px-4 py-3 text-sm">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-red-100 border border-red-200 text-red-800 px-4 py-3 text-sm flex items-center">
                        <span class="mr-2">&#x26A0;</span><?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/forgot-password') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label for="email" class="sr-only">Registered Email Address</label>
                        <input id="email" name="email" type="email" required placeholder="Enter your registered email"
                            value="<?= esc(old('email') ?? '') ?>" autofocus autocomplete="email"
                            class="w-full rounded-lg bg-white/30 border border-transparent px-4 py-4 text-base text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent" />
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-lg shadow transition transform hover:-translate-y-0.5 uppercase text-base">
                            Send Reset Link
                        </button>
                    </div>

                    <div class="text-center text-sm text-white/80 mt-4">
                        Remember your password? <a href="<?= base_url('/login') ?>" class="text-white font-medium hover:underline">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
