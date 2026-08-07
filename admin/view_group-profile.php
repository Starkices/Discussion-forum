<?php
session_start();
require 'db_connect.php'; // Include your database connection file
 

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch username
$username = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? '';
$adminName = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? '';

// Fetch user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check if user is suspended (for non-admins)
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    if ($status === '(suspended)') {
        echo "<script>window.location.href = 'suspended.php';</script>";
        exit();
    }
}

// Fetch group_id from GET
$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
if ($group_id <= 0) {
    echo "<p>Invalid group ID.</p>";
    exit();
}

// Fetch group details
$stmt = $conn->prepare("SELECT g.group_name, g.group_description, g.created_at, g.max_members, u.username AS created_by 
                        FROM groups g 
                        JOIN users u ON g.created_by = u.id 
                        WHERE g.id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$group = $result->fetch_assoc();
$stmt->close();

if (!$group) {
    echo "<p>Group not found.</p>";
    exit();
}

// Fetch current member count
$stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$stmt->bind_result($member_count);
$stmt->fetch();
$stmt->close();

// Fetch members list
$stmt = $conn->prepare("SELECT u.username, gm.role, gm.joined_at 
                        FROM group_members gm 
                        JOIN users u ON gm.user_id = u.id 
                        WHERE gm.group_id = ? 
                        ORDER BY gm.role DESC, gm.joined_at ASC");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$members_result = $stmt->get_result();
$stmt->close();

// Check if current user is group admin or system admin
$is_group_admin = false;
$is_system_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$stmt = $conn->prepare("SELECT role FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $group_id, $user_id);
$stmt->execute();
$stmt->bind_result($role);
if ($stmt->fetch() && $role === 'admin') {
    $is_group_admin = true;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Profile - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .main-content {
            margin-top: 70px;
            padding: 20px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .group-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .group-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .group-header h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 2rem;
        }
        .group-info p {
            margin: 10px 0;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .group-info i {
            color: #3498db;
        }
        .members-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .members-table th, .members-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .members-table th {
            background-color: #3498db;
            color: #fff;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .members-table tr:hover {
            background-color: #f1f3f5;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 15px;
            font-size: 0.9rem;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
            border-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
            border-color: #7f8c8d;
        }
        .btn-info {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }
        .btn-info:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        .footer {
            text-align: center;
            color: #7f8c8d;
            padding: 20px 0;
            margin-top: 40px;
            border-top: 1px solid #eee;
        }
        @media (max-width: 768px) {
            .main-content {
                padding: 10px;
            }
            .group-card {
                margin: 0 -10px;
                border-radius: 0;
            }
            .members-table th, .members-table td {
                padding: 8px;
                font-size: 0.85rem;
            }
            .actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
    <div class="main-content">
        <div class="group-card">
            <div class="group-header">
                <h2><?php echo htmlspecialchars($group['group_name']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($group['group_description'] ?? 'No description available.'); ?></p>
            </div>
            <div class="group-info">
                <p><i class="fas fa-user"></i> Created by: <?php echo htmlspecialchars($group['created_by']); ?></p>
                <p><i class="fas fa-calendar"></i> Created on: <?php echo date('M d, Y H:i', strtotime($group['created_at'])); ?></p>
                <p><i class="fas fa-users"></i> Members: <?php echo $member_count; ?> / <?php echo ($group['max_members'] > 0) ? $group['max_members'] : 'Unlimited'; ?></p>
            </div>
            <h3 class="mt-4">Members</h3>
            <div class="table-responsive">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Joined At</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($members_result->num_rows > 0): ?>
                            <?php while ($member = $members_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['username']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($member['role'])); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($member['joined_at'])); ?></td> 
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="<?php echo ($is_group_admin || $is_system_admin) ? 4 : 3; ?>">No members yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="actions mt-4">
                <a href="group.php?group_id=<?php echo $group_id; ?>" class="btn btn-primary"><i class="fas fa-comments"></i> Go to Group Chat</a>
                <a href="groups.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Groups</a>
                <?php if ($is_group_admin || $is_system_admin): ?>
                    <a href="edit_group.php?group_id=<?php echo $group_id; ?>" class="btn btn-info"><i class="fas fa-edit"></i> Edit Group</a>
                    <a href="?action=delete&group_id=<?php echo $group_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete Group</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <p class="footer">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>
</body>
</html>