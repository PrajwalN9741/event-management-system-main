<?php
// pages/quotations.php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/pdf_utils.php';
require_once 'includes/email_utils.php';

require_login();

$quotations_dir = 'static/quotations';
if (!file_exists($quotations_dir)) mkdir($quotations_dir, 0777, true);

if ($action === 'generate' || $action === 'view') {
    $event_id = $_GET['event_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    if (!$event) die("Event not found.");

    // Load inventory
    $stmt = $pdo->prepare("SELECT ei.*, ii.name, ii.unit, ii.price_per_unit FROM event_inventory ei JOIN inventory_items ii ON ei.item_id = ii.id WHERE ei.event_id = ?");
    $stmt->execute([$event_id]);
    $inventory_usages = $stmt->fetchAll();

    $pdf_path = "$quotations_dir/quotation_event_{$event_id}.pdf";
    
    if ($action === 'generate') {
        generate_quotation_pdf($event, $inventory_usages, $pdf_path);
        
        // Update or Insert Quotation record
        $stmt = $pdo->prepare("SELECT id FROM quotations WHERE event_id = ?");
        $stmt->execute([$event_id]);
        $quotation_id = $stmt->fetchColumn();
        
        $grand_total = 0; // Calculate it properly
        $flowers = json_decode($event['flower_items_json'] ?: '[]', true);
        foreach ($flowers as $f) $grand_total += (float)$f['price'] * (int)$f['qty'];
        foreach ($inventory_usages as $ui) $grand_total += $ui['quantity_used'] * $ui['price_per_unit'];

        if ($quotation_id) {
            $stmt = $pdo->prepare("UPDATE quotations SET pdf_path=?, total_amount=?, generated_at=CURRENT_TIMESTAMP WHERE id=?");
            $stmt->execute([$pdf_path, $grand_total, $quotation_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO quotations (event_id, pdf_path, total_amount) VALUES (?, ?, ?)");
            $stmt->execute([$event_id, $pdf_path, $grand_total]);
        }
        set_flash_message("Quotation generated successfully.", "success");
    }

    $stmt = $pdo->prepare("SELECT * FROM quotations WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $quotation = $stmt->fetch();

    include 'templates/quotation/quotation.php';
}

if ($action === 'email') {
    $event_id = $_GET['event_id'] ?? 0;
    $recipient = $_POST['recipient_email'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    if (!$event) die("Event not found.");

    if (!$recipient) $recipient = $event['client_email'];
    if (!$recipient) {
        set_flash_message("No recipient email provided.", "error");
        header("Location: index.php?page=quotations&action=view&event_id=$event_id");
        exit;
    }

    $pdf_path = "$quotations_dir/quotation_event_{$event_id}.pdf";
    if (!file_exists($pdf_path)) {
        // Auto-generate if missing
        $stmt = $pdo->prepare("SELECT ei.*, ii.name, ii.unit, ii.price_per_unit FROM event_inventory ei JOIN inventory_items ii ON ei.item_id = ii.id WHERE ei.event_id = ?");
        $stmt->execute([$event_id]);
        $inventory_usages = $stmt->fetchAll();
        generate_quotation_pdf($event, $inventory_usages, $pdf_path);
    }

    $subject = "Quotation for " . $event['name'];
    $body = "Dear {$event['client_name']}, <br><br> Please find attached the quotation for your event '{$event['name']}'.";
    
    list($ok, $err) = send_email($recipient, $subject, $body, [$pdf_path => "Quotation_{$event['name']}.pdf"]);
    
    if ($ok) {
        set_flash_message("Quotation emailed to $recipient.", "success");
    } else {
        set_flash_message("Failed to send email: $err", "error");
    }
    header("Location: index.php?page=quotations&action=view&event_id=$event_id");
    exit;
}
?>
