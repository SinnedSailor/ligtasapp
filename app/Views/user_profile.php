<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .profile-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .profile-picture-section {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .profile-picture-upload {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .profile-picture-preview {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #09637E;
        overflow: hidden;
    }

    .profile-picture-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(9, 99, 126, 0.85);
        color: #fff;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        cursor: pointer;
        border-bottom-left-radius: 50%;
        border-bottom-right-radius: 50%;
    }

    .form-section {
        margin-top: 20px;
    }

    .form-section h5 {
        color: #09637E;
        margin-bottom: 10px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
    }

    .forgot-password-link {
        text-decoration: none;
        color: #09637E;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h3 class="page-title">User Profile</h3>
    <p class="text-muted">Manage your account information and settings.</p>
</div>

<div class="row">
    <div class="col-lg-8 grid-margin">
        <div class="profile-card">
            <div class="profile-picture-section">
                <div class="profile-picture-upload">
                    <div class="profile-picture-preview" id="profilePreview">
                        <i class="ti-user"></i>
                    </div>
                    <div class="upload-overlay" onclick="document.getElementById('profilePicture').click()">
                        <i class="ti-camera"></i> Change Photo
                    </div>
                    <input type="file" id="profilePicture" class="d-none" accept="image/*" onchange="previewImage(event)">
                </div>
            </div>

            <div class="alert alert-info">
                Keep your profile information up to date for better communication.
            </div>

            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="form-section">
                    <h5>Personal Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" class="form-control" id="firstName" value="<?= session()->get('first_name') ?? '' ?>" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" class="form-control" id="lastName" value="<?= session()->get('last_name') ?? '' ?>" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" class="form-control" id="username" value="<?= session()->get('username') ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number *</label>
                            <input type="tel" class="form-control" id="contactNumber" placeholder="e.g., 09123456789" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" title="Please enter 11-digit phone number (numbers only)" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" class="form-control" id="email" value="<?= session()->get('email') ?? '' ?>" disabled>
                    </div>
                </div>

                <div class="form-section">
                    <h5>Location Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="province">Province *</label>
                            <input type="text" class="form-control" id="province" list="provinceList" value="<?= session()->get('province') ?? '' ?>" required placeholder="Search or select province">
                            <datalist id="provinceList">
                                <option value="Ilocos Norte">
                                <option value="Ilocos Sur">
                                <option value="La Union">
                                <option value="Pangasinan">
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="municipality">Municipality *</label>
                            <input type="text" class="form-control" id="municipality" list="municipalityList" value="<?= session()->get('municipality') ?? '' ?>" required placeholder="Search or select municipality">
                            <datalist id="municipalityList"></datalist>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5>Password Settings</h5>
                    <p class="text-muted">Need to change your password?</p>
                    <a href="#" class="forgot-password-link" onclick="forgotPassword(event)">
                        <i class="ti-lock"></i> Reset Password via Email
                    </a>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti-check"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('/dashboard') ?>'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture">`;
            };
            reader.readAsDataURL(file);
        }
    }

    function saveProfile(event) {
        event.preventDefault();

        const contactNumber = document.getElementById('contactNumber').value;
        if (contactNumber && !/^[0-9]{11}$/.test(contactNumber)) {
            alert('Please enter a valid 11-digit contact number.');
            return;
        }

        alert('Profile updated successfully!');
    }

    function forgotPassword(event) {
        event.preventDefault();
        const email = document.getElementById('email').value;
        if (confirm(`Send password reset link to ${email}?`)) {
            alert('Password reset link has been sent to your email. Please check your inbox.');
        }
    }

    const municipalities = {
        'Ilocos Norte': ['Laoag City', 'Batac City', 'Pagudpud', 'Bangui', 'Pasuquin', 'Burgos', 'Bacarra', 'Vintar', 'Dumalneg', 'Solsona', 'Dingras', 'Nueva Era', 'Marcos', 'Banna', 'Sarrat', 'Carasi', 'Piddig', 'Pinili', 'San Nicolas', 'Badoc', 'Currimao', 'Paoay'],
        'Ilocos Sur': ['Vigan City', 'Candon City', 'Santa Cruz', 'Santa Maria', 'Narvacan', 'Santiago', 'Bantay', 'Caoayan', 'Santa Catalina', 'Magsingal', 'San Vicente', 'San Ildefonso', 'San Juan', 'Cabugao', 'Sinait', 'San Esteban', 'Burgos', 'Santa Lucia', 'Lidlidda', 'Tagudin', 'Suyo', 'Alilem', 'Sugpon', 'Sudipen', 'Banayoyo', 'Galimuyod', 'Gregorio del Pilar', 'Sigay', 'Salcedo', 'Santa', 'Quirino', 'Cervantes'],
        'La Union': ['San Fernando City', 'Bauang', 'Naguilian', 'San Juan', 'Bacnotan', 'Balaoan', 'Luna', 'Bangar', 'Santol', 'San Gabriel', 'Sudipen', 'Caba', 'Aringay', 'Tubao', 'Pugo', 'Rosario', 'Santo Tomas', 'Agoo', 'Burgos'],
        'Pangasinan': ['Dagupan City', 'San Carlos City', 'Urdaneta City', 'Alaminos City', 'Lingayen', 'Mangaldan', 'Manaoag', 'Pozorrubio', 'Sison', 'Binalonan', 'Laoac', 'San Fabian', 'San Jacinto', 'Rosales', 'Umingan', 'Balungao', 'Santa Maria', 'Alcala', 'Bautista', 'Bayambang', 'Bugallon', 'Infanta', 'Labrador', 'Mabini', 'Malasiqui', 'Mapandan', 'Natividad', 'San Manuel', 'San Nicolas', 'San Quintin', 'Santa Barbara', 'Tayug', 'Uyong', 'Villasis', 'Asingan', 'Binmaley', 'Bolinao', 'Burgos', 'Dasol', 'Sual']
    };

    document.getElementById('province').addEventListener('change', function() {
        const province = this.value;
        const municipalityDatalist = document.getElementById('municipalityList');
        municipalityDatalist.innerHTML = '';

        if (province && municipalities[province]) {
            municipalities[province].forEach(mun => {
                const option = document.createElement('option');
                option.value = mun;
                municipalityDatalist.appendChild(option);
            });
        }
    });

    document.getElementById('province').addEventListener('input', function() {
        const province = this.value;
        const municipalityDatalist = document.getElementById('municipalityList');
        municipalityDatalist.innerHTML = '';

        if (province && municipalities[province]) {
            municipalities[province].forEach(mun => {
                const option = document.createElement('option');
                option.value = mun;
                municipalityDatalist.appendChild(option);
            });
        }
    });
</script>
<?= $this->endSection() ?>
