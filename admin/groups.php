<?php
session_start();
require 'db_connect.php'; // Include your database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch username and role
$username = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? '';
$adminName = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? '';
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

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

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'])) {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description'] ?? '');
    $group_password = !empty($_POST['group_password']) ? password_hash($_POST['group_password'], PASSWORD_DEFAULT) : null;
    $max_members = (int)($_POST['max_members'] ?? 0);
    if ($group_name !== '' && strlen($group_name) <= 100) {
        $stmt = $conn->prepare("INSERT INTO groups (group_name, group_description, created_by, password, max_members) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $group_name, $group_description, $user_id, $group_password, $max_members);
        if ($stmt->execute()) {
            echo "<script>alert('Group created successfully!');</script>";
        } else {
            echo "Error creating group: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle join group
if (isset($_GET['join_group_id'])) {
    $group_id = (int)$_GET['join_group_id'];
    $stmt = $conn->prepare("SELECT password FROM groups WHERE id=?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($group_password_hash);
    $stmt->fetch();
    $stmt->close();

    if (!empty($group_password_hash)) {
        $input_password = $_GET['group_password'] ?? '';
        if (!password_verify($input_password, $group_password_hash)) {
            echo "<script>alert('Incorrect group password!');window.location='groups.php';</script>";
            exit();
        }
    }

    $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id=? AND user_id=?");
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();

        $stmt = $conn->prepare("SELECT max_members FROM groups WHERE id = ?");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $stmt->bind_result($maxMembers);
        $stmt->fetch();
        $stmt->close();

        $canJoin = true;
        if ($maxMembers > 0) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            $stmt->bind_result($currentMembers);
            $stmt->fetch();
            $stmt->close();
            if ($currentMembers >= $maxMembers) {
                $canJoin = false;
                echo "Group is full. Cannot join.";
            }
        }

        if ($canJoin) {
            $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $group_id, $user_id);
            if ($stmt->execute()) {
                // Optionally redirect or notify
                // echo "<script>alert('Joined successfully!');window.location='groups.php';</script>";
            } else {
                echo "Error joining group: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $stmt->close();
        echo "You are already a member of this group.";
    }
}

// Handle admin actions (e.g., delete group)
if ($is_admin && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
    $group_id = (int)$_GET['group_id'];
    $stmt = $conn->prepare("DELETE FROM groups WHERE id = ?");
    $stmt->bind_param("i", $group_id);
    if ($stmt->execute()) {
        echo "<script>alert('Group deleted successfully!');window.location='groups.php';</script>";
    } else {
        echo "Error deleting group: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <link rel="stylesheet" href="adminacc.css">
    <link rel="stylesheet" href="posts.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php
    if ($is_admin) {
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
    <div class="main-content" style="margin-top:70px;">
        <?php
        // Check if the user is logged in as an admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Show admin-specific content
            echo '<h1>Available groups</h1>'; 
        }else {
            // Show user-specific content
            echo '<h1>Groups</h1>'; 
        }
        ?> 
       <?php if($is_admin): ?>
        <a href="newgroup.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Group</a>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch groups from database in alphabetical order
                $stmt= $conn->prepare("SELECT g.id, g.group_name, u.username, g.created_at FROM groups g JOIN users u ON g.created_by = u.id ORDER BY g.group_name");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['group_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                        echo '<td>' . date('d M Y, H:i', strtotime($row['created_at']))  . '</td>';
                        echo '<td>';
                        echo '<a href="edit_group.php?group_id=' . $row['id'] . '" class="btn" style="color:blue;"><i class="fas fa-edit"></i> Edit</a> ';
                        echo '<form method="POST" action="delete_group.php" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this group?\');">';
                        echo '<input type="hidden" name="group_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn" style="color:red;"><i class="fas fa-trash"></i> Delete</button>';
                        echo '</form> ';
                        echo '<form method="GET" action="group.php" style="display:inline;">';
                        echo '<input type="hidden" name="group_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn " ><i class="fas fa-eye"></i> View Group</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No groups found.</td></tr>';
                } 
                ?>
            </tbody>
        </table> 
        <?php elseif (!$is_admin): ?>
            <?php 
            // Fetch groups the user is a member of in post card format so user can visit the group
            $stmt= $conn->prepare("SELECT g.id, g.group_name, u.username, g.created_at FROM groups g JOIN group_members gm ON g.id = gm.group_id JOIN users u ON g.created_by = u.id WHERE gm.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo '<div class="card-deck">';
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card" style="margin-bottom: 20px;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['group_name']) . '</h5>';
                    echo '<p class="card-text">Created by: ' . htmlspecialchars($row['username']) . '</p>';
                    echo '<p class="card-text">' . date('M d, Y H:i', strtotime($row['created_at'])) . '</p>'; '</p>';
                    echo '<form method="GET" action="group.php">';
                    echo '<input type="hidden" name="group_id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="btn btn-primary">View Group</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No groups found.</p>';
            }
            ?>
            <hr>
            <br>
            <h2>Join a Group</h2> 
                <div class="mb-3">
                    <?php 
                    ?>
                    <div class="search-bar">
                    <?php
                        $search = $_GET['search-box'] ?? '';
                    ?>
                    <div>
                    <form method="get" action="">
                        <input type="text" name="search-box" placeholder="Search groups..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" name="search"><i class="fas fa-search"></i></button>
                    </form>
                    </div>
                    </div>
                    <?php
                    if (isset($_GET['search']) && !empty($search)) {
                        $search = '%' . $conn->real_escape_string($search) . '%';
                        $stmt = $conn->prepare("SELECT id, group_name, group_description FROM groups WHERE group_name LIKE ? OR group_description LIKE ?");
                        $stmt->bind_param("ss", $search, $search);
                    } else {
                        $stmt = $conn->prepare("SELECT * FROM groups WHERE id NOT IN (SELECT group_id FROM group_members WHERE user_id = ?)");
                        $stmt->bind_param("i", $user_id);
                    } 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo '<div class="card-deck">';
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="card" style="margin-bottom: 20px;">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($row['group_name']) . '</h5>';
                            echo '<p class="card-text">' . htmlspecialchars($row['group_description']) . '</p>';
                            echo '<form method="GET" action="">';
                            echo '<input type="hidden" name="join_group_id" value="' . $row['id'] . '">';
                            // echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
                            if($row['password'] !== null) {
                                echo '<button type="button" class="btn btn-secondary" onclick="joinGroupWithPassword(' . $row['id'] . ')">Join with Password</button>';
                            }else{
                                echo '<button type="submit" class="btn btn-primary">Join Group</button>';
                            } 
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>No groups available to join.</p>';
                    }
                    ?>
                </div> 
        <?php endif; ?> 
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>
</body>
</html>
<script>
function joinGroupWithPassword(groupId) {
    var pwd = prompt("This group requires a password. Please enter it:");
    if (pwd !== null && pwd !== "") {
        window.location.href = "?join_group_id=" + groupId + "&group_password=" + encodeURIComponent(pwd);
    }
}
</script>