 
<?php
require 'db_connect.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['username'];

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if ($user['status'] === '(suspended)') {
    echo "<script>window.location.href = 'suspended.php';</script>";
    exit();
}
// Get user ID from username
$userId = null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $adminName);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .post-list { margin-top: 30px; }
        .post-item { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 18px; margin-bottom: 18px; }
        .post-title { font-size: 1.2em; font-weight: bold; margin-bottom: 6px; }
        .post-meta { color: #888; font-size: 0.95em; margin-bottom: 8px; }
        .post-actions a { color: #0056b3; text-decoration: none; margin-right: 12px; }
        .post-actions a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
     
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
        <h1>Posted discussion </h1>
        <p>Here is the list of your posts </p>
        <div class="post-list">
        <?php
        if ($userId) {
            $sql = "SELECT id, title, category, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="post-item">';
                    echo '<div class="post-title">' . htmlspecialchars($row['title']) . '</div>';
                    echo '<div class="post-meta">Category: ' . htmlspecialchars($row['category']) . ' | Posted on: ' . date('M d, Y H:i', strtotime($row['created_at'])) . '</div>';
                    echo '<div class="post-actions">';
                    echo '<a href="posts.php#post-' . $row['id'] . '"><i class="fas fa-eye"></i> View</a>';
                    echo '<a href="edit_post.php?id=' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</a>';
                    echo '<a href="delete_post.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this post?\')"><i class="fas fa-trash"></i> Delete</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align:center;">You have not posted anything yet.</p>';
            }
            $stmt->close();
        } else {
            echo '<p style="text-align:center;">User not found.</p>';
        }
        ?>
        </div>
    </div>
</body>
</html>