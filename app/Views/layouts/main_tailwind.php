<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'LIGTAS') ?></title>

    <!-- Tailwind (compiled) only -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">

    <style>
        /* shift fixed topbar to the right so it does not overlap the sidebar */
        @media (min-width: 768px) {
            .topbar-shift { left: 18rem !important; width: calc(100% - 18rem) !important; }
            /* when sidebar exists, ensure main-panel is offset so content never sits underneath */
            #sidebar + .main-panel { margin-left: 18rem !important; }
        }
        @media (min-width: 1024px) {
            .topbar-shift { left: 16rem !important; width: calc(100% - 16rem) !important; }
            #sidebar + .main-panel { margin-left: 16rem !important; }
        }
        /* keep main-panel full-width on small screens */
        @media (max-width: 767px) {
            .main-panel { margin-left: 0 !important; }
            .topbar-shift { left: 0 !important; width: 100% !important; }
        }
    </style>

    <?= $this->renderSection('pageStyles') ?>

</head>
<body class="antialiased bg-slate-50 text-slate-900 min-h-screen">
<?php
    // Determine basic UI state (same variables used by legacy layout)
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
        <nav class="w-full fixed top-4 left-0 z-40 px-4 topbar-shift">
            <div class="max-w-7xl mx-auto bg-white/80 backdrop-blur-md rounded-2xl px-4 py-3 shadow-md flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a class="text-xl font-semibold text-slate-900 flex items-center gap-3" href="<?= base_url('/dashboard') ?>">
                        <?= svg_icon('home', 'w-6 h-6 text-indigo-600') ?>
                        <span>LIGTAS</span>
                    </a>
                    <p class="hidden md:block text-sm text-slate-500 mt-0.5">Local Incident Gathering &amp; Tracking</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?= base_url('/logout') ?>" title="Logout" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-600">
                        <?= svg_icon('logout', 'w-5 h-5') ?>
                        <span class="sr-only">Logout</span>
                    </a>
                </div>


            </div>
        </nav>
    <?php endif; ?>

    <div class="container-fluid <?= $pageBodyClass ?>">
        <?php if (!$hideSidebar): ?>
            <nav id="sidebar" class="fixed left-0 top-0 z-30 w-72 lg:w-64 h-screen flex-shrink-0 flex flex-col bg-white border-r border-slate-100 p-4 <?= $hideNavbar ? '' : 'pt-20' ?>">
                <!-- Brand -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">L</div>
                    <a href="<?= base_url('/dashboard') ?>" class="text-lg font-semibold text-slate-900">LIGTAS</a>
                    <button class="ml-auto inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:bg-slate-50" aria-label="Toggle sidebar"><?= svg_icon('menu', 'w-5 h-5') ?></button>
                </div>

                <!-- Navigation -->
                <div class="flex-1 mt-2 overflow-y-auto">
                    <div class="text-xs text-slate-400 uppercase tracking-wide mb-3">Menu</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= ($root === '' || $root === 'dashboard') ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>" <?= ($root === '' || $root === 'dashboard') ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('home', 'w-5 h-5') ?>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= base_url('/incident-report') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'incident-report' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>" <?= $root === 'incident-report' ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('files', 'w-5 h-5') ?>
                                <span class="menu-title">Incident Report</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= base_url('/ordinance') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'ordinance' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>" <?= $root === 'ordinance' ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('file', 'w-5 h-5') ?>
                                <span class="menu-title">Documents</span>
                            </a>
                        </li>

                        <?php if (session()->get('is_admin')): ?>
                        <li>
                            <a href="<?= base_url('/admin-panel') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'admin-panel' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>" <?= $root === 'admin-panel' ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('users', 'w-5 h-5') ?>
                                <span class="menu-title">Admin Panel</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Footer / Profile -->
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3 text-sm">
                    <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-semibold"><?= esc($initials) ?></div>
                    <div>
                        <div class="font-semibold text-sm text-slate-900"><?= esc(trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: ($username ?? 'User')) ?></div>
                        <div class="text-xs text-slate-400"><?= esc(session()->get('role_name') ?? 'User') ?></div>
                    </div>
                    <a href="<?= base_url('/user-profile') ?>" class="ml-auto text-slate-400 hover:text-slate-600" title="Edit Profile"><?= svg_icon('pencil', 'w-4 h-4') ?></a>
                </div>
            </nav>
        <?php endif; ?>

        <div class="<?= $mainPanelClass ?> <?= $hideSidebar ? '' : 'ml-72 lg:ml-64' ?> h-screen overflow-y-auto <?= $hideNavbar ? '' : 'pt-20' ?>">
            <div class="content-wrapper">
                <?= $this->renderSection('content') ?>


            </div>
        </div>
    </div>
</div>

<?= $this->renderSection('pageScripts') ?>
</body>
</html>