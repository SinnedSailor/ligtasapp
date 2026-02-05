<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IWAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1C4D8D 0%, #2563A8 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 900px;
            padding: 40px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #1C4D8D;
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            background: #1C4D8D;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(28, 77, 141, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .login-link p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .login-link a {
            color: #1C4D8D;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #154470;
        }

        .error-message {
            background-color: #fee;
            border-left: 4px solid #f44336;
            color: #c62828;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .password-section {
            grid-column: 1 / -1;
        }

        .password-group {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: flex-end;
        }

        .password-input-wrapper {
            display: flex;
            flex-direction: column;
        }

        .generate-btn {
            padding: 12px 20px;
            background: #2563A8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .generate-btn:hover {
            background: #1C4D8D;
        }

        .password-strength {
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength.weak {
            background-color: #ffebee;
            color: #c62828;
        }

        .password-strength.medium {
            background-color: #fff3e0;
            color: #e65100;
        }

        .password-strength.strong {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>IWAS</h1>
            <p>Create Your Account</p>
        </div>

        <?php if (session()->has('error')): ?>
            <div class="error-message">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/store-register') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Create a username">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="province">Province <span style="color: red;">*</span></label>
                    <select id="province" name="province" required onchange="updateMunicipality()">
                        <option value="">-- Select Province --</option>
                        <option value="Ilocos Norte">Ilocos Norte</option>
                        <option value="Ilocos Sur">Ilocos Sur</option>
                        <option value="La Union">La Union</option>
                        <option value="Pangasinan">Pangasinan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="municipality">Municipality <span style="color: red;">*</span></label>
                    <select id="municipality" name="municipality" required disabled>
                        <option value="">-- Select Municipality --</option>
                    </select>
                </div>

                <div class="form-group form-full password-section">
                    <label for="password">Password <span style="color: red;">*</span></label>
                    <div class="password-group">
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" required placeholder="Create a password (min 8 chars, uppercase, lowercase, number, special char)" oninput="checkPasswordStrength()">
                            <div class="password-strength" id="strength"></div>
                        </div>
                        <button type="button" class="generate-btn" onclick="generatePassword()">Generate</button>
                    </div>
                </div>

                <div class="form-group form-full">
                    <label for="password_confirm">Confirm Password <span style="color: red;">*</span></label>
                    <input type="password" id="password_confirm" name="password_confirm" required placeholder="Confirm your password">
                </div>

                <button type="submit" class="register-btn form-full">Register</button>
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account?</p>
            <a href="<?= base_url('/login') ?>">Login here</a>
        </div>
    </div>

    <script>
        // Municipality data by province
        const municipalities = {
            'Ilocos Norte': ['Laoag', 'Batac', 'Adams', 'Bacarra', 'Badoc', 'Bangui', 'Banna', 'Burgos', 'Carasi', 'Currimao', 'Dingras', 'Dumalneg', 'Nueva Era', 'Pagudpud', 'Pasuquin', 'Paoay', 'San Nicolas', 'Sarrat', 'Solsona', 'Vintar'],
            'Ilocos Sur': ['Vigan', 'Candon', 'Alilem', 'Anao-os', 'Bantay', 'Cabugao', 'Caoayan', 'Castillejos', 'Cervantes', 'Claveria', 'Concepcion', 'Galimuyod', 'Guimbal', 'Magsingal', 'Mangatarem', 'Narvacan', 'Paetaño', 'Pigilan', 'Salcedo', 'San Esteban', 'San Ildefonso', 'San Juan', 'Santa Catalina', 'Santa Cruz', 'Santa Lucia', 'Santa Maria', 'Santiago', 'Santo Domingo', 'Sigay', 'Sinait', 'Sugpon', 'Tagudin'],
            'La Union': ['San Fernando', 'Agoo', 'Aringay', 'Bacnotan', 'Bagulin', 'Bangar', 'Bauang', 'Burgos', 'Caba', 'Casiguran', 'Luna', 'Naguilian', 'Pugo', 'Rosario', 'San Gabriel', 'San Juan', 'Santiago', 'Santol', 'Sudipen', 'Tubao'],
            'Pangasinan': ['Dagupan', 'Lingayen', 'Alaminos', 'Aguilar', 'Asingan', 'Balungao', 'Bani', 'Basista', 'Bayambang', 'Binalonan', 'Binmaley', 'Bolinao', 'Bugallon', 'Calasiao', 'Dasol', 'Infanta', 'Jaro', 'Labrador', 'Laoac', 'Lingayen', 'Luba', 'Lubao', 'Luisiana', 'Mangatarem', 'Mangaldan', 'Mapandan', 'Masinloc', 'Milagros', 'Minsingao', 'Monsalud', 'Mubis', 'Munai', 'Natividad', 'Oton', 'Palayan', 'Paniqui', 'Pantalon', 'Pantasagan', 'Panuyas', 'Paombong', 'Papaya', 'Paracale', 'Parang']
        };

        function updateMunicipality() {
            const province = document.getElementById('province').value;
            const municipalitySelect = document.getElementById('municipality');
            
            municipalitySelect.innerHTML = '<option value="">-- Select Municipality --</option>';
            
            if (province && municipalities[province]) {
                municipalities[province].forEach(mun => {
                    const option = document.createElement('option');
                    option.value = mun;
                    option.textContent = mun;
                    municipalitySelect.appendChild(option);
                });
                municipalitySelect.disabled = false;
            } else {
                municipalitySelect.disabled = true;
            }
        }

        function generatePassword() {
            const length = 12;
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const numbers = '0123456789';
            const special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
            
            let password = '';
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += special.charAt(Math.floor(Math.random() * special.length));
            
            const allChars = uppercase + lowercase + numbers + special;
            for (let i = password.length; i < length; i++) {
                password += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }
            
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            document.getElementById('password').value = password;
            document.getElementById('password').type = 'text';
            checkPasswordStrength();
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('strength');
            
            if (password.length === 0) {
                strengthDiv.classList.remove('show');
                return;
            }

            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password);
            const isLongEnough = password.length >= 8;

            const requirements = [hasUppercase, hasLowercase, hasNumber, hasSpecial, isLongEnough];
            const metRequirements = requirements.filter(req => req).length;

            strengthDiv.classList.add('show');

            if (metRequirements <= 2) {
                strengthDiv.textContent = '❌ Weak - Missing requirements';
                strengthDiv.className = 'password-strength weak show';
            } else if (metRequirements <= 4) {
                strengthDiv.textContent = '⚠️ Medium - Nearly there';
                strengthDiv.className = 'password-strength medium show';
            } else {
                strengthDiv.textContent = '✓ Strong - All requirements met';
                strengthDiv.className = 'password-strength strong show';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password);
            const isLongEnough = password.length >= 8;

            if (!hasUppercase || !hasLowercase || !hasNumber || !hasSpecial || !isLongEnough) {
                e.preventDefault();
                alert('Password must contain:\n✓ At least 8 characters\n✓ Uppercase letter\n✓ Lowercase letter\n✓ Number\n✓ Special character (!@#$%^&* etc)');
                return;
            }

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
        });
    </script>
</body>
</html>
