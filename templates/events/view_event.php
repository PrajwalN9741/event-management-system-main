<?php
$title = htmlspecialchars($event['name']) . " – EMS";
ob_start();

$flowers = json_decode($event['flower_items_json'] ?: '[]', true);
$flower_total = 0;
foreach ($flowers as $f) $flower_total += (float)$f['price'] * (int)$f['qty'];

$inventory_total = 0;
foreach ($inventory_usages as $ui) $inventory_total += $ui['quantity_used'] * $ui['price_per_unit'];

$grand_total = $flower_total + $inventory_total;
?>
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php?page=events">Events</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($event['name']); ?></li>
            </ol>
        </nav>
        <h1 class="page-title"><?php echo htmlspecialchars($event['name']); ?></h1>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?page=events&action=edit&id=<?php echo $event['id']; ?>" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-2"></i>Edit Event
        </a>
        <a href="index.php?page=quotations&action=generate&event_id=<?php echo $event['id']; ?>" class="btn btn-purple">
            <i class="bi bi-file-pdf me-2"></i>Generate Quotation
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Event Info -->
        <div class="card-panel mb-4">
            <h5 class="panel-title mb-4">Event Details</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Type</label>
                    <span class="badge badge-type"><?php echo htmlspecialchars($event['event_type']); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Status</label>
                    <span class="badge <?php echo ($event['status'] == 'confirmed') ? 'bg-success' : (($event['status'] == 'pending') ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                        <?php echo htmlspecialchars($event['status']); ?>
                    </span>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Date & Time</label>
                    <p class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i><?php echo date('d M Y', strtotime($event['event_date'])); ?>
                        <?php if ($event['event_time']): ?>
                        <i class="bi bi-clock ms-3 me-2"></i><?php echo date('H:i', strtotime($event['event_time'])); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Venue</label>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($event['venue']); ?></p>
                </div>
            </div>
            <?php if ($event['notes']): ?>
            <div class="mt-4">
                <label class="text-muted small d-block">Notes</label>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($event['notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Cost Breakdown -->
        <div class="card-panel">
            <h5 class="panel-title mb-4">Quotation Breakdown</h5>
            
            <?php if (!empty($flowers)): ?>
            <h6 class="text-muted mb-3"><i class="bi bi-flower1 me-2"></i>Flowers & Decorations</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item Type</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flowers as $f): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($f['type']); ?></td>
                            <td class="text-center"><?php echo $f['qty']; ?></td>
                            <td class="text-end">₹<?php echo number_format($f['price'], 2); ?></td>
                            <td class="text-end">₹<?php echo number_format($f['price'] * $f['qty'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-light">
                            <td colspan="3"><strong>Flower Total</strong></td>
                            <td class="text-end"><strong>₹<?php echo number_format($flower_total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (!empty($inventory_usages)): ?>
            <h6 class="text-muted mb-3"><i class="bi bi-box-seam me-2"></i>Inventory Items</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th class="text-center">Qty Used</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory_usages as $ui): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ui['name']); ?> <small class="text-muted">(<?php echo htmlspecialchars($ui['unit']); ?>)</small></td>
                            <td class="text-center"><?php echo $ui['quantity_used']; ?></td>
                            <td class="text-end">₹<?php echo number_format($ui['price_per_unit'], 2); ?></td>
                            <td class="text-end">₹<?php echo number_format($ui['quantity_used'] * $ui['price_per_unit'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-light">
                            <td colspan="3"><strong>Inventory Total</strong></td>
                            <td class="text-end"><strong>₹<?php echo number_format($inventory_total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="grand-total-banner d-flex justify-content-between align-items-center mt-4 p-3 bg-purple text-white rounded">
                <h4 class="mb-0">Estimated Grand Total</h4>
                <h4 class="mb-0">₹<?php echo number_format($grand_total, 2); ?></h4>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Client Info -->
        <div class="card-panel mb-4">
            <h5 class="panel-title mb-4">Client Information</h5>
            <div class="d-flex align-items-center mb-3">
                <div class="avatar-circle me-3"><?php echo strtoupper(substr($event['client_name'], 0, 1)); ?></div>
                <div>
                    <h6 class="mb-0"><?php echo htmlspecialchars($event['client_name']); ?></h6>
                    <small class="text-muted">Primary Contact</small>
                </div>
            </div>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i><?php echo htmlspecialchars($event['client_email'] ?: 'N/A'); ?></li>
                <li class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><?php echo htmlspecialchars($event['client_phone'] ?: 'N/A'); ?></li>
            </ul>
        </div>

        <!-- Metadata -->
        <div class="card-panel">
            <h5 class="panel-title mb-3">System Info</h5>
            <div class="mb-3">
                <label class="text-muted small d-block">Created By</label>
                <p class="mb-0"><i class="bi bi-person me-2"></i><?php echo htmlspecialchars($event['creator_name']); ?></p>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Created At</label>
                <p class="mb-0 small"><?php echo date('d M Y, H:i', strtotime($event['created_at'])); ?></p>
            </div>
            <div>
                <label class="text-muted small d-block">Last Updated</label>
                <p class="mb-0 small"><?php echo date('d M Y, H:i', strtotime($event['updated_at'])); ?></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'templates/base.php';
?>
