<?php
// includes/functions.php

function set_flash_message($message, $category = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['message' => $message, 'category' => $category];
}

function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

function url_for($page, $params = []) {
    $url = "index.php?page=$page";
    foreach ($params as $key => $value) {
        $url .= "&$key=" . urlencode($value);
    }
    return $url;
}

function asset($path) {
    return "static/" . ltrim($path, '/');
}

function load_env() {
    $file = __DIR__ . '/../.env';
    if (!file_exists($file)) return;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}
?>
