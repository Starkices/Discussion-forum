<?php
// adminacc.php

require 'db_connect.php';

session_start();
// Example: Check if user is logged in (customize as needed)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminlogin.php');
    exit();
}

// Example admin name
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NIITDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts & Font Awesome for icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    include 'admininterface.php';
    ?>
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button>
        <div><h1>Admin Settings</h1></div>
        <div class="admin-info">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminName); ?>
        </div>
    </div>
    <div class="main-content" style="margin-top:70px;">
        <?php
        // Fetch current settings from database (example: site_name, email, maintenance_mode)
        $query = "SELECT * FROM settings LIMIT 1";
        $result = mysqli_query($conn, $query);
        $settings = mysqli_fetch_assoc($result);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
            $admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
            $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

            $update = "UPDATE settings SET site_name='$site_name', admin_email='$admin_email', maintenance_mode=$maintenance_mode";
            if (mysqli_query($conn, $update)) {
                echo '<div class="alert-success">Settings updated successfully.</div>';
                // Refresh settings
                $settings['site_name'] = $site_name;
                $settings['admin_email'] = $admin_email;
                $settings['maintenance_mode'] = $maintenance_mode;
            } else {
                echo '<div class="alert-error">Failed to update settings.</div>';
            }
        }
        ?>

        <div class="settings-container" style="max-width:500px;margin:0 auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 8px #eee;">
            <h2 style="margin-bottom:24px;">Site Settings <a href="faq_admin.php" class=" btn btn-primary mt-2" ><i class="fas fa-book"></i> Edit faq</a></h2>
            
            <form method="post" autocomplete="off">
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="site_name" style="display:block;margin-bottom:6px;">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="admin_email" style="display:block;margin-bottom:6px;">Admin Email</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:6px;">
                        <input type="checkbox" name="maintenance_mode" <?php if (!empty($settings['maintenance_mode'])) echo 'checked'; ?>>
                        Enable Maintenance Mode
                    </label>
                </div>
                <button type="submit" style="background:#007bff;color:#fff;padding:10px 24px;border:none;border-radius:4px;cursor:pointer;">Save Changes</button>
            </form>
        </div>
        <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>
    </div>