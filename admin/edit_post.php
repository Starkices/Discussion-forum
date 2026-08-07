<?php 
require_once('db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])) {
    header('Location: ../login.php');
    exit();
}
$adminName = $_SESSION['admin_name'] ?? $_SESSION['username'];

$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = "";

// Fetch post data
if ($postId > 0) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if (!$post) {
        $msg = "Post not found.";
    }
} else {
    $msg = "No post selected.";
}

// Handle form submission for updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // Handle image upload
    $image = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $conn->prepare("UPDATE posts SET title = ?, category = ?, content = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $category, $content, $image, $postId);
    if ($stmt->execute()) {
        $msg = "Post updated successfully.";
        // Refresh post data
        $post['title'] = $title;
        $post['category'] = $category;
        $post['content'] = $content;
        $post['image'] = $image;
    } else {
        $msg = "Error updating post: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link rel="stylesheet" href="adminacc.css">
    <link rel="stylesheet" href="posts.css">
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
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .form-group {
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
            letter-spacing: 0.02em;
        }
        .form-group input {
            width: 95%;
            padding: 10px 12px;
            border: 1.5px solid #bfc9d9;
            border-radius: 6px;
            font-size: 1rem;
            background: #f6f8fa;
            transition: border 0.2s;
        }
        .form-group input:focus {
            border: 1.5px solid #007bff;
            outline: none;
            background: #fff;
        }
        .btn-submit {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            background: #f9f9f9;
            transition: border-color 0.2s;
        }

        .form-group input[type="text"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group input[type="file"] {
            margin-top: 5px;
            padding: 5px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            color: #333;
            transition: border-color 0.2s;
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
    <div class="main-content" style="margin-top:70px;">
        <h1>Edit Post</h1>
        <p>Edit your post below and click "Update Post" to save changes.</p>
        <form method="post" enctype="multipart/form-data">
            <span class="msg"><?php echo $msg; ?></span>
            <br>
            <div class="form-group">
                <label for="title">Post Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Post Category:</label>
                <select name="category" id="category" required>
                    <option value="General Discussion" <?php if (($post['category'] ?? '') == 'General Discussion') echo 'selected'; ?>>General</option>
                    <option value="Programming Support" <?php if (($post['category'] ?? '') == 'Programming Support') echo 'selected'; ?>>Programming Support</option>
                    <option value="Assignment updates" <?php if (($post['category'] ?? '') == 'Assignment updates') echo 'selected'; ?>>Assignment updates</option>
                    <?php
                    if ($_SESSION['admin_logged_in']) {
                        echo '<option value="Announcements"';
                        if (($post['category'] ?? '') == 'Announcements') echo ' selected';
                        echo '>Admin Announcements</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Post Content:</label>
                <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Post Image (optional):</label>
                <?php if (!empty($post['image'])): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Current Image" style="max-width:120px;max-height:120px;border-radius:6px;margin-bottom:8px;">
                        <br>
                        <small>Current image shown above. Upload to replace.</small>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="update" class="btn-submit">Update Post</button>
        </form>
        <div class="actions">
            <a href="my_posts.php" class="btn"><i class="fas fa-arrow-left"></i> Back to My Posts</a>
            <a href="posts.php" class="btn"><i class="fas fa-eye"></i> View All Posts</a>
        </div>
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> NIITDF. All rights reserved.</p>
</body>
</html>