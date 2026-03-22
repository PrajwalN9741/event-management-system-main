<?php
$title = "Edit Item – EMS";
ob_start();
?>
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php?page=inventory">Inventory</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($item['name']); ?></li>
            </ol>
        </nav>
        <h1 class="page-title">Edit Inventory Item</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card-panel">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($item['name']); ?>">
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c; ?>" <?php echo ($item['category'] == $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Unit</label>
                        <select name="unit" class="form-select">
                            <?php foreach ($units as $u): ?>
                            <option value="<?php echo $u; ?>" <?php echo ($item['unit'] == $u) ? 'selected' : ''; ?>><?php echo $u; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">In Stock Quantity</label>
                        <input type="number" step="0.1" name="quantity" class="form-control" value="<?php echo $item['quantity']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price per Unit (₹)</label>
                        <input type="number" step="0.01" name="price_per_unit" class="form-control" value="<?php echo $item['price_per_unit']; ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" step="0.1" name="low_stock_threshold" class="form-control" value="<?php echo $item['low_stock_threshold']; ?>">
                    <div class="form-text">Alert will trigger when stock level is at or below this value.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    <a href="index.php?page=inventory" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card-panel h-100">
            <h5 class="panel-title mb-4">Stock Statistics</h5>
            <div class="row g-4 text-center">
                <div class="col-6">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h3 class="mb-1"><?php echo $item['quantity']; ?></h3>
                        <small class="text-muted d-block">Current Stock</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h3 class="mb-1">₹<?php echo number_format($item['price_per_unit'], 2); ?></h3>
                        <small class="text-muted d-block">Price per <?php echo $item['unit']; ?></small>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h6 class="text-muted mb-3">Recent Usage Info</h6>
                <p class="small text-muted">Usage tracking is handled through the Events module. Add or edit an event to allocate this item to a booking.</p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'templates/base.php';
?>
