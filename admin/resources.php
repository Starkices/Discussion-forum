<?php
require 'db_connect.php';
session_start();

// Handle both user and admin sessions
$username = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? null;

$adminName = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? null;

if ($username === null) {
    header('Location: login.php');
    exit();
}

// Fetch user ID and details (with suspension check)
$stmt = $conn->prepare("SELECT id, status FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('User not found. Please check your login.');</script>";
    header('Location: login.php');
    exit();
}

$userId = $user['id'];

if ($user['status'] === '(suspended)') {
    echo "<script>window.location.href = 'suspended.php';</script>";
    exit();
}

// Dummy data for resources (keep as is)
$resources = [
    ['title' => 'Forum Guidelines', 'link' => 'guidelines.php', 'desc' => 'Read the rules before posting.'],
    ['title' => 'How to Start a Discussion', 'link' => 'start_discussion.php', 'desc' => 'Tips for engaging conversations.'],
    ['title' => 'FAQ', 'link' => 'faq.php', 'desc' => 'Frequently asked questions.'],
]; 

$msg = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'resources';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link rel="icon" href="../media/icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f8f8; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        .nav-tabs .nav-link.active { color: #0d6efd; font-weight: bold; }
        .resource-title, .group-name { font-size: 1.2rem; font-weight: 500; }
        .desc { color: #555; margin-top: 6px; }
        .members { color: #888; font-size: 14px; margin-top: 4px; }
        .group-item { border-bottom: 1px solid #eee; padding: 18px 0; }
        .resource-item { border-bottom: 1px solid #eee; padding: 18px 0; }
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
 
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
    <h1 class="mb-4">Discussion Forum Resources</h1>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link <?php echo $activeTab == 'resources' ? 'active' : ''; ?>" href="?tab=resources">Resources</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $activeTab == 'groups' ? 'active' : ''; ?>" href="?tab=groups">Groups</a>
        </li>
    </ul>

    <?php
        // Fetch group data from database based on active tab
        if ($activeTab == 'resources') {
            echo '<h2>Resources</h2>';
            echo '<div class="resource-list">';
            foreach ($resources as $resource) {
                echo '<div class="resource-item">';
                echo '<a href="' . htmlspecialchars($resource['link']) . '" class="resource-title">' . htmlspecialchars($resource['title']) . '</a>';
                echo '<p class="desc">' . htmlspecialchars($resource['desc']) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        } elseif ($activeTab == 'groups') {
            // Fetch all groups (removed LIMIT 2)
            $stmt = $conn->prepare("SELECT id, group_name, group_description, password FROM groups LIMIT 4");
            $stmt->execute();
            $result = $stmt->get_result();
            echo '<h2>Groups</h2>';
            echo '<div class="group-list">';
            echo '<p class="members">Join a group to start discussions and connect with others.</p>';
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="group-item">';
                    echo '<h3 class="group-name">' . htmlspecialchars($row['group_name']) . '</h3>';
                    echo '<p class="desc">' . htmlspecialchars($row['group_description']) . '</p>';
                    // Fetch number of members in the group
                    $memberStmt = $conn->prepare("SELECT COUNT(*) as member_count FROM group_members WHERE group_id = ?");
                    $memberStmt->bind_param("i", $row['id']);
                    $memberStmt->execute();
                    $memberResult = $memberStmt->get_result();
                    $memberCount = $memberResult->fetch_assoc()['member_count'];
                    $memberStmt->close();
                    echo '<p class="members">Members: ' . $memberCount . '</p>';
                    // Check if user is already a member 
                    $checkMemberStmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
                    $checkMemberStmt->bind_param("ii", $row['id'], $userId);
                    $checkMemberStmt->execute();
                    $checkMemberResult = $checkMemberStmt->get_result();
                    $isMember = $checkMemberResult->fetch_row()[0] > 0;
                    $checkMemberStmt->close();
                    if (!$isMember) {
                        if($row['password'] !== null) {
                            echo '<button type="button" class="btn btn-secondary" onclick="joinGroupWithPassword(' . $row['id'] . ')">Join with Password</button>';
                        }else{
                            echo '<a href="join_group.php?id=' . $row['id'] . '" class="btn btn-primary mt-2">Join Group</a>';
                        }
                    } else {
                        echo '<span class="text-success mt-2">You are a member of this group</span>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p>No groups available yet. Contact an admin to create one.</p>';
            }
            $stmt->close();
            echo '</div>';
        }
    ?>
          
</div>
</body>
<script>
function joinGroupWithPassword(groupId) {
    var pwd = prompt("This group requires a password. Please enter it:");
    if (pwd !== null && pwd !== "") {
        window.location.href = "join_group.php?id=" + groupId + "&group_password=" + encodeURIComponent(pwd);
    }
}
</script>
</html>