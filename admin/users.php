<?php
require_once('db_connect.php');

// Start the session
session_start();
// Example: Check if user is logged in (customize as needed)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}else {
    // Example admin name
    $adminName = $_SESSION['admin_name'] ?? 'Administrator';
}
// name: wilcox
//password: R243029300220
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .actions {
            justify-content: space-between;
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
        }
        .actions a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .user-table th {
            background-color: #f2f2f2;
        }
        .user-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
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
        <div><h1>Students</h1></div>
        <div class="admin-info">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminName); ?>
        </div>
    </div>
    <div class="main-content" style="margin-top:70px;">
        <div class="actions">
        <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="add_user.php" class="btn"><i class="fas fa-user-plus"></i> Add Students</a>
        <a href="delete_user.php" class="btn"><i class="fas fa-user-times"></i> Actions</a>
        </div>
        <h1>Registered Students</h1> 
        <table class="user-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('db_connect.php');
                
                $sql = "SELECT username, created_at, status FROM users";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['username']) . (isset($row['status']) ? htmlspecialchars($row['status']) : '') . "</td>";
                        echo "<td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No users found</td></tr>";
                }
                
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>

</body>
</html>