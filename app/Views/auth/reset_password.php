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

                <h4 class="text-xl font-bold text-white mb-2">Create New Password</h4>
                <p class="text-sm text-white/80 mb-6">Enter your new password below. It must contain at least 8 characters including uppercase, lowercase, number, and symbol.</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-red-100 border border-red-200 text-red-800 px-4 py-3 text-sm flex items-center">
                        <span class="mr-2">&#x26A0;</span><?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/reset-password') ?>" method="POST" class="space-y-4" id="resetPasswordForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>" />

                    <div>
                        <label for="password" class="sr-only">New Password</label>
                        <input id="password" name="password" type="password" required placeholder="New Password"
                            autocomplete="new-password" autofocus
                            class="w-full rounded-lg bg-white/30 border border-transparent px-4 py-4 text-base text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent" />
                    </div>

                    <div>
                        <label for="password_confirm" class="sr-only">Confirm New Password</label>
                        <input id="password_confirm" name="password_confirm" type="password" required placeholder="Confirm New Password"
                            autocomplete="new-password"
                            class="w-full rounded-lg bg-white/30 border border-transparent px-4 py-4 text-base text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent" />
                    </div>

                    <!-- Password requirements checklist -->
                    <div class="p-3 bg-white/10 rounded-lg text-xs text-white/90 space-y-1">
                        <p class="font-semibold text-white">Password requirements:</p>
                        <ul class="space-y-0.5 ml-1">
                            <li id="rule-len" class="flex items-center text-white/70"><span>• At least 8 characters</span></li>
                            <li id="rule-upper" class="flex items-center text-white/70"><span>• At least one uppercase letter (A-Z)</span></li>
                            <li id="rule-lower" class="flex items-center text-white/70"><span>• At least one lowercase letter (a-z)</span></li>
                            <li id="rule-num" class="flex items-center text-white/70"><span>• At least one number (0-9)</span></li>
                            <li id="rule-spec" class="flex items-center text-white/70"><span>• At least one special character (!@#$%^&*...)</span></li>
                        </ul>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-lg shadow transition transform hover:-translate-y-0.5 uppercase text-base">
                            Update Password
                        </button>
                    </div>

                    <div class="text-center text-sm text-white/80 mt-4">
                        <a href="<?= base_url('/login') ?>" class="text-white font-medium hover:underline">&larr; Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwd = document.getElementById('password');
    const rLen = document.getElementById('rule-len');
    const rUpper = document.getElementById('rule-upper');
    const rLower = document.getElementById('rule-lower');
    const rNum = document.getElementById('rule-num');
    const rSpec = document.getElementById('rule-spec');

    function checkRule(el, valid) {
        if (valid) {
            el.className = 'flex items-center text-green-300 font-medium';
        } else {
            el.className = 'flex items-center text-white/70';
        }
    }

    if (pwd) {
        pwd.addEventListener('input', function() {
            const val = this.value;
            checkRule(rLen, val.length >= 8);
            checkRule(rUpper, /[A-Z]/.test(val));
            checkRule(rLower, /[a-z]/.test(val));
            checkRule(rNum, /[0-9]/.test(val));
            checkRule(rSpec, /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(val));
        });
    }
});
</script>
<?= $this->endSection() ?>
