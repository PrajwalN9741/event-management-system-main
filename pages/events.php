<?php
// pages/events.php
require_once 'config/db.php';
require_once 'includes/auth.php';

require_login();

$event_types = ['Wedding', 'Corporate', 'Birthday', 'Anniversary', 'Engagement', 'Other'];

if ($action === 'index') {
    $q = $_GET['q'] ?? '';
    $etype = $_GET['type'] ?? '';
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';

    $query = "SELECT * FROM events WHERE 1=1";
    $params = [];

    if ($q) {
        $query .= " AND (name LIKE ? OR client_name LIKE ? OR venue LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }
    if ($etype) {
        $query .= " AND event_type = ?";
        $params[] = $etype;
    }
    if ($date_from) {
        $query .= " AND event_date >= ?";
        $params[] = $date_from;
    }
    if ($date_to) {
        $query .= " AND event_date <= ?";
        $params[] = $date_to;
    }

    $query .= " ORDER BY event_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll();

    include 'templates/events/view_events.php';
}

if ($action === 'view') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT e.*, u.username as creator_name FROM events e JOIN users u ON e.created_by = u.id WHERE e.id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if (!$event) die("Event not found.");

    // Load inventory usages
    $stmt = $pdo->prepare("SELECT ei.*, ii.name, ii.unit, ii.price_per_unit FROM event_inventory ei JOIN inventory_items ii ON ei.item_id = ii.id WHERE ei.event_id = ?");
    $stmt->execute([$id]);
    $inventory_usages = $stmt->fetchAll();

    include 'templates/events/view_event.php';
}

if ($action === 'add' || $action === 'edit') {
    $id = $_GET['id'] ?? 0;
    $event = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch();
        if (!$event) die("Event not found.");
        
        // Load current inventory usages for edit
        $stmt = $pdo->prepare("SELECT * FROM event_inventory WHERE event_id = ?");
        $stmt->execute([$id]);
        $current_usages = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [item_id => quantity_used]
    }

    $inventory_items = $pdo->query("SELECT * FROM inventory_items ORDER BY name ASC")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $event_type = $_POST['event_type'] ?? '';
        $event_date = $_POST['event_date'] ?? '';
        $event_time = $_POST['event_time'] ?: null;
        $venue = $_POST['venue'] ?? '';
        $client_name = $_POST['client_name'] ?? '';
        $client_email = $_POST['client_email'] ?? '';
        $client_phone = $_POST['client_phone'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'] ?? 'confirmed';

        // Parse flowers
        $flowers = [];
        if (isset($_POST['flower_type'])) {
            foreach ($_POST['flower_type'] as $i => $type) {
                if ($type) {
                    $flowers[] = [
                        'type' => $type,
                        'qty' => (int)$_POST['flower_qty'][$i],
                        'price' => (float)$_POST['flower_price'][$i]
                    ];
                }
            }
        }
        $flower_items_json = json_encode($flowers);

        $pdo->beginTransaction();
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO events (name, event_type, event_date, event_time, venue, client_name, client_email, client_phone, notes, status, flower_items_json, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $event_type, $event_date, $event_time, $venue, $client_name, $client_email, $client_phone, $notes, $status, $flower_items_json, get_current_user_id()]);
                $event_id = $pdo->lastInsertId();
            } else {
                // Restore old inventory
                $stmt = $pdo->prepare("SELECT * FROM event_inventory WHERE event_id = ?");
                $stmt->execute([$id]);
                $old_usages = $stmt->fetchAll();
                foreach ($old_usages as $usage) {
                    $pdo->prepare("UPDATE inventory_items SET quantity = quantity + ? WHERE id = ?")->execute([$usage['quantity_used'], $usage['item_id']]);
                }
                $pdo->prepare("DELETE FROM event_inventory WHERE event_id = ?")->execute([$id]);

                $stmt = $pdo->prepare("UPDATE events SET name=?, event_type=?, event_date=?, event_time=?, venue=?, client_name=?, client_email=?, client_phone=?, notes=?, status=?, flower_items_json=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
                $stmt->execute([$name, $event_type, $event_date, $event_time, $venue, $client_name, $client_email, $client_phone, $notes, $status, $flower_items_json, $id]);
                $event_id = $id;
            }

            // Apply new inventory
            if (isset($_POST['inv_item_id'])) {
                foreach ($_POST['inv_item_id'] as $i => $item_id) {
                    $qty = (float)$_POST['inv_quantity'][$i];
                    if ($qty > 0) {
                        $pdo->prepare("INSERT INTO event_inventory (event_id, item_id, quantity_used) VALUES (?, ?, ?)")->execute([$event_id, $item_id, $qty]);
                        $pdo->prepare("UPDATE inventory_items SET quantity = quantity - ? WHERE id = ?")->execute([$qty, $item_id]);
                    }
                }
            }

            $pdo->commit();
            set_flash_message("Event saved successfully.", "success");
            header("Location: index.php?page=events");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error saving event: " . $e->getMessage();
        }
    }

    include 'templates/events/add_event.php';
}

if ($action === 'delete') {
    require_role('admin');
    $id = $_GET['id'] ?? 0;
    
    $pdo->beginTransaction();
    try {
        // Restore inventory
        $stmt = $pdo->prepare("SELECT * FROM event_inventory WHERE event_id = ?");
        $stmt->execute([$id]);
        $usages = $stmt->fetchAll();
        foreach ($usages as $usage) {
            $pdo->prepare("UPDATE inventory_items SET quantity = quantity + ? WHERE id = ?")->execute([$usage['quantity_used'], $usage['item_id']]);
        }
        
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
        $pdo->commit();
        set_flash_message("Event deleted.", "success");
    } catch (Exception $e) {
        $pdo->rollBack();
        set_flash_message("Error deleting event: " . $e->getMessage(), "error");
    }
    header("Location: index.php?page=events");
    exit;
}
?>
