<?php
session_start();

require 'db_connect.php';

// --- Security: CSRF token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- Security: Rate limiting (max 5 attempts per 10 minutes) ---
if (!isset($_SESSION['admin_login_attempts'])) {
    $_SESSION['admin_login_attempts'] = 0;
    $_SESSION['admin_last_attempt'] = time();
}
$lockout_time = 600; // 10 minutes
$max_attempts = 5;
$remaining = $max_attempts - $_SESSION['admin_login_attempts'];
$locked = false;

if ($_SESSION['admin_login_attempts'] >= $max_attempts) {
    if (time() - $_SESSION['admin_last_attempt'] < $lockout_time) {
        $locked = true;
    } else {
        // Reset after lockout period
        $_SESSION['admin_login_attempts'] = 0;
        $remaining = $max_attempts;
        $locked = false;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid session. Please try again.';
    } else {
        $input_password = $_POST['password'] ?? '';
        $admin_username = $_POST['username'] ?? '';

        $db_conn = $conn ?? $mysqli ?? $db ?? null;
        $authenticated = false;
        if ($db_conn) {
            $stmt = $db_conn->prepare("SELECT password FROM admins WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $admin_username);
                $stmt->execute();
                // Ensure variable is defined to avoid undefined variable notices
                $stored_password_hash = null;
                $stmt->bind_result($stored_password_hash);
                if ($stmt->fetch() && $stored_password_hash !== null && password_verify($input_password, $stored_password_hash)) {
                    $authenticated = true;
                }
                $stmt->close();
            }
        }

        if ($authenticated) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin_username;
            $_SESSION['admin_login_attempts'] = 0; // Reset on success

            if ($db_conn) {
                $stmt = $db_conn->prepare("UPDATE admins SET last_login = NOW() WHERE username = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $admin_username);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            header('Location: ./dashboard.php');
            exit;
        } else {
            $_SESSION['admin_login_attempts']++;
            $_SESSION['admin_last_attempt'] = time();
            $remaining = $max_attempts - $_SESSION['admin_login_attempts'];
            $error = $remaining > 0 ? "Incorrect username or password. $remaining attempt(s) left." : "Too many failed attempts. Please wait 10 minutes.";
        }
    }
} elseif ($locked) {
    $wait = $lockout_time - (time() - $_SESSION['admin_last_attempt']);
    $error = "Too many failed attempts. Please wait " . ceil($wait/60) . " minute(s) before trying again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Match login.html CSS as closely as possible -->
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="adminlogin.css">

</head>
<body>
    <div class="abstract-bg">
        <div class="bubble bubble1"></div>
        <div class="bubble bubble2"></div>
        <div class="bubble bubble3"></div>
    </div>
    <div class="login-box">
        <img src="admin-icon.png" alt="Admin" class="admin-logo" onerror="this.style.display='none'">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="text" id="username" name="username" placeholder="Enter Admin Username" required>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="password" name="password" placeholder="Enter Admin Password" required <?= $locked ? 'disabled' : '' ?>>
            <br>
            <button type="submit" <?= $locked ? 'disabled' : '' ?>>Login</button>
        </form>
    </div>
</body>
</html>