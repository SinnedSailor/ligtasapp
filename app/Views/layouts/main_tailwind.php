<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'LIGTAS') ?></title>

    <!-- Tailwind compiled -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">

    <style>
        @media (min-width: 768px) {
            .topbar-shift { left: 18rem !important; width: calc(100% - 18rem) !important; }

            #sidebar + .main-panel {
                margin-left: 18rem !important;
                width: calc(100% - 18rem) !important;
            }
        }
    
        .topbar-shift,
        .main-panel {
            transition: left .3s ease, width .3s ease, margin-left .3s ease;
        }
        @media (min-width: 1024px) {
            .topbar-shift { left: 16rem !important; width: calc(100% - 16rem) !important; }
            #sidebar + .main-panel {
                margin-left: 16rem !important;
                width: calc(100% - 16rem) !important;
            }
        }
        @media (max-width: 767px) {
            .main-panel { margin-left: 0 !important; width: 100% !important; }
            .topbar-shift { left: 0 !important; width: 100% !important; }
        }

    
        .topbar-shift,
        .main-panel {
            transition: left .3s ease, width .3s ease, margin-left .3s ease;
        }

        /* collapsed sidebar state only changes sidebar width */
        #sidebar.sidebar-collapsed { width: 5rem !important; }
        #sidebar.sidebar-collapsed .menu-title { display: none; }
        #sidebar.sidebar-collapsed .text-xs.uppercase { display: none; } /* hide "Menu" header */
        #sidebar.sidebar-collapsed .flex.items-center>div:not(.w-16) { display: none; } /* hide profile text */

        /* ensure horizontal scrolling wrappers reserve space for the vertical scrollbar */
        .overflow-x-auto {
            scrollbar-gutter: stable;
        }

        /* when the sidebar is collapsed we also need to shrink the shifted topbar/main-panel */
        body.sidebar-collapsed-main .topbar-shift {
            left: 5rem !important;
            width: calc(100% - 5rem) !important;
        }
        body.sidebar-collapsed-main #sidebar + .main-panel {
            margin-left: 5rem !important;
            width: calc(100% - 5rem) !important;
        }

        /* sidebar link */
        #sidebar a,
        #sidebar a svg,
        #sidebar a .menu-title {
            transition: color .2s;
        }
        /* hover*/
        #sidebar a:hover,
        #sidebar a:hover svg,
        #sidebar a:hover .menu-title {
            color: #002C76 !important;
        }
        #sidebar a:hover {
            background-color: rgba(0,44,118,0.1) !important;
        }
        /* active link  */
        #sidebar a[aria-current="page"],
        #sidebar a[aria-current="page"] svg,
        #sidebar a[aria-current="page"] .menu-title {
                color: #fff !important;
            }
            #sidebar a[aria-current="page"] {
                background-color: #002c76 !important;
            }
            #sidebar a[aria-current="page"] .menu-title {
                background-color: #002c76 !important;
                color: #fff !important;
                border-radius: 0.375rem;
                padding: 0.25rem 0.5rem;
            }
        
    </style>

    <?= $this->renderSection('pageStyles') ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="antialiased bg-slate-50 text-slate-900 min-h-screen">
<?php
    //Basic variables for layout and navbar/sidebar state
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

    <!-- header -->
