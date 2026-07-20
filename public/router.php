<?php
// Router for PHP built-in development server
// This file handles all requests and routes them to index.php

$requested_file = __DIR__ . $_SERVER['REQUEST_URI'];

// Remove query string
$requested_file = preg_replace('/\?.*$/', '', $requested_file);

// If the requested file is a real file or directory (and it's not a route), serve it
if (is_file($requested_file) && file_exists($requested_file)) {
    return false; // Let PHP serve the file
}

if (is_dir($requested_file) && file_exists($requested_file)) {
    return false;
}

// Otherwise, route everything through index.php
require_once __DIR__ . '/index.php';
