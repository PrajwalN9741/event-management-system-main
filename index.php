<?php
// index.php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
load_env();

// Support for built-in PHP server (serves static files correctly)
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
        return false;
    }
}

session_start();

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Simple Router
if (!is_logged_in() && !in_array($page, ['login', 'register'])) {
    header('Location: index.php?page=login');
    exit;
}

// Map pages to files
$pages = [
    'dashboard' => 'pages/dashboard.php',
    'events'    => 'pages/events.php',
    'inventory' => 'pages/inventory.php',
    'quotations'=> 'pages/quotations.php',
    'login'     => 'pages/auth.php',
    'logout'    => 'pages/auth.php',
    'register'  => 'pages/auth.php',
    'users'     => 'pages/auth.php',
];

if (isset($pages[$page]) && file_exists($pages[$page])) {
    require_once $pages[$page];
} else {
    echo "404 - Page Not Found";
}
?>
