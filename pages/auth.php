<?php
// pages/auth.php
require_once 'config/db.php';
require_once 'includes/auth.php';

if ($page === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            $verify = password_verify($password, $user['password_hash']);
            file_put_contents('debug_auth.log', "DEBUG | Attempt: $username | Provided: $password | DB Hash: " . $user['password_hash'] . " | Verify: " . ($verify ? 'OK' : 'FAIL') . "\n", FILE_APPEND);
            if ($verify) {
                login($user);
                header("Location: index.php?page=dashboard");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            file_put_contents('debug_auth.log', "DEBUG | User NOT found: $username\n", FILE_APPEND);
            $error = "Invalid username or password.";
        }
    }
    include 'templates/auth/login.php';
}

if ($page === 'logout') {
    logout();
}

if ($page === 'register') {
    require_role('admin');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'staff';
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $role]);
            
            if (is_logged_in()) {
                header("Location: index.php?page=users&success=User registered.");
            }
            exit;
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
    include 'templates/auth/register.php';
}

if ($page === 'users') {
    require_role('admin');
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    include 'templates/auth/users.php';
}
?>
