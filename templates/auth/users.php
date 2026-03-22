<?php
$title = "User Management – EMS";
ob_start();
?>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">User Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <a href="index.php?page=auth&action=register" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add New User</span>
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    <?php echo strtoupper($user['username'][0]); ?>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="text-muted small">ID: #<?php echo $user['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-danger-subtle text-danger px-3 py-2">Admin</span>
                            <?php else: ?>
                            <span class="badge bg-info-subtle text-info px-3 py-2">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (get_current_user_id() != $user['id']): ?>
                                <form method="POST" action="index.php?page=auth&action=delete&id=<?php echo $user['id']; ?>" 
                                      onsubmit="return confirm('Are you sure you want to delete user <?php echo addslashes($user['username']); ?>?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">Current User</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1); }
</style>
<?php
$content = ob_get_clean();
include 'templates/base.php';
?>
