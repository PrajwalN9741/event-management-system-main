<?php
$title = "Register – EMS";
ob_start();
?>
<div class="auth-card-wrapper">
    <div class="auth-card" style="max-width:500px">
        <div class="auth-logo">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Set up a new EMS user account</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register" id="regForm" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" id="username" name="username" class="form-control" placeholder="johndoe" required minlength="3" />
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required />
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="6" />
                        <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Minimum 6 characters</div>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" required />
                    </div>
                </div>
            </div>

            <?php if (is_admin()): ?>
            <div class="mb-4">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="bi bi-check-circle me-2"></i>Create Account
            </button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
if (!is_logged_in()) {
    $auth_content = $content;
    $content = null;
}

ob_start();
?>
<script>
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('i');
        if (pwd.type === 'password') { pwd.type = 'text'; icon.className = 'bi bi-eye-slash'; }
        else { pwd.type = 'password'; icon.className = 'bi bi-eye'; }
    });

    document.getElementById('regForm').addEventListener('submit', function (e) {
        const p = document.getElementById('password').value;
        const c = document.getElementById('confirm_password').value;
        if (p !== c) { e.preventDefault(); alert('Passwords do not match!'); return; }
        if (p.length < 6) { e.preventDefault(); alert('Password must be at least 6 characters.'); }
    });
</script>
<?php
$scripts = ob_get_clean();
include 'templates/base.php';
?>
