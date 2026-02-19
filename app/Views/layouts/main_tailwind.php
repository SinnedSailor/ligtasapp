<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'LIGTAS') ?></title>

    <!-- Tailwind (compiled) only -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">

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
        <nav class="w-full fixed top-4 left-0 z-40 px-4">
            <div class="max-w-7xl mx-auto bg-white/80 backdrop-blur-md rounded-2xl px-4 py-3 shadow-md flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a class="text-xl font-semibold text-slate-900 flex items-center gap-3" href="<?= base_url('/dashboard') ?>">
                        <?= svg_icon('home', 'w-6 h-6 text-indigo-600') ?>
                        <span>LIGTAS</span>
                    </a>
                    <p class="hidden md:block text-sm text-slate-500 mt-0.5">Local Incident Gathering &amp; Tracking</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative hidden md:block">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= svg_icon('search', 'w-4 h-4') ?></div>
                        <input type="search" placeholder="Search incidents, provinces..." class="pl-10 pr-3 py-2 w-64 rounded-full border border-slate-200 text-sm bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>

                    <button class="px-3 py-2 bg-indigo-600 text-white rounded-full text-sm hover:bg-indigo-700">+ Incident</button>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex items-center gap-2 text-sm text-slate-600 pr-3 border-r border-slate-100"><?= esc(session()->get('role_name') ?? '') ?></div>
                        <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-semibold"><?= esc($initials) ?></div>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container-fluid <?= $pageBodyClass ?>">
        <?php if (!$hideSidebar): ?>
            <nav id="sidebar" class="w-72 lg:w-64 h-screen flex flex-col bg-white border-r border-slate-100 p-4 <?= $hideNavbar ? '' : 'pt-20' ?>">
                <!-- Brand -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">L</div>
                    <a href="<?= base_url('/dashboard') ?>" class="text-lg font-semibold text-slate-900">LIGTAS</a>
                    <button class="ml-auto inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:bg-slate-50" aria-label="Toggle sidebar"><?= svg_icon('menu', 'w-5 h-5') ?></button>
                </div>

                <!-- Navigation -->
                <div class="flex-1 mt-2">
                    <ul class="space-y-1">
                        <li>
                            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= ($root === '' || $root === 'dashboard') ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <?= svg_icon('home', 'w-5 h-5') ?>
                                <span class="menu-title">Dashboard</span>
                                <span class="ml-auto text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full">01</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/incident-report') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'incident-report' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <?= svg_icon('files', 'w-5 h-5') ?>
                                <span class="menu-title">Incident Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/ordinance') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'ordinance' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <?= svg_icon('files', 'w-5 h-5') ?>
                                <span class="menu-title">Documents</span>
                            </a>
                        </li>
                        <?php if (session()->get('is_admin')): ?>
                        <li>
                            <a href="<?= base_url('/admin-panel') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'admin-panel' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' ?>">
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

        <div class="<?= $mainPanelClass ?>">
            <div class="content-wrapper">
                <?= $this->renderSection('content') ?>
            </div>

            <?php if (!$hideFooter): ?>
                <?= view('components/footer') ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->renderSection('pageScripts') ?>
</body>
</html>