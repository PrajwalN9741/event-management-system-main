<?php
$is_edit = ($action === 'edit');
$title = ($is_edit ? 'Edit' : 'Add') . ' Event – EMS';
ob_start();

$flowers = $is_edit ? json_decode($event['flower_items_json'] ?: '[]', true) : [];
?>
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php?page=events">Events</a></li>
                <li class="breadcrumb-item active"><?php echo $is_edit ? 'Edit Event' : 'New Event'; ?></li>
            </ol>
        </nav>
        <h1 class="page-title"><?php echo $is_edit ? 'Edit Event' : 'Create New Event'; ?></h1>
    </div>
</div>

<form method="POST" id="eventForm">
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Basic Details -->
            <div class="card-panel mb-4">
                <h5 class="panel-title mb-4">Core Event Details</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Event Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" placeholder="e.g. Sharma Wedding">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Event Type <span class="text-danger">*</span></label>
                        <select name="event_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <?php foreach ($event_types as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo (isset($event) && $event['event_type'] == $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="event_date" class="form-control" required value="<?php echo htmlspecialchars($event['event_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Time</label>
                        <input type="time" name="event_time" class="form-control" value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Venue Address <span class="text-danger">*</span></label>
                        <input type="text" name="venue" class="form-control" required value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>" placeholder="Full venue details">
                    </div>
                </div>
            </div>

            <!-- Flowers & Decor -->
            <div class="card-panel mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="panel-title mb-0">Flowers & Decoration Items</h5>
                    <button type="button" class="btn btn-outline-purple btn-sm" onclick="addFlowerRow()">
                        <i class="bi bi-plus-lg me-1"></i>Add Item
                    </button>
                </div>
                
                <div id="flowerContainer">
                    <?php if (empty($flowers)): ?>
                    <div class="row g-2 mb-3 flower-row">
                        <div class="col-md-6">
                            <input type="text" name="flower_type[]" class="form-control" placeholder="Item/Service Name">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="flower_qty[]" class="form-control" placeholder="Qty">
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="flower_price[]" class="form-control" placeholder="Price">
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($flowers as $f): ?>
                    <div class="row g-2 mb-3 flower-row">
                        <div class="col-md-6">
                            <input type="text" name="flower_type[]" class="form-control" value="<?php echo htmlspecialchars($f['type']); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="flower_qty[]" class="form-control" value="<?php echo $f['qty']; ?>">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="flower_price[]" class="form-control" value="<?php echo $f['price']; ?>">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-ghost text-danger w-100" onclick="this.closest('.flower-row').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Inventory -->
            <div class="card-panel">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="panel-title mb-0">Inventory Requirements</h5>
                    <button type="button" class="btn btn-outline-blue btn-sm" onclick="addInventoryRow()">
                        <i class="bi bi-plus-lg me-1"></i>Allocate Stock
                    </button>
                </div>

                <div id="inventoryContainer">
                    <?php if ($is_edit && !empty($current_usages)): ?>
                    <?php foreach ($current_usages as $item_id => $qty): ?>
                    <div class="row g-2 mb-3 inventory-row">
                        <div class="col-md-8">
                            <select name="inv_item_id[]" class="form-select">
                                <option value="">Select Item</option>
                                <?php foreach ($inventory_items as $item): ?>
                                <option value="<?php echo $item['id']; ?>" <?php echo ($item['id'] == $item_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?> available)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.1" name="inv_quantity[]" class="form-control" value="<?php echo $qty; ?>">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-ghost text-danger w-100" onclick="this.closest('.inventory-row').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Client Details -->
            <div class="card-panel mb-4">
                <h5 class="panel-title mb-3">Client Details</h5>
                <div class="mb-3">
                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="client_name" class="form-control" required value="<?php echo htmlspecialchars($event['client_name'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="client_email" class="form-control" value="<?php echo htmlspecialchars($event['client_email'] ?? ''); ?>">
                </div>
                <div>
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="client_phone" class="form-control" value="<?php echo htmlspecialchars($event['client_phone'] ?? ''); ?>">
                </div>
            </div>

            <!-- Notes & Status -->
            <div class="card-panel mb-4">
                <h5 class="panel-title mb-3">Other Details</h5>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="confirmed" <?php echo (isset($event) && $event['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="pending" <?php echo (isset($event) && $event['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="cancelled" <?php echo (isset($event) && $event['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Internal Notes</label>
                    <textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($event['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-lg">
                <i class="bi bi-check-circle me-2"></i><?php echo $is_edit ? 'Update Event' : 'Create Event'; ?>
            </button>
            <a href="index.php?page=events" class="btn btn-light w-100 mt-2">Cancel</a>
        </div>
    </div>
</form>

<!-- Templates for dynamic rows -->
<template id="flowerRowTpl">
    <div class="row g-2 mb-3 flower-row">
        <div class="col-md-6">
            <input type="text" name="flower_type[]" class="form-control" placeholder="Item Name">
        </div>
        <div class="col-md-3">
            <input type="number" name="flower_qty[]" class="form-control" placeholder="Qty">
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" name="flower_price[]" class="form-control" placeholder="Price">
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-ghost text-danger w-100" onclick="this.closest('.flower-row').remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
</template>

<template id="inventoryRowTpl">
    <div class="row g-2 mb-3 inventory-row">
        <div class="col-md-8">
            <select name="inv_item_id[]" class="form-select">
                <option value="">Select Item</option>
                <?php foreach ($inventory_items as $item): ?>
                <option value="<?php echo $item['id']; ?>">
                    <?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?> available)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" step="0.1" name="inv_quantity[]" class="form-control" placeholder="Qty">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-ghost text-danger w-100" onclick="this.closest('.inventory-row').remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
</template>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
    function addFlowerRow() {
        const container = document.getElementById('flowerContainer');
        const tpl = document.getElementById('flowerRowTpl').content.cloneNode(true);
        container.appendChild(tpl);
    }
    
    function addInventoryRow() {
        const container = document.getElementById('inventoryContainer');
        const tpl = document.getElementById('inventoryRowTpl').content.cloneNode(true);
        container.appendChild(tpl);
    }
</script>
<?php
$scripts = ob_get_clean();
include 'templates/base.php';
?>
