<?php
// app/Views/admin/create_first_admin.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 500px;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #667eea;
            color: white;
            border: none;
            padding: 2rem;
        }
        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-primary {
            background-color: #667eea;
            border: none;
        }
        .btn-primary:hover {
            background-color: #5568d3;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .password-requirements {
            font-size: 0.875rem;
            line-height: 1.6;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
        .password-requirements strong {
            display: block;
            margin-bottom: 0.5rem;
        }
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        .requirement-icon {
            margin-right: 0.5rem;
            display: inline-block;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Create Administrator Account</h3>
                <p class="mb-0 text-white-50">Set up the first admin user for your system</p>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('admin/storeFirstAdmin') ?>" id="adminForm">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name *</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required 
                               value="<?= old('first_name') ?>" placeholder="Enter first name">
                    </div>

                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name *</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required 
                               value="<?= old('last_name') ?>" placeholder="Enter last name">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" required 
                               value="<?= old('username') ?>" placeholder="Enter username">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?= old('email') ?>" placeholder="Enter email address">
                    </div>

                    <div class="mb-3">
                        <label for="province" class="form-label">Province</label>
                        <input type="text" class="form-control" id="province" name="province" 
                               value="<?= old('province') ?>" placeholder="Enter province">
                    </div>

                    <div class="mb-3">
                        <label for="municipality" class="form-label">Municipality</label>
                        <input type="text" class="form-control" id="municipality" name="municipality" 
                               value="<?= old('municipality') ?>" placeholder="Enter municipality">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Enter a strong password">
                    </div>

                    <div class="mb-3">
                        <label for="passwordConfirm" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="passwordConfirm" name="password_confirm" required 
                               placeholder="Confirm your password">
                    </div>

                    <div class="password-requirements">
                        <strong>Password Requirements:</strong>
                        <div class="requirement">
                            <span class="requirement-icon">✓</span>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon">✓</span>
                            <span>One uppercase letter (A-Z)</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon">✓</span>
                            <span>One lowercase letter (a-z)</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon">✓</span>
                            <span>One number (0-9)</span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon">✓</span>
                            <span>One special character (!@#$%^&* etc.)</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">Create Admin Account</button>

                    <div class="text-center mt-3">
                        <p class="text-muted">
                            Already have an account? <a href="<?= base_url('login') ?>" class="text-primary text-decoration-none">Login here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
