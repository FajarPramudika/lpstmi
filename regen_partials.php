<?php
// Bootstrap CI enough to use Wp_clone
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8000';
$_SERVER['REQUEST_URI'] = '/';

define('ENVIRONMENT', 'development');
chdir(__DIR__);
require_once 'index.php'; // This will start CI, but we want to intercept or just let it run. Wait, if we just include index.php, it will run the default controller (Pages::index) and render the homepage!
