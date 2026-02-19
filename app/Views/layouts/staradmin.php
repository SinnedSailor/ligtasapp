<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'LIGTAS') ?></title>
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">
    <style>
        :root {
            --LIGTAS-primary: #0B5FB3;
            --LIGTAS-primary-dark: #094D8F;
            --LIGTAS-secondary: #C73A3A;
            --LIGTAS-secondary-dark: #A43030;
            --LIGTAS-header: #D4F6FF;
            --LIGTAS-main-bg: #FBFBFB;
            --LIGTAS-sidebar: FBFBFB;
            --LIGTAS-padding: #D4F6FF;
        }

        .navbar {
            background: #002C76 !important;
            position: fixed;
            width: 100%;
            z-index: 1030;
            min-height: 70px;
        }

        .navbar .navbar-brand-wrapper {
            background: #002C76 !important;
            height: 70px;
            display: flex;
            align-items: center;
        }

        .navbar .navbar-menu-wrapper {
            background: #002C76 !important;
            height: 70px;
            display: flex;
            align-items: center;
        }

        .navbar .navbar-menu-wrapper h6,
        .navbar .navbar-nav .nav-link,
        .navbar .navbar-nav .nav-link i {
            color: #f2fbff !important;
        }

        .navbar .navbar-nav .nav-link:hover,
        .navbar .navbar-nav .nav-link:focus {
            color: #002C76 !important;
        }

        .navbar .navbar-nav .nav-link.active,
        .navbar .navbar-nav .show > .nav-link {
            color: #002C76 !important;
        }

        .page-body-wrapper {
            background: var(--LIGTAS-main-bg);
            padding-top: 70px;
        }

        .page-body-wrapper.no-navbar {
            background: var(--LIGTAS-main-bg);
            padding-top: 0;
        }

        /* Ensure the visible page header sits directly under the fixed navbar
           - reduce extra top padding from the theme's .content-wrapper
           - prevent awkward gaps on all pages that use .page-header */
        .content-wrapper {
            padding-top: 0.5rem !important;
        }
        .content-wrapper > .page-header,
        .page-header {
            margin-top: 0 !important;
        }

        .main-panel.full-width {
            margin-left: 0 !important;
            width: 100%;
            background: var(--LIGTAS-main-bg);
        }

        .main-panel {
            margin-left: 260px;
            width: calc(100% - 260px);
            background: var(--LIGTAS-main-bg);
        }

        .sidebar,
        .sidebar .nav .nav-item.active > .nav-link,
        .sidebar .nav .nav-item:hover > .nav-link {
            background: var(--LIGTAS-sidebar);
        }

        .sidebar .nav .nav-item.active > .nav-link {
            background: #ffffff;
        }

        .sidebar .nav .nav-item:hover > .nav-link {
            background: #e6f0fb;
        }

        .sidebar .nav .nav-item:hover > .nav-link,
        .sidebar .nav .nav-item:hover > .nav-link i,
        .sidebar .nav .nav-item:hover > .nav-link .menu-title {
            color: var(--LIGTAS-primary) !important;
        }

        .sidebar .nav .nav-item .nav-link,
        .sidebar .nav .nav-item .nav-link,
        .sidebar .nav .nav-item .nav-link i,
        .sidebar .nav .nav-item .menu-title {
            color: #002C76 !important;
            font-weight: 700;
        }

        .sidebar .nav .nav-item.active > .nav-link,
        .sidebar .nav .nav-item.active > .nav-link i,
        .sidebar .nav .nav-item.active > .nav-link .menu-title {
            color: var(--LIGTAS-primary) !important;
        }

        .sidebar,
        .sidebar-offcanvas {
            height: 100vh;
            overflow: hidden !important;
            overscroll-behavior: contain;
            background: var(--LIGTAS-sidebar);
        }

        .sidebar-offcanvas {
            position: fixed;
            top: 70px;
            bottom: 0;
            left: 0;
            background: var(--LIGTAS-sidebar);
        }

        .page-body-wrapper.no-navbar .sidebar-offcanvas {
            top: 0;
        }

        .sidebar .nav {
            max-height: 100%;
            overflow: hidden !important;
        }

        .sidebar::-webkit-scrollbar,
        .sidebar-offcanvas::-webkit-scrollbar,
        .sidebar .nav::-webkit-scrollbar {
            display: none;
        }

        .sidebar .nav .nav-item .nav-link,
        .navbar .navbar-brand-wrapper .navbar-brand img,
        .navbar .navbar-brand-wrapper .navbar-brand-mini img {
            color: #1f2937;
        }

        .sidebar-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 18px 16px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 8px;
        }

        .sidebar-profile .avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #002C76;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            margin-bottom: 8px;
            border: 2px solid #fff;
            font-size: 2.5rem;
            position: relative;
            transition: box-shadow 0.2s;
        }
        .sidebar-profile .avatar:hover {
            box-shadow: 0 0 0 4px #d4f6ff;
            cursor: pointer;
        }
        .sidebar-profile .edit-avatar {
            display: none;
            position: absolute;
            right: -10px;
            bottom: 8px;
            background: #fff;
            border-radius: 50%;
            border: 1.5px solid #002C76;
            width: 32px;
            height: 32px;
            align-items: center;
            justify-content: center;
            color: #002C76;
            font-size: 1.2rem;
            z-index: 2;
        }
        .sidebar-profile .avatar:hover .edit-avatar {
            display: flex;
        }

        .sidebar-profile .name {
            color: #002C76;
            background: #fff;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            border-radius: 6px;
            padding: 2px 8px;
            margin-bottom: 2px;
        }

        .sidebar-profile .role {
            color: #002C76;
            background: #fff;
            font-size: 12px;
            text-align: center;
            border-radius: 6px;
            padding: 1px 8px;
        }

        .sidebar-profile .actions {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
        }

        .sidebar-profile .action-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
            background: #002C76;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,44,118,0.08);
        }

        .sidebar-profile .action-link:hover {
            background: #094D8F;
            color: #ffd86b;
        }

        .navbar .navbar-brand,
        .navbar .navbar-brand:visited,
        .navbar .navbar-brand:hover,
        .navbar .navbar-brand:active {
            color: #fff;
        }

        .text-primary,
        .page-title,
        .card-title,
        .stat-number {
            color: var(--LIGTAS-primary) !important;
        }

        .btn-primary,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--LIGTAS-primary) !important;
            border-color: var(--LIGTAS-primary) !important;
        }

        .btn-primary:hover {
            background-color: var(--LIGTAS-primary-dark) !important;
            border-color: var(--LIGTAS-primary-dark) !important;
        }

        .btn-secondary,
        .btn-secondary:focus,
        .btn-secondary:active {
            background-color: var(--LIGTAS-secondary) !important;
            border-color: var(--LIGTAS-secondary) !important;
            color: #fff !important;
        }

        .btn-secondary:hover {
            background-color: var(--LIGTAS-secondary-dark) !important;
            border-color: var(--LIGTAS-secondary-dark) !important;
        }

        .btn-light,
        .btn-light:hover,
        .btn-outline-light:hover {
            color: #1f2937 !important;
        }

        .profile-initials {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .container-scroller {
            background: #D4F6FF !important;
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body>
    <?php
        $path = trim(service('uri')->getPath(), '/');
        $root = $path === '' ? '' : explode('/', $path)[0];
        $firstName = session()->get('first_name');
        $lastName = session()->get('last_name');
        $username = session()->get('username');
        $initials = '';

        if ($firstName || $lastName) {
            $initials = strtoupper(substr($firstName ?? '', 0, 1) . substr($lastName ?? '', 0, 1));
        }

        if ($initials === '') {
            $initials = strtoupper(substr($username ?? 'IW', 0, 2));
        }

        $hideNavbar = !empty($hideNavbar);
        $hideSidebar = !empty($hideSidebar);
        $hideFooter = !empty($hideFooter);
        $pageBodyClass = $hideNavbar ? 'page-body-wrapper no-navbar' : 'page-body-wrapper';
        $mainPanelClass = $hideSidebar ? 'main-panel full-width' : 'main-panel';
    ?>
                </div>
                <?php if (!$hideFooter): ?>
                    <?= view('components/footer') ?>
                <?php endif; ?>
            </div>
        </div>

    <div class="container-scroller"> 
        <?php if (!$hideNavbar): ?>
            <nav class="w-full fixed top-0 left-0 bg-slate-800 text-white px-4 py-3 shadow-sm z-40">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a class="text-lg font-bold tracking-wide" href="<?= base_url('/dashboard') ?>">LIGTAS</a>
                        <span class="hidden md:inline text-slate-300">Local Incident Gathering and Tracking for Aquatic Safety</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="lg:hidden p-2 rounded-md bg-slate-700 hover:bg-slate-600" type="button" aria-label="Open menu"><?= view('components/icon', ['name' => 'menu', 'class' => 'w-5 h-5 text-white']) ?></button>
                        <a href="#" id="logoutBtn" title="Log out" class="ml-3 text-white text-xl" style="display:flex;align-items:center;"><?= view('components/icon', ['name' => 'power', 'class' => 'w-5 h-5 text-white']) ?></a>
                    </div>
                </div>
            </nav>
        <?php endif; ?>

        <div class="container-fluid <?= $pageBodyClass ?>">
            <?php if (!$hideSidebar): ?>
                <nav id="sidebar" class="w-64 h-screen flex flex-col bg-white/5 p-4">
                    <div class="space-y-6">
                        <div class="sidebar-profile text-center">
                            <div class="avatar mx-auto mb-3 w-12 h-12 rounded-full bg-slate-700 text-white flex items-center justify-center"> <?= esc($initials) ?>
                                <a href="<?= base_url('/user-profile') ?>" class="ml-2 text-sm text-slate-400" title="Edit Profile"><?= view('components/icon', ['name' => 'pencil', 'class' => 'w-4 h-4']) ?></a>
                            </div>
                            <div class="name font-semibold text-sm">
                                <?= esc(trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: ($username ?? 'User')) ?>
                            </div>
                            <div class="role text-xs text-slate-400"><?= esc(session()->get('role_name') ?? 'User') ?></div>
                        </div>
                        <ul class="flex-1 space-y-2 mt-4">
                        <li class="<?= $root === '' || $root === 'dashboard' ? 'bg-slate-100/10 rounded-md' : '' ?>">
                            <a class="flex items-center gap-3 px-3 py-2 text-sm" href="<?= base_url('/dashboard') ?>">
                                <?= view('components/icon', ['name' => 'home', 'class' => 'inline-block w-5 h-5']) ?>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="<?= $root === 'ordinance' ? 'bg-slate-100/10 rounded-md' : '' ?>">
                            <a class="flex items-center gap-3 px-3 py-2 text-sm" href="<?= base_url('/ordinance') ?>">
                                <span class="menu-icon">
                                    <?= session()->get('role_name') === 'FOCAL' ? view('components/icon', ['name' => 'eye', 'class' => 'inline-block w-4 h-4']) : (session()->get('role_name') === 'PROVINCE' ? view('components/icon', ['name' => 'check', 'class' => 'inline-block w-4 h-4']) : view('components/icon', ['name' => 'upload', 'class' => 'inline-block w-4 h-4'])) ?>
                                </span>
                                <span class="menu-title">
                                    <?= session()->get('role_name') === 'FOCAL' ? 'View Documents' : (session()->get('role_name') === 'PROVINCE' ? 'Review Documents' : 'Upload Documents') ?>
                                </span>
                            </a>
                        </li>
                        <li class="<?= $root === 'incident-report' ? 'bg-slate-100/10 rounded-md' : '' ?>">
                            <a class="flex items-center gap-3 px-3 py-2 text-sm" href="<?= base_url('/incident-report') ?>">
                                <?= view('components/icon', ['name' => 'alert', 'class' => 'inline-block w-5 h-5 text-yellow-500']) ?>
                                <span class="menu-title">Incident Report</span>
                            </a>
                        </li>
                        <?php if (session()->get('is_admin')): ?>
                            <li class="<?= $root === 'admin-panel' ? 'bg-slate-100/10 rounded-md' : '' ?>">
                                <a class="flex items-center gap-3 px-3 py-2 text-sm" href="<?= base_url('/admin-panel') ?>">
                                    <?= view('components/icon', ['name' => 'shield', 'class' => 'inline-block w-5 h-5']) ?>
                                    <span class="menu-title">Admin Panel</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                        
                </nav>
            <?php endif; ?>

            <div class="<?= $mainPanelClass ?>">
                <div class="content-wrapper">
                    <?= $this->renderSection('content') ?>
                </div>

    <?= $this->renderSection('pageScripts') ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Dropzone.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logout confirmation
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Log out?',
                        text: 'Are you sure you want to log out?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0b45aa',
                        cancelButtonColor: '#C9282D',
                        confirmButtonText: 'Yes, log out',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url('/logout') ?>";
                        }
                    });
                });
            }

            // Show login success alert 
            <?php if (session()->getFlashdata('login_success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Logged in successfully',
                    showConfirmButton: false,
                    timer: 1500
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
