<?php
session_start();
require_once('db_connect.php'); // Include your database connection file


if (!isset($_SESSION['admin_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $username = $_SESSION['username']; // For regular users
} else if (!isset($_SESSION['user_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $username = $_SESSION['admin_name']; // For admins
} else {
    // Not logged in, redirect to login page
    header('Location: ../login.php');
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
if (isset($_SESSION['user_logged_in']) && $user['status'] === '(suspended)') {
    echo "<script>window.location.href = 'suspended.php';</script>";
    exit();
} 
// Example admin name
$adminName = $_SESSION['admin_name'] ?? $_SESSION['username'];
// Fetch user ID from username
    $userId = null;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();
    $_SESSION['user_id'] = $userId;

// Build a nested tree of comments for a given post
function buildTree($comments, $postId, $parentId = null) {
    $tree = [];
    foreach ($comments as $comment) {
        if ($comment['post_id'] == $postId && $comment['parent_id'] == $parentId) {
            $children = buildTree($comments, $postId, $comment['id']);
            $tree[] = [
                'comment' => $comment,
                'replies' => $children
            ];
        }
    }
    return $tree;
}
 

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentb'])) {
    $postId = intval($_POST['post_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $userId = $_SESSION['username'] ?? $_SESSION['admin_name'];
    $parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;

    // Validate parent_id only if it's set
    $validParent = true;
    if ($parentId !== null) {
        $checkParent = $conn->query("SELECT id FROM comments WHERE id = $parentId AND post_id = $postId");
        $validParent = ($checkParent && $checkParent->num_rows > 0);
    }

    if (!empty($comment) && $validParent) {
        if ($parentId !== null && $validParent) {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $postId, $userId, $comment, $parentId);
        } else {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment ) VALUES (?, ?, ? )");
            $stmt->bind_param("iss", $postId, $userId, $comment);
        }
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "#post-" . $postId);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Comment cannot be empty or invalid parent comment.');</script>";
    }
}


// Handle post report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_post'])) {
    $postId = intval($_POST['report_post_id']);
    $reason = trim(mysqli_real_escape_string($conn, $_POST['reason'])); // Sanitize

    // Refetch userId for safety (in case it's an admin or session mismatch)
    $username = $_SESSION['username'] ?? $_SESSION['admin_name'];
    $userId = null;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();

    if (!empty($reason) && $userId !== null) {
        $stmt = $conn->prepare("INSERT INTO reports (post_id, reported_by, reason, status) VALUES (?, ?, ?, 'open')");
        $stmt->bind_param("iis", $postId, $userId, $reason); // Correct types: int-int-string
        
        if ($stmt->execute()) {
            echo "<script>alert('Post reported successfully.');</script>";
        } else {
            // Improved error handling
            echo "<script>alert('Error reporting post: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Reason cannot be empty or user not found.');</script>";
    }
    
    // Refresh to show updated reports (optional)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <link rel="stylesheet" href="adminacc.css">
    <link rel="stylesheet" href="posts.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="../media/icon.png" type="image/png">
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
        .posts {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .small-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.39);
            transition: box-shadow 0.3s ease;
            overflow: hidden;
        }

        .comment-input {
            width: 100%;
            min-height: 60px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 1rem;
            font-family: 'Roboto', Arial, sans-serif;
            margin-bottom: 10px;
            background: #f9f9f9;
            transition: border-color 0.2s, box-shadow 0.2s;
            resize: vertical;
            box-sizing: border-box;
        }
        .comment-input:focus {
            border-color: #007bff;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
        }
        .btn-comment {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 6px rgba(0,123,255,0.08);
            margin-bottom: 10px;
        }
        .btn-comment:hover {
            background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
        }
        .comment {
            background: #f5f7fa;
            /* border-radius: 6px; */
            padding: 12px 16px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border-bottom: 2px solid #e3e8ee;
        }
        .comment .meta {
            font-size: 0.85rem;
            color: #888;
            margin-top: 4px;
        }
        .replies {
            margin-left: 24px;
            border-left: 2px solid #e3e8ee;
            /* border-bottom: 2px solid #e3e8ee; */
            /* padding-left: 12px; */
            margin-top: 8px;
        }
        .comment-count {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 8px;
        }
        .btn-report{
            outline: none;
            border: none;
            color: #0056b3;
        }
        .profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // Include the sidebar
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
    
    <div class="main-content" style="margin-top:70px;">
        <div class="actions">
        <?php
        // Check if the user is logged in as an admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Show admin-specific actions
            echo '<a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>';
            echo '<a href="manage_posts.php" class="btn"><i class="fas fa-edit"></i> Edit Posts</a>';
        }
        ?>
        <a href="create_post.php" class="btn"><i class="fas fa-plus"></i> Create Post</a>
        </div>
        <?php
        // Check if the user is logged in as an admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Show admin-specific content
            echo '<h1>View Posts</h1>'; 
        }
        ?>
    <!-- Search and filter form -->
    <div class="search-bar">
        <?php
            $search = $_GET['search-box'] ?? '';
        ?>
        <div>
        <form method="get" action="">
            <input type="text" name="search-box" placeholder="Search posts..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" name="search"><i class="fas fa-search"></i></button>
        </form>
        </div>
        <div>
        <form method="get" action="">
            <?php
            $category = [
                'General Discussion',
                'Programming Support',
                'Assignments updates',
                'Announcements',
            ];
            $selected_category = $_GET['category'] ?? '';
            // Category filter dropdown
            echo '<select name="category">';
            echo '<option value="">All Categories</option>';
            foreach ($category as $cat) {
                $selected = ($selected_category === $cat) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
            }
            echo '</select>';
            ?>
            <button type="submit" name="filter"><i class="fas fa-filter"></i> Filter</button>
        </form>
        </div>
    </div>
    <div class="posts">
        <?php

        if (isset($_GET['search'])) {
            $search = $_GET['search-box'];
            $search = mysqli_real_escape_string($conn, $search);
            $sql = "SELECT * FROM posts WHERE title LIKE '%$search%' OR content LIKE '%$search%' ORDER BY created_at DESC";

            $result = $conn->query($sql);

        } elseif (isset($_GET['category']) && !empty($_GET['category'])) {
            $selected_category = $_GET['category'];
            $selected_category = mysqli_real_escape_string($conn, $selected_category);
            $sql = "SELECT * FROM posts WHERE category = '$selected_category' ORDER BY created_at DESC";

            $result = $conn->query($sql);

        } else {
            $sql = "SELECT * FROM posts  ORDER BY created_at DESC";

            $result = $conn->query($sql);
        }

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $postId = $row['id'];
                $postTitle = htmlspecialchars($row['title']);
                $postContent = htmlspecialchars($row['content']);
                $postCategory = htmlspecialchars($row['category']);
                $image = htmlspecialchars($row['image']);
                $postCreatedAt = date('d M Y, H:i', strtotime($row['created_at'])) ;
                
                // Fetch poster's name
                $postUserId = $row['user_id'];
                $postUsername = '';
                $userStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $userStmt->bind_param("i", $postUserId);
                $userStmt->execute();
                $userStmt->bind_result($postUsername);
                $userStmt->fetch();
                $userStmt->close(); 
                $posterProfile = '';

                //fetch poster's profile image
                $profileStmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
                $profileStmt->bind_param("i", $postUserId);
                $profileStmt->execute();
                $profileStmt->bind_result($posterProfile);
                $profileStmt->fetch();
                $profileStmt->close();
                echo '<div class="small-card" id="post-' . $row['id'] . '">';
                if ($posterProfile) {
                    $posterProfile = htmlspecialchars($posterProfile);
                } else {
                    $posterProfile = '../media/3135715.png'; // Fallback profile image
                }
                echo '<img src="' . htmlspecialchars($posterProfile) . '" alt="Poster Profile" class="profile">';
                echo '<span class="poster-name">' . htmlspecialchars($postUsername) . '</span>';
                echo '<h2>' . $postTitle . '</h2>';
                if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                    // Show admin-specific
                echo '<p><strong>Category:</strong> ' . $postCategory . '</p>';
                echo '<p><strong>Created on:</strong> ' . $postCreatedAt . '</p>';
                }
                if ($image) {
                    echo "<a href='$image' ><img src='$image' alt='Post Image' style='max-width: 100%; height: auto;'></a>";
                }
                echo '<p>' . nl2br($postContent) . '</p>'; 
                // Fetch comments for this post 
                $commentsQuery = "SELECT * FROM comments WHERE post_id = $postId ORDER BY created_at ASC";
                $commentsResult = $conn->query($commentsQuery);
                $comments = [];
                if ($commentsResult && $commentsResult->num_rows > 0) {
                    while ($commentRow = $commentsResult->fetch_assoc()) {
                        $comments[] = $commentRow;
                    }
                }
                // Build the comment tree
                $commentTree = buildTree($comments, $postId);
                // Display comments
                $commentCount = count($comments);
                echo '<div class="comment-count" onclick="togglecomments(\'comments-' . $postId . '\')">' . $commentCount . ' Comments</div>';
                if (count($commentTree) > 0) {
 
                echo '<div class="comments" id="comments-' . $postId . '" style="display:none;">';
                    foreach ($commentTree as $commentData) {
                        $comment = $commentData['comment'];
                        $commentId = $comment['id'];
                        $commentContent = htmlspecialchars($comment['comment']);
                        $commentUserId = htmlspecialchars($comment['user_id']);
                        $commentCreatedAt = date('d M Y, H:i', strtotime($comment['created_at'])) ;
                        echo '<div class="comment">';
                        echo '<p><strong>' . $commentUserId . '</strong> <span class="meta">on ' . $commentCreatedAt . '</span></p>';
                        echo '<p >' . nl2br($commentContent) . '</p>';
                        echo '<button class="btn-report" style="background:none;" onclick="toggleReplyForm(' . $commentId . ')">..Reply</button>';
                        echo '<div id="reply-' . $commentId . '" class="reply-form" style="display:none;">';
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="post_id" value="' . $postId . '">';
                        echo '<input type="hidden" name="parent_id" value="' . $commentId . '">';
                        echo '<br>';
                        echo '<input type="text" name="comment" placeholder="Reply to this comment..." class="comment-input" required>';
                        echo '<button name="commentb" type="submit" class="btn-comment"><i class="fas fa-reply"></i> Reply</button>';
                        echo '</form>';
                        echo '</div>';
                        // Display replies
                        if (!empty($commentData['replies'])) {
                            echo '<div class="replies">';
                            foreach ($commentData['replies'] as $replyData) {
                                $reply = $replyData['comment'];
                                $replyId = $reply['id'];
                                $replyContent = htmlspecialchars($reply['comment']);
                                $replyUserId = htmlspecialchars($reply['user_id']);
                                $replyCreatedAt = htmlspecialchars($reply['created_at']);
                                echo '<div class="comment">';
                                echo '<p><strong>' . $replyUserId . '</strong> <span class="meta">on ' . $replyCreatedAt . '</span></p>';
                                echo '<p>' . nl2br($replyContent) . '</p>'; 
                                echo '</div>'; // Close reply comment div
                            }
                            echo '</div>'; // Close replies div
                        }
                        echo '</div>'; // Close comment div
                    }
                    echo '</div>'; // Close comments div
                } else {
                    echo '<div class="comments"><p>No comments yet. Be the first to comment!</p></div>';
                }
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="post_id" value="' . $postId . '">';
                // Do NOT include parent_id for top-level comments
                echo '<textarea name="comment" placeholder="Add a comment..." class="comment-input" required></textarea>';
                echo '<button name="commentb" type="submit" class="btn-comment"><i class="fas fa-comment"></i> Comment</button>';
                echo '</form>';
                echo '<button type="button" class="btn-report" style="background: none;" onclick="openReportModal(' . $postId . ')"><i class="fas fa-flag"></i> Report</button>';
                echo '</div>';
            }
        } elseif (isset($selected_category) && !empty($selected_category)) {
            echo '<p>No posts found for ' . htmlspecialchars($selected_category) . '.</p>';
        } else {
            echo '<p>No posts found.</p>';
        }
                
        ?> 
        <div id="reportModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeReportModal()">&times;</span>
        <h2>Report Post</h2>
        <form method="post" id="reportForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" name="report_post_id" id="report_post_id" value="<?php echo $postId; ?>">
            <label for="report_reason">Reason:</label>
            <textarea name="reason" placeholder="Reason for report.." class="comment-input" required style="height: 100px;" id="report_reason"></textarea>
            <button type="submit" name="report_post" class="btn-comment" style="background:#e74c3c;width:100%;"><i class="fas fa-flag"></i> Submit Report</button>

        </form>
    </div>
    </div>
<style>
.modal {
    position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;
}
.modal-content {
    background: #fff; padding: 30px 30px 20px 30px; border-radius: 10px; width: 100%; max-width: 400px; position: relative;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.close {
    position: absolute; right: 18px; top: 12px; font-size: 1.5em; color: #888; cursor: pointer;
}
</style>
<script src="poss.js"></script>

    </div>
</div>
</body>
</html>
