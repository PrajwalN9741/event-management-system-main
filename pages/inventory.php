<?php
// pages/inventory.php
require_once 'config/db.php';
require_once 'includes/auth.php';

require_login();

$categories = ['Decoration', 'Furniture', 'Audio/Visual', 'Catering', 'Floral', 'Lighting', 'Other'];
$units = ['pcs', 'kg', 'litre', 'meter', 'box', 'set', 'pair', 'hour'];

if ($action === 'index') {
    $q = $_GET['q'] ?? '';
    $cat = $_GET['category'] ?? '';

    $query = "SELECT * FROM inventory_items WHERE 1=1";
    $params = [];
    if ($q) {
        $query .= " AND name LIKE ?";
        $params[] = "%$q%";
    }
    if ($cat) {
        $query .= " AND category = ?";
        $params[] = $cat;
    }
    $query .= " ORDER BY name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    include 'templates/inventory/inventory.php';
}

if ($action === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $quantity = (float)($_POST['quantity'] ?? 0);
        $unit = $_POST['unit'] ?? 'pcs';
        $price = (float)($_POST['price_per_unit'] ?? 0);
        $threshold = (float)($_POST['low_stock_threshold'] ?? 10);

        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO inventory_items (name, category, quantity, unit, price_per_unit, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $quantity, $unit, $price, $threshold]);
            set_flash_message("Item '$name' added to inventory.", "success");
        } else {
            set_flash_message("Item name is required.", "error");
        }
    }
    header("Location: index.php?page=inventory");
    exit;
}

if ($action === 'edit') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM inventory_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) die("Item not found.");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? $item['name'];
        $category = $_POST['category'] ?? $item['category'];
        $quantity = (float)($_POST['quantity'] ?? $item['quantity']);
        $unit = $_POST['unit'] ?? $item['unit'];
        $price = (float)($_POST['price_per_unit'] ?? $item['price_per_unit']);
        $threshold = (float)($_POST['low_stock_threshold'] ?? $item['low_stock_threshold']);

        $stmt = $pdo->prepare("UPDATE inventory_items SET name=?, category=?, quantity=?, unit=?, price_per_unit=?, low_stock_threshold=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
        $stmt->execute([$name, $category, $quantity, $unit, $price, $threshold, $id]);
        set_flash_message("Item '$name' updated.", "success");
        header("Location: index.php?page=inventory");
        exit;
    }
    include 'templates/inventory/edit_item.php';
}

if ($action === 'delete') {
    require_role('admin');
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM inventory_items WHERE id = ?");
    $stmt->execute([$id]);
    set_flash_message("Item deleted.", "success");
    header("Location: index.php?page=inventory");
    exit;
}
?>
