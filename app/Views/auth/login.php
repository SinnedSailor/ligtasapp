<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/main_tailwind') ?>
<?= $this->section('pageStyles') ?>
<style>
    /* page background photo with gradient overlay */
    .auth-bg {
        background: 
            linear-gradient(135deg,rgba(4, 242, 255, 0.6) 0%, rgba(22, 53, 209, 0.6) 60%),
            url('<?= base_url('assets/images/water.png') ?>') center/cover no-repeat;
        background-size: cover;
        background-position: center;
    }

    /* glassmorphism card styling (for older browsers) */
    .glass-card {
        border-radius: 3rem;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.2);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-8 px-2">
    <div class="glass-card rounded-[3rem] shadow-2xl w-96 max-w-md overflow-hidden">
        <div class="p-6 md:p-8 flex items-center justify-center">
            <div class="w-full">
                <div class="flex flex-col items-center text-center border-b pb-6 mb-6">
                    <!-- logo -->
                    <div class="mb-4 w-16 h-16 rounded-full overflow-hidden">
                        <img src="<?= base_url('assets/images/ligtas.png') ?>" alt="LIGTAS logo" class="w-full h-full object-cover" />
                    </div>
                    <h1 class="text-3xl font-extrabold text-white">LIGTAS</h1>
                    <p class="text-sm font-semibold text-white">Local Incident Gathering and Tracking for Aquatic Safety</p>
                </div>
                <h4 class="text-lg font-semibold text-white mb-6">Welcome!</h4>

                <?php if (session()->getFlashdata('success')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-green-100 border border-green-200 text-green-800 px-4 py-3">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-red-100 border border-red-200 text-red-800 px-4 py-3">
                        <span class="mr-2">X</span><?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/authenticate') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label for="email" class="sr-only">Email or Username</label>
                        <input id="email" name="email" type="text" required placeholder="Email or Username"
                            value="<?= esc(old('email') ?? '') ?>"
                            autocomplete="username" autofocus
                            class="w-full rounded-lg bg-white/30 border border-transparent px-4 py-4 text-base text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent" />
                    </div>
                    <div class="relative">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" required placeholder="Password"
                            autocomplete="current-password"
                            class="w-full rounded-lg bg-white/30 border border-transparent px-4 py-4 pr-10 text-base text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent" />
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-lg shadow transition transform hover:-translate-y-0.5 uppercase text-base">Sign In</button>
                    </div>
                    <div class="text-center text-sm text-white/80 mt-3">
                        Don't have an account? <a href="<?= base_url('/register') ?>" class="text-white font-medium hover:underline">Register here</a>
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const toggle = document.getElementById('togglePassword');
                        const pwd = document.getElementById('password');
                        const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>`;
                        const eyeOffIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.591m1.456-1.457A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.546m-3.03.239a3 3 0 11-4.243-4.243" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>`;
                        if (toggle && pwd) {
                            toggle.addEventListener('click', function () {
                                const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
                                pwd.setAttribute('type', type);
                                this.innerHTML = type === 'password' ? eyeIcon : eyeOffIcon;
                            });
                        }
                    });
                </script>
            </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
