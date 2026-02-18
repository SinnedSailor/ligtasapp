<?php
$hideNavbar = true;
$hideSidebar = true;
$hideFooter = true;
?>

<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<!-- inline styles removed — replaced with Tailwind utilities in markup -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-8 bg-gradient-to-r from-blue-900 to-blue-700 text-white">
            <h3 class="text-xl font-semibold">Create Administrator Account</h3>
            <p class="text-sm text-blue-100/80 mt-1">Set up the first admin user for your system</p>
        </div>
        <div class="px-6 py-6">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4">
                    <div class="flex items-start justify-between gap-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-3">
                        <div><?= session()->getFlashdata('error') ?></div>
                        <button type="button" class="text-red-800 font-bold" onclick="this.closest('.mb-4').remove()">&times;</button>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/storeFirstAdmin') ?>" id="adminForm">
                <div class="mb-4">
                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" id="firstName" name="first_name" required value="<?= old('first_name') ?>" placeholder="Enter first name" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="mb-4">
                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" id="lastName" name="last_name" required value="<?= old('last_name') ?>" placeholder="Enter last name" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                    <input type="text" id="username" name="username" required value="<?= old('username') ?>" placeholder="Enter username" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" required value="<?= old('email') ?>" placeholder="Enter email address" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="mb-4">
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                    <select id="province" name="province" required class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300">
                        <option value="">Select province</option>
                        <?php foreach (($provinces ?? []) as $province): ?>
                            <option value="<?= esc($province) ?>" <?= (old('province') === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="municipality" class="block text-sm font-medium text-gray-700 mb-2">Municipality</label>
                    <select id="municipality" name="municipality" required class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300">
                        <option value="">Select municipality</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" id="password" name="password" required placeholder="Enter a strong password" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="mb-4">
                    <label for="passwordConfirm" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <input type="password" id="passwordConfirm" name="password_confirm" required placeholder="Confirm your password" class="block w-full rounded-md border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-300" />
                </div>

                <div class="bg-gray-50 rounded-md p-4 text-sm text-gray-700 mb-4">
                    <strong class="block mb-2">Password Requirements:</strong>
                    <div class="flex items-center gap-2 mb-1"><span class="text-green-600">✓</span><span>At least 8 characters</span></div>
                    <div class="flex items-center gap-2 mb-1"><span class="text-green-600">✓</span><span>One uppercase letter (A-Z)</span></div>
                    <div class="flex items-center gap-2 mb-1"><span class="text-green-600">✓</span><span>One lowercase letter (a-z)</span></div>
                    <div class="flex items-center gap-2 mb-1"><span class="text-green-600">✓</span><span>One number (0-9)</span></div>
                    <div class="flex items-center gap-2"><span class="text-green-600">✓</span><span>One special character (!@#$%^&* etc.)</span></div>
                </div>

                <button type="submit" class="btn-primary w-full mt-4">Create Admin Account</button>

                <div class="text-center mt-3">
                    <p class="text-gray-500 text-sm">
                        Already have an account? <a href="<?= base_url('login') ?>" class="text-blue-700 underline">Login here</a>
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
