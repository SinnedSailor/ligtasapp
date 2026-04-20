
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('/login', 'Auth::login');
$routes->post('/authenticate', 'Auth::authenticate');
$routes->get('/register', 'Auth::register');
$routes->post('/store-register', 'Auth::store_register');
$routes->get('/dashboard', 'Auth::dashboard');
$routes->get('/logout', 'Auth::logout');

// Navigation routes
$routes->get('/documents', 'Documents::index');
// legacy paths now redirect so URL updates (keeps nav highlighting correct)
$routes->get('/ordinance', static fn() => redirect()->to('/documents'));

$routes->get('/incident-report', 'Auth::incident_report');
$routes->post('/incident-report/import', 'IncidentReport::import');
$routes->post('/incident-report/create', 'IncidentReport::createIncident');
$routes->post('/incident-report/update/(:num)', 'IncidentReport::updateIncident/$1');
$routes->post('/incident-report/attachments/upload', 'IncidentReport::uploadAttachment');
$routes->post('/incident-report/attachments/upload-temp', 'IncidentReport::uploadTempAttachment');
$routes->post('/incident-report/attachments/remove-temp', 'IncidentReport::removeTempAttachment');
$routes->get('/incident-report/attachments/(:num)', 'IncidentReport::listAttachments/$1');
$routes->get('/incident-report/attachments/view/(:num)', 'IncidentReport::viewAttachment/$1');
$routes->get('/incident-report/attachments/preview/(:num)', 'IncidentReport::previewAttachment/$1');
$routes->get('/incident-report/attachments/download/(:num)', 'IncidentReport::downloadAttachment/$1');
$routes->post('/incident-report/approve/(:num)', 'IncidentReport::approve/$1');
$routes->post('/incident-report/reject/(:num)', 'IncidentReport::reject/$1');
$routes->get('/incident-report/generateReport', 'IncidentReport::generateReport');
$routes->get('/incident-report/chart-data', 'IncidentReport::chartData');
// legacy '/pops' route now redirects
$routes->get('/pops', static fn() => redirect()->to('/documents'));
$routes->get('/user-profile', 'Auth::user_profile');
$routes->post('/user-profile/update', 'Auth::update_profile');

// Document workflow routes
$routes->post('/documents/upload', 'Documents::upload');
$routes->post('/documents/approve/(:num)', 'Documents::approve/$1');
$routes->post('/documents/reject/(:num)', 'Documents::reject/$1');
$routes->get('/documents/download/(:num)', 'Documents::download/$1');
$routes->get('/documents/view/(:num)', 'Documents::view/$1');
$routes->get('/documents/health', 'Documents::healthCheck');

// Admin routes
$routes->get('/admin-panel', 'Admin::panel');
$routes->get('/admin/create-first-admin', 'Admin::createFirstAdmin');
$routes->post('/admin/store-first-admin', 'Admin::storeFirstAdmin');
$routes->get('/admin/users', 'Admin::users');
$routes->post('/admin/assignRole', 'Admin::assignRole');
$routes->post('/admin/clearRole', 'Admin::clearRole');
$routes->post('/admin/grantAdmin', 'Admin::grantAdmin');
$routes->post('/admin/revokeAdmin', 'Admin::revokeAdmin');
$routes->get('/admin/getUsers', 'Admin::getUsers');
$routes->get('/admin/getStats', 'Admin::getStats');
$routes->get('/admin/backup', 'Admin::backup');
$routes->get('/admin/backup/export', 'Admin::exportBackup');
$routes->post('/admin/backup/restore', 'Admin::restoreBackup');
$routes->get('/debug/repair-encryption/(:num)', 'Admin::repairEncryption/$1');
$routes->get('/debug/repair-encryption', 'Admin::repairEncryption');

