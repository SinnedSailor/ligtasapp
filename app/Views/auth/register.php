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
            linear-gradient(135deg,rgba(4, 242, 255, 0.6) 0%, rgba(22, 41, 209, 0.6) 60%),
            url('<?= base_url('assets/images/water.png') ?>') center/cover no-repeat;
        background-size: cover;
        background-position: center;
    }

    .glass-card {
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 3rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page auth-bg min-h-screen flex items-center justify-center py-12 px-4">
    <div class="glass-card shadow-2xl w-full max-w-lg p-6">
        <div class="text-center border-b pb-6 mb-6">
            <div class="mb-4 w-16 h-16 rounded-full overflow-hidden mx-auto shadow">
                <img src="<?= base_url('assets/images/ligtas.png') ?>" alt="LIGTAS logo" class="w-full h-full object-cover" />
            </div>
            <h3 class="text-2xl font-extrabold text-white">LIGTAS</h3>
            <p class="text-sm text-white">Local Incident Gathering and Tracking for Aquatic Safety</p>
        </div>

        <h4 class="text-lg font-semibold text-white">Create your account</h4>
        <p class="text-sm text-white mb-3">Fill in the details to register</p>

        <?php if (session()->has('error')): ?>
            <div class="mb-4 rounded-md bg-red-900 bg-opacity-50 border border-red-700 text-red-100 px-4 py-3">
                <span class="mr-2">⚠️</span><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/store-register') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <?= csrf_field() ?>

            <div>
                <label for="first_name" class="sr-only">First name</label>
                <input id="first_name" name="first_name" type="text" required placeholder="First name" pattern="[A-Za-z\s]+" title="Letters and spaces only" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="last_name" class="sr-only">Last name</label>
                <input id="last_name" name="last_name" type="text" required placeholder="Last name" pattern="[A-Za-z\s]+" title="Letters and spaces only" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="username" class="sr-only">Username</label>
                <input id="username" name="username" type="text" required placeholder="Username" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="email" class="sr-only">Email</label>
                <input id="email" name="email" type="email" required placeholder="Email" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300" />
            </div>

            <div>
                <label for="province" class="sr-only">Province</label>
                <select id="province" name="province" required class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">Select province</option>
                    <?php foreach (($provinces ?? []) as $province): ?>
                        <option value="<?= esc($province) ?>" <?= (old('province') === $province) ? 'selected' : '' ?>><?= esc($province) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="municipality" class="sr-only">Municipality</label>
                <select id="municipality" name="municipality" required class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm text-black placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">Select municipality</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="password" class="sr-only">Password</label>
                <div class="relative w-full rounded-lg overflow-hidden border border-gray-200 bg-white">
                    <input id="password" name="password" type="password" required placeholder="Password" class="w-full bg-transparent px-4 py-3 pr-10 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-0" />
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="password_confirm" class="sr-only">Confirm Password</label>
                <div class="relative w-full rounded-lg overflow-hidden border border-gray-200 bg-white">
                    <input id="password_confirm" name="password_confirm" type="password" required placeholder="Confirm Password" class="w-full bg-transparent px-4 py-3 pr-10 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-0" />
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center text-white text-sm">
                    <input id="agree" name="agree" type="checkbox" required class="form-checkbox h-4 w-4 text-blue-600" <?= old('agree') ? 'checked' : '' ?> />
                    <span class="ml-2">I agree to the <a href="#" id="showTerms" class="underline hover:text-blue-300">Data Privacy Act of 2012 (RA&nbsp;10173)</a></span>
                </label>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-3 rounded-lg shadow uppercase text-sm">Create Account</button>
            </div>

            <div class="md:col-span-2 text-center text-sm text-white-500">
                Already have an account? <a href="<?= base_url('/login') ?>" class="text-white font-medium hover:underline">Sign in</a>
            </div>
        </form>
    </div>
</div>

<!-- Terms modal -->
<div id="termsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg max-w-lg w-full mx-4 p-6 relative">
        <button id="closeTerms" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl leading-none">
            &times;
        </button>
        <h2 class="text-xl font-semibold mb-4">Data Privacy Act of 2012 (RA 10173)</h2>
        <div class="h-64 overflow-y-auto text-sm leading-relaxed text-gray-700">
            <p class="text-justify indent-4 mb-4">This application acknowledges and upholds the provisions of the Data Privacy Act of 2012 (Republic Act No. 10173). Users must consent to the collection, processing, and storage of their personal information in accordance with the Act. The information collected will be used solely for the purpose of creating and managing accounts and will not be shared with unauthorized third parties.</p>
            <p class="text-justify indent-4 mb-4">By checking the agreement box on the registration form, you state that you have read, understood, and agree to the terms of RA 10173. You also acknowledge that you have been informed about your rights under the Act, including your right to access, correct, and request deletion of your personal data.</p>
            <p class="text-justify indent-4 mb-4">For the full text of the law, please visit <a href="https://www.privacy.gov.ph/data-privacy-act/" target="_blank" class="underline text-blue-600">the official website</a>.</p>
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

    // show privacy terms modal
    const showTermsBtn = document.getElementById('showTerms');
    const termsModal = document.getElementById('termsModal');
    const closeTermsBtn = document.getElementById('closeTerms');

    if (showTermsBtn && termsModal && closeTermsBtn) {
        showTermsBtn.addEventListener('click', (e) => {
            e.preventDefault();
            termsModal.classList.remove('hidden');
        });
        closeTermsBtn.addEventListener('click', () => {
            termsModal.classList.add('hidden');
        });
    }

    // password visibility toggles
    document.querySelectorAll('.toggle-password').forEach(btn => {
        const input = btn.previousElementSibling;
        const eyeOff = `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.591m1.456-1.457A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.546m-3.03.239a3 3 0 11-4.243-4.243" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>`;
        btn.addEventListener('click', () => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            btn.innerHTML = type === 'password' ? btn.dataset.eye : eyeOff;
        });
        // store original eye icon
        btn.dataset.eye = btn.innerHTML;
    });

    // prevent numbers in name fields while typing
    ['first_name','last_name'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', () => {
                el.value = el.value.replace(/[^A-Za-z\s]/g, '');
            });
        }
    });
</script>
<?= $this->endSection() ?>
