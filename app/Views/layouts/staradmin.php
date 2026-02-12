<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'LIGTAS') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/feather/feather.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/ti-icons/css/themify-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/vendors/css/vendor.bundle.base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/staradmin/css/style.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/staradmin/images/favicon.png') ?>" />
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
    <div class="container-scroller">
        <?php if (!$hideNavbar): ?>
            <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
                <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                    <a class="navbar-brand brand-logo fw-bold" href="<?= base_url('/dashboard') ?>">LIGTAS</a>
                    <a class="navbar-brand brand-logo-mini fw-bold" href="<?= base_url('/dashboard') ?>">I</a>
                </div>
                <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                    <h6 class="mb-0 d-none d-md-block text-primary">Local Incident Gathering and Tracking for Aquatic Safety</h6>
                    <ul class="navbar-nav navbar-nav-right"></ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                        <span class="ti-layout-grid2"></span>
                    </button>
                    <!-- Logout Icon Button -->
                    <a href="<?= base_url('/logout') ?>" title="Log out" class="ms-3" style="color: #fff; font-size: 1.5rem; display: flex; align-items: center;">
                        <i class="ti-power-off"></i>
                    </a>
                </div>
            </nav>
        <?php endif; ?>

        <div class="container-fluid <?= $pageBodyClass ?>">
            <?php if (!$hideSidebar): ?>
                <nav class="sidebar sidebar-offcanvas d-flex flex-column" id="sidebar" style="height: 100vh;">
                    <div>
                        <div class="sidebar-profile">
                            <div class="avatar position-relative">
                                <?= esc($initials) ?>
                                <a href="<?= base_url('/user-profile') ?>" class="edit-avatar" title="Edit Profile">
                                    <i class="ti-pencil"></i>
                                </a>
                            </div>
                            <div class="name">
                                <?= esc(trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: ($username ?? 'User')) ?>
                            </div>
                            <div class="role"><?= esc(session()->get('role_name') ?? 'User') ?></div>
                        </div>
                        <ul class="nav flex-grow-1">
                        <li class="nav-item <?= $root === '' || $root === 'dashboard' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= base_url('/dashboard') ?>">
                                <i class="ti-home menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item <?= $root === 'ordinance' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= base_url('/ordinance') ?>">
                                <i class="<?= session()->get('role_name') === 'FOCAL' ? 'ti-eye' : (session()->get('role_name') === 'PROVINCE' ? 'ti-check-box' : 'ti-upload') ?> menu-icon"></i>
                                <span class="menu-title">
                                    <?= session()->get('role_name') === 'FOCAL' ? 'View Documents' : (session()->get('role_name') === 'PROVINCE' ? 'Review Documents' : 'Upload Documents') ?>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item <?= $root === 'incident-report' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= base_url('/incident-report') ?>">
                                <i class="ti-alert menu-icon"></i>
                                <span class="menu-title">Incident Report</span>
                            </a>
                        </li>
                        <?php if (session()->get('is_admin')): ?>
                            <li class="nav-item <?= $root === 'admin-panel' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('/admin-panel') ?>">
                                    <i class="ti-shield menu-icon"></i>
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
                <?php if (!$hideFooter): ?>
                    <footer class="footer">
                        <div class="d-sm-flex justify-content-center justify-content-sm-between">
                            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">LIGTAS 2026</span>
                            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Local Incident Gathering and Tracking for Aquatic Safety</span>
                        </div>
                    </footer>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/staradmin/vendors/js/vendor.bundle.base.js') ?>"></script>
    <script src="<?= base_url('assets/staradmin/js/off-canvas.js') ?>"></script>
    <script src="<?= base_url('assets/staradmin/js/hoverable-collapse.js') ?>"></script>
    <script src="<?= base_url('assets/staradmin/js/template.js') ?>"></script>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
