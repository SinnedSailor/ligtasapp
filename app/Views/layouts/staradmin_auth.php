<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'IWAS') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/feather/feather.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/ti-icons/css/themify-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/css/vendor.bundle.base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/css/style.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/staradmin/images/favicon.png') ?>" />
    <style>
        :root {
            --iwas-primary: #09637E;
            --iwas-accent-red: #C94A4A;
            --iwas-accent-yellow: #D6B443;
            --iwas-accent-green: #3FAF7B;
            --iwas-light: #f2fbff;
        }
        
        body.auth {
            background: linear-gradient(135deg, #09637E 0%, #0B4F63 50%, #002C76 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        
        body.auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(217, 180, 67, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(63, 175, 123, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 60% 60%, rgba(201, 74, 74, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .container-scroller,
        .page-body-wrapper,
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
        
        .auth-form-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .auth-form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--iwas-primary) 0%, var(--iwas-accent-green) 50%, var(--iwas-accent-yellow) 100%);
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="auth">
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-5 mx-auto">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/staradmin/vendors/js/vendor.bundle.base.js') ?>"></script>
    <script src="<?= base_url('assets/staradmin/js/template.js') ?>"></script>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
