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
            linear-gradient(135deg,rgba(4, 242, 255, 0.6) 0%, rgba(22, 53, 209, 0.6) 60%),
            url('<?= base_url('assets/images/water.png') ?>') center/cover no-repeat;
        background-size: cover;
        background-position: center;
    }
    .glass-card {
        border-radius: 3rem;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    /* Individual OTP digit boxes */
    .otp-input {
        width: 3rem;
        height: 3.5rem;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        background: rgba(255,255,255,0.9);
        color: #1e1b4b;
        border: 2px solid rgba(255,255,255,0.6);
        border-radius: 0.5rem;
        caret-color: #4f46e5;
    }
    .otp-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.4);
        background: #ffffff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-8 px-2">
    <div class="glass-card shadow-2xl w-96 max-w-md overflow-hidden">
        <div class="p-6 md:p-8 flex items-center justify-center">
            <div class="w-full">
                <div class="flex flex-col items-center text-center border-b pb-6 mb-6">
                    <div class="mb-4 w-16 h-16 rounded-full overflow-hidden">
                        <img src="<?= base_url('assets/images/ligtas.png') ?>" alt="LIGTAS logo" class="w-full h-full object-cover" />
                    </div>
                    <h1 class="text-3xl font-extrabold text-white">LIGTAS</h1>
                    <p class="text-sm font-semibold text-white">Local Incident Gathering and Tracking for Aquatic Safety</p>
                </div>

                <h4 class="text-lg font-semibold text-white mb-2">Two-Factor Verification</h4>
                <p class="text-sm text-white/80 mb-6">Enter the 6-digit code sent to your email. It expires in 5 minutes.</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-red-100 border border-red-200 text-red-800 px-4 py-3">
                        <span class="mr-2">&#x26A0;</span><?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-green-100 border border-green-200 text-green-800 px-4 py-3">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/verify-otp') ?>" method="POST" id="otpForm" class="space-y-6">
                    <?= csrf_field() ?>
                    <!-- Hidden combined input that gets populated on submit -->
                    <input type="hidden" name="otp" id="otpHidden" />

                    <!-- 6 individual digit boxes -->
                    <div class="flex justify-center gap-2" id="otpBoxes">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-input"
                            autocomplete="one-time-code" />
                        <?php endfor; ?>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-lg shadow transition transform hover:-translate-y-0.5 uppercase text-base">
                        Verify Code
                    </button>
                </form>

                <div class="text-center mt-4">
                    <form action="<?= base_url('/resend-otp') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="text-sm text-white/70 hover:text-white hover:underline">
                            Didn't receive a code? Resend
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('#otpBoxes input'));
    const form  = document.getElementById('otpForm');
    const hidden = document.getElementById('otpHidden');

    boxes.forEach(function (box, idx) {
        box.addEventListener('input', function () {
            // Allow only digits
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && idx < boxes.length - 1) {
                boxes[idx + 1].focus();
            }
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                boxes[idx - 1].focus();
            }
        });

        // Handle paste on any box
        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            pasted.split('').forEach(function (ch, i) {
                if (boxes[idx + i]) {
                    boxes[idx + i].value = ch;
                }
            });
            const next = Math.min(idx + pasted.length, boxes.length - 1);
            boxes[next].focus();
        });
    });

    form.addEventListener('submit', function (e) {
        const combined = boxes.map(function (b) { return b.value; }).join('');
        if (combined.length !== 6) {
            e.preventDefault();
            alert('Please enter all 6 digits.');
            return;
        }
        hidden.value = combined;
    });
});
</script>
<?= $this->endSection() ?>
