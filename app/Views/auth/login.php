<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/main_tailwind') ?>
<?= $this->section('pageStyles') ?>
<style>
    /* page background image */
    .auth-bg {
        background: linear-gradient(135deg,#002C76 0%, #001F5C 60%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 md:divide-x md:divide-gray-100 overflow-hidden items-stretch">
        <div class="hidden md:flex items-center justify-center bg-gray-50 p-10">
            <div>
                <!-- Illustration -->
                <img src="<?= base_url('assets/images/ligtas-logo.png') ?>" alt="LIGTAS illustration" class="max-w-full max-h-full object-contain block" loading="lazy" />
            </div>
        </div>
        <div class="p-8 md:p-12 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="text-center md:text-left border-b md:border-b-0 pb-6 mb-6 md:pb-0 md:mb-6">
                    <h1 class="text-3xl font-extrabold text-indigo-700">LIGTAS</h1>
                    <p class="text-sm text-indigo-600 mt-1">Local Incident Gathering and Tracking for Aquatic Safety</p>
                </div>
                <h4 class="text-lg font-semibold text-gray-700 mb-6">Welcome!</h4>

                <?php if (session()->getFlashdata('success')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                        <span class="mr-2">/</span><?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3">
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
                            class="w-full rounded-lg border border-gray-200 px-4 py-4 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent" />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" required placeholder="Password"
                            autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-200 px-4 py-4 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent" />
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-4 rounded-lg shadow transition transform hover:-translate-y-0.5 uppercase text-base">Sign In</button>
                    </div>
                    <div class="text-center text-sm text-slate-500 mt-3">
                        Don't have an account? <a href="<?= base_url('/register') ?>" class="text-indigo-600 font-medium hover:underline">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
