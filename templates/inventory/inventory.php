<?php
$title = "Inventory Management – EMS";
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-box-seam me-2"></i>Inventory Management</h1>
        <p class="page-subtitle">Track and manage your equipment and supplies</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="bi bi-plus-circle me-2"></i>Add New Item
    </button>
</div>

<!-- Filters -->
<div class="card-panel mb-4">
    <form method="GET" action="index.php" class="row g-3">
        <input type="hidden" name="page" value="inventory">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search inventory items..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo (isset($cat) && $cat == $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-blue w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card-panel">
    <?php if (!empty($items)): ?>
    <div class="table-responsive">
        <table class="table table-hover ems-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Stock Level</th>
                    <th>Unit Price</th>
                    <th>Last Updated</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <?php $is_low = ($item['quantity'] <= $item['low_stock_threshold']); ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <?php if ($is_low): ?>
                        <span class="badge bg-danger ms-2" title="Low Stock"><i class="bi bi-exclamation-triangle"></i></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-type"><?php echo htmlspecialchars($item['category'] ?: 'Other'); ?></span></td>
                    <td>
                        <div class="<?php echo $is_low ? 'text-danger fw-bold' : ''; ?>">
                            <?php echo $item['quantity']; ?> <?php echo htmlspecialchars($item['unit']); ?>
                        </div>
                        <small class="text-muted">Threshold: <?php echo $item['low_stock_threshold']; ?></small>
                    </td>
                    <td>₹<?php echo number_format($item['price_per_unit'], 2); ?></td>
                    <td><small class="text-muted"><?php echo date('d M Y', strtotime($item['updated_at'])); ?></small></td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="index.php?page=inventory&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-ghost" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (is_admin()): ?>
                            <button type="button" class="btn btn-sm btn-ghost text-danger" title="Delete" onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-box-seam"></i>
        <p>No inventory items found.</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">Add First Item</button>
    </div>
    <?php endif; ?>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Inventory Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?page=inventory&action=add" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Round Table (5ft)">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <select name="unit" class="form-select">
                                <?php foreach ($units as $u): ?>
                                <option value="<?php echo $u; ?>"><?php echo $u; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Initial Quantity</label>
                            <input type="number" step="0.1" name="quantity" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price per Unit (₹)</label>
                            <input type="number" step="0.01" name="price_per_unit" class="form-control" value="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" step="0.1" name="low_stock_threshold" class="form-control" value="10">
                        <small class="form-text text-muted">You will be alerted when stock falls below this level.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deleteItemName"></span>"?</p>
                <p class="text-danger small">This action cannot be undone and may affect existing event quotations.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Item</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = 'index.php?page=inventory&action=delete&id=' + id;
        deleteModal.show();
    }
</script>
<?php
$scripts = ob_get_clean();
include 'templates/base.php';
?>
