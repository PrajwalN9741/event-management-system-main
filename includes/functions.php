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
?>
