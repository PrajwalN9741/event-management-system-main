<?php
// pages/dashboard.php
require_once 'config/db.php';
require_once 'includes/auth.php';

require_login();

$today = date('Y-m-d');

// Upcoming events (next 10)
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_date >= ? ORDER BY event_date ASC LIMIT 10");
$stmt->execute([$today]);
$upcoming_events = $stmt->fetchAll();

// Today's events
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_date = ?");
$stmt->execute([$today]);
$todays_events = $stmt->fetchAll();

// Total events this month
$month = date('Y-m');
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE strftime('%Y-%m', event_date) = ?");
$stmt->execute([$month]);
$total_this_month = $stmt->fetchColumn();

// Low stock items
$stmt = $pdo->query("SELECT * FROM inventory_items WHERE quantity <= low_stock_threshold");
$low_stock = $stmt->fetchAll();

// Total inventory count
$total_inventory = $pdo->query("SELECT COUNT(*) FROM inventory_items")->fetchColumn();

// Revenue estimate
$stmt = $pdo->query("SELECT e.id, e.flower_items_json, 
    (SELECT SUM(ei.quantity_used * ii.price_per_unit) 
     FROM event_inventory ei 
     JOIN inventory_items ii ON ei.item_id = ii.id 
     WHERE ei.event_id = e.id) as inventory_total
    FROM events e");
$all_events = $stmt->fetchAll();
$total_revenue = 0;
foreach ($all_events as $e) {
    $flowers = json_decode($e['flower_items_json'] ?: '[]', true);
    $flower_total = 0;
    foreach ($flowers as $f) {
        $flower_total += (float)($f['price'] ?? 0) * (int)($f['qty'] ?? 0);
    }
    $total_revenue += $flower_total + (float)$e['inventory_total'];
}

// Recent quotations
$stmt = $pdo->query("SELECT q.*, e.name as event_name FROM quotations q JOIN events e ON q.event_id = e.id ORDER BY generated_at DESC LIMIT 5");
$recent_quotations = $stmt->fetchAll();

include 'templates/dashboard.php';
?>
