<?php
$title = "All Events – EMS";
ob_start();
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar3 me-2"></i>All Events</h1>
        <p class="page-subtitle">Manage and track all event bookings</p>
    </div>
    <a href="index.php?page=events&action=add" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Event
    </a>
</div>

<!-- Filters -->
<div class="card-panel mb-4">
    <form method="GET" action="index.php" class="row g-3">
        <input type="hidden" name="page" value="events">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search name, client, venue..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
            </div>
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <?php foreach ($event_types as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo (isset($etype) && $etype == $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from ?? ''); ?>">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to ?? ''); ?>">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-purple w-100">Filter</button>
            <a href="index.php?page=events" class="btn btn-light"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<div class="card-panel">
    <?php if (!empty($events)): ?>
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
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
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
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="index.php?page=events&action=view&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-ghost" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="index.php?page=events&action=edit&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-ghost" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (is_admin()): ?>
                            <button type="button" class="btn btn-sm btn-ghost text-danger" title="Delete" onclick="confirmDelete(<?php echo $event['id']; ?>, '<?php echo addslashes($event['name']); ?>')">
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
        <i class="bi bi-calendar-x"></i>
        <p>No events found for the search criteria.</p>
        <a href="index.php?page=events&action=add" class="btn btn-primary btn-sm">Add New Event</a>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete event "<span id="deleteEventName"></span>"?</p>
                <p class="text-danger small"><strong>Warning:</strong> This will also return all allocated inventory back to stock.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Event</a>
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
        document.getElementById('deleteEventName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = 'index.php?page=events&action=delete&id=' + id;
        deleteModal.show();
    }
</script>
<?php
$scripts = ob_get_clean();
include 'templates/base.php';
?>
