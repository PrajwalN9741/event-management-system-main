<?php
$title = "Dashboard – EMS";
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard</h1>
        <p class="page-subtitle"><?php echo date('l, d F Y'); ?></p>
    </div>
    <a href="index.php?page=events&action=add" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Event
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?php echo count($upcoming_events); ?></div>
                <div class="stat-label">Upcoming Events</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
            <div class="stat-body">
                <div class="stat-value">₹<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?php echo count($low_stock); ?></div>
                <div class="stat-label">Low Stock Alerts</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?php echo $total_inventory; ?></div>
                <div class="stat-label">Inventory Items</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Upcoming Events -->
    <div class="col-lg-8">
        <div class="card-panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="panel-title"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h5>
                <a href="index.php?page=events" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <?php if (!empty($upcoming_events)): ?>
            <div class="table-responsive">
                <table class="table table-hover ems-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_events as $event): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($event['name']); ?></strong></td>
                            <td><span class="badge badge-type"><?php echo htmlspecialchars($event['event_type']); ?></span></td>
                            <td><i class="bi bi-calendar3 me-1 text-muted"></i><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                            <td class="text-truncate" style="max-width:150px"><?php echo htmlspecialchars($event['venue']); ?></td>
                            <td><?php echo htmlspecialchars($event['client_name']); ?></td>
                            <td>
                                <span class="badge <?php echo ($event['status'] == 'confirmed') ? 'bg-success' : (($event['status'] == 'pending') ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                    <?php echo htmlspecialchars($event['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=events&action=view&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-ghost">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>No upcoming events found.</p>
                <a href="index.php?page=events&action=add" class="btn btn-primary btn-sm">Add First Event</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right column -->
    <div class="col-lg-4">
        <!-- Today's Events -->
        <div class="card-panel mb-4">
            <h5 class="panel-title mb-3"><i class="bi bi-calendar-check me-2"></i>Today's Events</h5>
            <?php if (!empty($todays_events)): ?>
            <?php foreach ($todays_events as $ev): ?>
            <div class="today-event-item">
                <div class="d-flex justify-content-between">
                    <strong><?php echo htmlspecialchars($ev['name']); ?></strong>
                    <span class="badge badge-type"><?php echo htmlspecialchars($ev['event_type']); ?></span>
                </div>
                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($ev['venue']); ?></small>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p class="text-muted small text-center py-3">No events today</p>
            <?php endif; ?>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card-panel">
            <h5 class="panel-title mb-3"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Alerts</h5>
            <?php if (!empty($low_stock)): ?>
            <?php foreach ($low_stock as $item): ?>
            <div class="stock-alert-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <small class="text-muted d-block"><?php echo htmlspecialchars($item['category'] ?? 'Uncategorized'); ?></small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger"><?php echo $item['quantity']; ?> <?php echo htmlspecialchars($item['unit']); ?></span>
                        <small class="text-muted d-block">min: <?php echo $item['low_stock_threshold']; ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <a href="index.php?page=inventory" class="btn btn-outline-warning btn-sm w-100 mt-2">
                Manage Inventory
            </a>
            <?php else: ?>
            <div class="text-center py-3">
                <i class="bi bi-check-circle text-success fs-4"></i>
                <p class="text-muted small mt-1">All stock levels OK</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'templates/base.php';
?>
