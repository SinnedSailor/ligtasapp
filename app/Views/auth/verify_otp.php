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
        border-radius: 2.5rem;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-10 px-4">
    <div class="glass-card shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 border border-white/30 text-white shadow-lg mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-wide">Two-Factor Authentication</h1>
                <p class="text-xs font-medium text-white/80 mt-1 uppercase tracking-wider">Admin Security Verification</p>
            </div>

            <p class="text-sm text-center text-white/90 mb-6 leading-relaxed">
                A 6-digit security code has been sent to:<br>
                <span class="font-bold text-white bg-white/20 px-3 py-0.5 rounded-full inline-block mt-1"><?= esc($maskedEmail) ?></span>
            </p>

            <?php if (ENVIRONMENT === 'development' && session()->getFlashdata('dev_otp_preview')): ?>
                <div class="mb-5 p-3.5 bg-amber-500/30 border border-amber-300/60 rounded-xl text-amber-100 text-xs text-center backdrop-blur">
                    <span class="font-semibold block mb-0.5 uppercase tracking-wide">🛠️ Development Mode Preview</span>
                    Current OTP code: <span class="font-mono text-base font-bold text-white bg-black/40 px-2 py-0.5 rounded ml-1"><?= esc(session()->getFlashdata('dev_otp_preview')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 rounded-xl bg-green-500/20 border border-green-300 text-green-100 px-4 py-3 text-sm">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-xl bg-red-500/20 border border-red-300 text-red-100 px-4 py-3 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/admin/verify-otp') ?>" method="POST" class="space-y-5" id="otpForm">
                <?= csrf_field() ?>
                <div>
                    <label for="otp" class="sr-only">6-Digit Verification Code</label>
                    <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required
                        placeholder="••••••" autofocus autocomplete="one-time-code"
                        class="w-full text-center tracking-[0.6em] text-2xl font-mono font-bold rounded-xl bg-white/25 border border-white/40 px-4 py-3.5 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white/35 transition" />
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 uppercase text-sm tracking-wider">
                    Verify & Proceed
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-white/20 flex flex-col items-center justify-between text-xs space-y-3">
                <form action="<?= base_url('/admin/resend-otp') ?>" method="POST" id="resendForm">
                    <?= csrf_field() ?>
                    <button type="submit" id="resendBtn" class="text-white/90 hover:text-white font-medium underline transition disabled:opacity-50 disabled:no-underline">
                        Didn't receive the code? Resend Code
                    </button>
                </form>

                <a href="<?= base_url('/logout') ?>" class="text-white/70 hover:text-white transition">
                    &larr; Return to Sign In
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            // Auto strip non-digits
            otpInput.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    document.getElementById('otpForm').submit();
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