<div class="container-scroller">
    <?php if (!$hideNavbar): ?>
        <nav class="w-full fixed top-4 left-0 z-40 px-4 sm:px-6 lg:px-8 topbar-shift">
            <div class="max-w-7xl mx-auto bg-white/80 backdrop-blur-md rounded-2xl px-4 sm:px-6 lg:px-8 py-3 shadow-md flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a class="text-xl font-semibold text-slate-900 flex items-center gap-3" href="<?= base_url('/dashboard') ?>">
                        <?= svg_icon('home', 'w-6 h-6 text-indigo-600') ?>
                        <span>LIGTAS</span>
                    </a>
                    <p class="hidden md:block text-sm text-slate-500 mt-0.5">Local Incident Gathering &amp; Tracking for Aquatic Safety</p>
                </div>

                <div class="flex items-center gap-1">
                    <a href="<?= base_url('/logout') ?>" title="Logout" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-600">
                        <?= svg_icon('logout', 'w-5 h-5') ?>
                        <span class="sr-only">Logout</span>
                    </a>
                    <span class="ml-2 text-slate-500 text-sm font-normal flex items-center">Logout</span>
                </div>


            </div>
        </nav>
    <?php endif; ?>

    <div class="container-fluid px-0 <?= $pageBodyClass ?>">
        <?php if (!$hideSidebar): ?>
            <nav id="sidebar" class="fixed left-0 top-0 z-30 w-72 lg:w-64 h-screen flex-shrink-0 flex flex-col bg-white border-r border-slate-100 p-4 <?= $hideNavbar ? '' : 'pt-16' ?>">
                <!-- Brand -->
                <div class="flex items-center gap-3 px-2 py-3 mb-4">
                <div class="flex items-center gap-4 pt-6 pb-4 mb-4 w-full">
                    <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center overflow-hidden">
                        <img src="<?= base_url('assets/images/ligtas.png') ?>" alt="Logo" class="w-14 h-14 object-cover rounded-full mx-auto my-auto" />
                    </div>
                </div>
                    <button class="ml-auto inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:bg-slate-50" aria-label="Toggle sidebar"><?= svg_icon('menu', 'w-5 h-5') ?></button>
                </div>

                <!-- Navigation -->
                <div class="flex-1 mt-2 overflow-y-auto">
                    <div class="text-xs text-slate-400 uppercase tracking-wide mb-3">Menu</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= ($root === '' || $root === 'dashboard') ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' ?>" <?= ($root === '' || $root === 'dashboard') ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('home', 'w-5 h-5') ?>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= base_url('/incident-report') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'incident-report' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' ?>" <?= $root === 'incident-report' ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('files', 'w-5 h-5') ?>
                                <span class="menu-title">Incident Report</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= base_url('/documents') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'documents' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' ?>" <?= $root === 'documents' ? 'aria-current="page"' : '' ?>>
                                <?= svg_icon('file', 'w-5 h-5') ?>
                                <span class="menu-title">Documents</span>
                            </a>
                        </li>

                        <?php if (session()->get('is_admin')): ?>
                        <li>
                            <a href="<?= base_url('/admin-panel') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $root === 'admin-panel' ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' ?>" <?= $root === 'admin-panel' ? 'aria-current="page"' : '' ?> >
                                <?= svg_icon('users', 'w-5 h-5') ?>
                                <span class="menu-title">Admin Panel</span>
                            </a>
                        </li>
                        <li>
                            <?php
                                $isBackupActive = strpos($path, 'admin/backup') === 0;
                            ?>
                            <a href="<?= base_url('/admin/backup') ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md <?= $isBackupActive ? 'bg-slate-100 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' ?>" <?= $isBackupActive ? 'aria-current="page"' : '' ?> >
                                <?= svg_icon('archive', 'w-5 h-5') ?>
                                <span class="menu-title">Backup &amp; Restore</span>
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

        <div class="<?= $mainPanelClass ?> h-screen overflow-y-scroll <?= $hideNavbar ? '' : 'pt-20' ?>">
            <div class="content-wrapper">
                <?= $this->renderSection('content') ?>


            </div>
        </div>
    </div>
</div>

<?= $this->renderSection('pageScripts') ?>
<?php include(APPPATH . 'Views/layouts/sweetalert_script.php'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
 
    if (typeof Swal !== 'undefined' && Swal.setDefaults) {
        Swal.setDefaults({
            confirmButtonColor: '#002c76',
            cancelButtonColor: '#9db4dd',
            zIndex: 20000
        });
    }

    var logoutBtn = document.querySelector('a[title="Logout"]');

    // sidebar collapse toggle
    var sidebar = document.getElementById('sidebar');
    var toggleBtn = sidebar ? sidebar.querySelector('button[aria-label="Toggle sidebar"]') : null;
    function setCollapsed(collapsed) {
        if (collapsed) {
            sidebar.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-collapsed-main');
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            document.body.classList.remove('sidebar-collapsed-main');
        }
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }
    if (sidebar && toggleBtn) {
        // restore state
        var stored = localStorage.getItem('sidebarCollapsed');
        if (stored === '1') {
            setCollapsed(true);
        }
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            setCollapsed(!isCollapsed);
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to logout?',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#9db4dd',
                icon: undefined,
                confirmButtonColor: '#002c76',
                showClass: { popup: 'swal2-noanimation' },
                hideClass: { popup: '' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = logoutBtn.href;
                }
            });
        });
    }
});
</script>
</body>
</html>