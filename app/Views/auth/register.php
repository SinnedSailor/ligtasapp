<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<style>
    .auth-bg { background: linear-gradient(135deg,#09637E 0%, #0B4F63 60%); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl p-8">
        <div class="text-center border-b pb-6 mb-6">
            <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-blue-900 to-teal-700 flex items-center justify-center text-white text-2xl shadow mb-3">💧</div>
            <h3 class="text-2xl font-extrabold text-blue-900">LIGTAS</h3>
            <p class="text-sm text-gray-500 mt-1">Local Incident Gathering and Tracking for Aquatic Safety</p>
        </div>

        <h4 class="text-lg font-semibold text-gray-700 mb-4">Create your account</h4>
        <p class="text-sm text-gray-500 mb-6">Fill in the details to register</p>

        <?php if (session()->has('error')): ?>
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                <span class="mr-2">⚠️</span><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/store-register') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= csrf_field() ?>

            <div>
                <label for="first_name" class="sr-only">First name</label>
                <input id="first_name" name="first_name" type="text" required placeholder="First name" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="last_name" class="sr-only">Last name</label>
                <input id="last_name" name="last_name" type="text" required placeholder="Last name" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="username" class="sr-only">Username</label>
                <input id="username" name="username" type="text" required placeholder="Username" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="email" class="sr-only">Email</label>
                <input id="email" name="email" type="email" required placeholder="Email" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="province" class="sr-only">Province</label>
                <select id="province" name="province" required class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">Select province</option>
                    <?php foreach (($provinces ?? []) as $province): ?>
                        <option value="<?= esc($province) ?>" <?= (old('province') === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="municipality" class="sr-only">Municipality</label>
                <select id="municipality" name="municipality" required class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">Select municipality</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" required placeholder="Password" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div class="md:col-span-2">
                <label for="password_confirm" class="sr-only">Confirm Password</label>
                <input id="password_confirm" name="password_confirm" type="password" required placeholder="Confirm Password" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-3 rounded-lg shadow uppercase text-sm">Create Account</button>
            </div>

            <div class="md:col-span-2 text-center text-sm text-gray-500">
                Already have an account? <a href="<?= base_url('/login') ?>" class="text-blue-700 font-medium hover:underline">Sign in</a>
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
