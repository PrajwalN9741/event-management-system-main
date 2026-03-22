<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $title ?? 'EMS – Event Management System'; ?></title>
  <meta name="description" content="Professional Event Management System – manage events, inventory, and quotations." />
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet" />
  <!-- PWA Manifest -->
  <link rel="manifest" href="<?php echo asset('manifest.json'); ?>">
  <meta name="theme-color" content="#6c3ff3">
  <!-- PWA Apple Icons -->
  <link rel="apple-touch-icon" href="<?php echo asset('images/icons/icon-192x192.png'); ?>">
  <?php echo $head ?? ''; ?>
</head>
<body>

  <?php if (is_logged_in()): ?>

  <!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
  <nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-brand" style="font-size:1.1rem;">
      <i class="bi bi-stars me-2"></i>MNNMP Events
    </div>
    <ul class="sidebar-nav flex-grow-1">
      <li>
        <a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
          <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
      </li>
      <li class="sidebar-section">Events</li>
      <li>
        <a href="index.php?page=events" class="<?php echo ($page === 'events') ? 'active' : ''; ?>">
          <i class="bi bi-calendar3"></i> All Events
        </a>
      </li>
      <li>
        <a href="index.php?page=events&action=add">
          <i class="bi bi-plus-circle"></i> Add Event
        </a>
      </li>
      <li class="sidebar-section">Operations</li>
      <li>
        <a href="index.php?page=inventory" class="<?php echo ($page === 'inventory') ? 'active' : ''; ?>">
          <i class="bi bi-box-seam"></i> Inventory
        </a>
      </li>
      <li class="sidebar-section">Account</li>
      <?php if (is_admin()): ?>
      <li>
        <a href="index.php?page=users" class="<?php echo ($page === 'users') ? 'active' : ''; ?>">
          <i class="bi bi-people"></i> Users
        </a>
      </li>
      <li>
        <a href="index.php?page=register" class="<?php echo ($page === 'register') ? 'active' : ''; ?>">
          <i class="bi bi-person-plus"></i> Add User
        </a>
      </li>
      <?php endif; ?>
      <li>
        <a href="index.php?page=logout">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </li>
    </ul>
    <div class="sidebar-footer">
      <i class="bi bi-person-circle me-2"></i>
      <span><?php echo get_current_username(); ?></span>
      <span class="badge ms-1 <?php echo is_admin() ? 'badge-admin' : 'badge-staff'; ?>">
        <?php echo $_SESSION['role'] ?? ''; ?>
      </span>
    </div>
  </nav>

  <!-- ── Main Content ─────────────────────────────────────────────────────── -->
  <div class="main-wrapper">
    <!-- Top bar -->
    <div class="topbar d-flex align-items-center justify-content-between">
      <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
      </button>
      <div class="d-flex align-items-center gap-3">
        <span class="text-muted small"><i class="bi bi-clock me-1"></i><span id="clock"></span></span>
        <a href="index.php?page=events&action=add" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle me-1"></i>New Event
        </a>
      </div>
    </div>

    <!-- Flash messages -->
    <div class="px-4 pt-3">
      <?php foreach (get_flash_messages() as $msg): ?>
      <div class="alert alert-<?php echo ($msg['category'] === 'error') ? 'danger' : $msg['category']; ?> alert-dismissible fade show" role="alert">
        <?php echo $msg['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Page content -->
    <div class="content-area">
      <?php echo $content ?? ''; ?>
    </div>
  </div>

  <?php else: ?>

  <!-- Auth pages: no sidebar, full page layout -->
  <div class="auth-wrapper">
    <div class="px-4 pt-3">
      <?php foreach (get_flash_messages() as $msg): ?>
      <div class="alert alert-<?php echo ($msg['category'] === 'error') ? 'danger' : $msg['category']; ?> alert-dismissible fade show shadow" role="alert" style="z-index:9999; position:fixed; top:20px; right:20px;">
        <?php echo $msg['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endforeach; ?>
    </div>
    <?php echo $auth_content ?? ''; ?>
  </div>

  <?php endif; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="<?php echo asset('js/main.js'); ?>"></script>
  <!-- PWA Service Worker Registration -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register("<?php echo asset('sw.js'); ?>")
          .then(reg => console.log('SW registered!', reg))
          .catch(err => console.log('SW registration failed!', err));
      });
    }
  </script>
  <?php echo $scripts ?? ''; ?>
</body>
</html>
