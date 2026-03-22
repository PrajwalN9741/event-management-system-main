<?php
// config/db.php

$db_path = __DIR__ . '/../instance/database.db';

// Ensure instance directory exists
if (!file_exists(dirname($db_path))) {
    mkdir(dirname($db_path), 0777, true);
}

try {
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist
    $schema = file_get_contents(__DIR__ . '/../schema.sql');
    $pdo->exec($schema);
    
    // Auto-seed inventory if empty
    $check = $pdo->query("SELECT COUNT(*) FROM inventory_items")->fetchColumn();
    if ($check == 0 && file_exists(__DIR__ . '/../seed_inventory.sql')) {
        $seed = file_get_contents(__DIR__ . '/../seed_inventory.sql');
        $pdo->exec($seed);
    }
    
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
