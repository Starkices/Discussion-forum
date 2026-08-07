<?php

require 'db_connect.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$adminName = $_SESSION['username'];
$username = $_SESSION['username'];

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
if ($user['status'] === '(suspended)') {
        // User is suspended, redirect to suspended page
        session_start();
        echo "<script>window.location.href = 'suspended.php';</script>";
        exit();
    }else{}
// Handle profile update
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Edit profile
    if (isset($_POST['edit_profile'])) {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        // Handle profile picture upload
        $profile_pic = $user['profile_pic'];
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $target = './uploads/profile_' . $username . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);
            $profile_pic = $target;
        }
        // Handle cover photo upload
        $cover_photo = $user['cover_photo'];
        if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
            $ext2 = pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION);
            $target2 = './uploads/cover_' . $username . '.' . $ext2;
            move_uploaded_file($_FILES['cover_photo']['tmp_name'], $target2);
            $cover_photo = $target2;
        }
        $update = $conn->prepare("UPDATE users SET username=?, email=?, profile_pic=?, cover_photo=? WHERE username=?");
        $update->bind_param("sssss", $fullname, $email, $profile_pic, $cover_photo, $username);
        if ($update->execute()) {
            $update_msg = "Profile updated!";
            // Refresh user info
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user = $user_result->fetch_assoc();
        } else {
            $update_msg = "Update failed!";
        }
    }
    // Change password
    if (isset($_POST['change_password'])) {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        if (password_verify($old, $user['password'])) {
            if ($new === $confirm && strlen($new) >= 6) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password=? WHERE username=?");
                $update->bind_param("ss", $hashed, $username);
                if ($update->execute()) {
                    $update_msg = "Password changed!";
                } else {
                    $update_msg = "Password update failed!";
                }
            } else {
                $update_msg = "Passwords do not match or too short!";
            }
        } else {
            $update_msg = "Old password incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="../media/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
 
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
        <?php if (!empty($user['cover_photo'])): ?>
            <img class="cover-photo" src="<?= htmlspecialchars($user['cover_photo']) ?>" alt="Cover Photo">
        <?php else: ?>
            <div class="cover-photo"></div>
        <?php endif; ?>
        <div class="profile-header">
            <img class="profile-pic" src="<?= htmlspecialchars($user['profile_pic'] ?? '../media/3135715.png') ?>" alt="Profile Picture">
            <div class="profile-info">
                <h2><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></h2>
                <p><i class="fa fa-user"></i> @<?= htmlspecialchars($user['username']) ?></p>
                <p><i class="fa fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                <p><i class="fa fa-calendar"></i> Joined: <?= date('F Y', strtotime($user['created_at'])) ?></p>
            </div>
            <button class="edit-btn" onclick="document.getElementById('editModal').style.display='block'"><i class="fa fa-edit"></i> Edit Profile</button>
        </div>
        <?php if ($update_msg): ?>
            <div class="update-msg"><?= htmlspecialchars($update_msg) ?></div>
        <?php endif; ?>
        <div class="posts-section">
            <h3 style="margin-bottom:20px;">Recent Posts</h3>
            <?php
            //fetch user id
            $user_id = $user['id'];
            // Fetch recent posts by this user
            $posts_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
            $posts_stmt->bind_param("i", $user_id);
            $posts_stmt->execute();
            $posts_result = $posts_stmt->get_result();
 
            ?>
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <i class="fa fa-clock"></i> <?= date('M d, Y H:i', strtotime($post['created_at'])) ?>
                        </div>
                        <div class="post-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
                        <?php if (!empty($post['image'])): ?>
                            <img class="post-img" src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </div>
        <div class="actions">
            <a href="posts.php" class="btn"><i class="fas fa-arrow-left"></i> Back to posts</a>
            <a href="my_posts.php" class="btn"><i class="fas fa-eye"></i> view all</a>
            </div>
    </div>
    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
            <h2>Edit Profile</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($user['username']) ?>" required>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <label for="profile_pic">Profile Picture</label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                <label for="cover_photo">Cover Photo</label>
                <input type="file" name="cover_photo" id="cover_photo" accept="image/*">
                <button type="submit" name="edit_profile">Save Changes</button>
            </form>
            <hr>
            <h3>Change Password</h3>
            <form method="post">
                <label for="old_password">Old Password</label>
                <input type="password" name="old_password" id="old_password" required>
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required minlength="6">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required minlength="6">
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>
    <script>
        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>