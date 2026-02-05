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
$routes->get('/ordinance', 'Auth::ordinance');
$routes->get('/incident-report', 'Auth::incident_report');
$routes->get('/pops', 'Auth::pops');
$routes->get('/user-profile', 'Auth::user_profile');

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

