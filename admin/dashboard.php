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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminacc.css">
</head>
<body>
    <?php
    include 'admininterface.php';
    ?>
     
    <div class="main-content" style="margin-top:70px;">
        <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1> 
        <div class="dashboard-cards">
            <div class="card">
                <i class="fas fa-users"></i>
                <div class="card-title">Total Users</div>
                <div class="card-value">
                    <?php
                    $userCount = $conn->query("SELECT COUNT(*) FROM users");
                    echo $userCount ? $userCount->fetch_row()[0] : 0;
                    ?>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-file-alt"></i>
                <div class="card-title">Reports</div>
                <div class="card-value">
                    <?php
                    $reportCount = $conn->query("SELECT COUNT(*) FROM reports");
                    echo $reportCount ? $reportCount->fetch_row()[0] : 0;
                    ?>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-cogs"></i>
                <div class="card-title">Settings Changed</div>
                <div class="card-value">
                    <?php
                    // Example: If you have a settings_changes table
                    $settingsCount = $conn->query("SELECT COUNT(*) FROM settings_changes");
                    echo $settingsCount ? $settingsCount->fetch_row()[0] : 0;
                    ?>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-clock"></i>
                <div class="card-title">Last Login</div>
                <div class="card-value">
                    <?php
                    // Example: If you store last login in the admin table
                    $admin = $_SESSION['admin_name'] ?? '';
                    $lastLogin = '';
                    $stmt = $conn->prepare("SELECT last_login FROM admins WHERE username = ?");
                    $stmt->bind_param("s", $admin);
                    $stmt->execute();
                    $stmt->bind_result($lastLogin);
                    $stmt->fetch();
                    $stmt->close();
                    echo $lastLogin ? date('d M Y, H:i', strtotime($lastLogin)) : 'N/A';
                    ?>
                </div>
            </div>
        </div>
        <!-- Add more dashboard widgets or tables as needed -->
        <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> NIITDF. All rights reserved.</p>

    </div>
</body>
</html>