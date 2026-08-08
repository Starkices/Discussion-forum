<?php

require_once('db_connect.php');

session_start();

// Determine login state safely (isset() first avoids "undefined array key" warnings)
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$isUser = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

if (!$isAdmin && !$isUser) {
    // Not logged in, redirect to login page
    header('Location: ../login.php');
    exit();
}

// Regular users: check suspension status
if ($isUser) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && $user['status'] === '(suspended)') {
        echo "<script>window.location.href = 'suspended.php';</script>";
        exit();
    }
}

$adminName = $_SESSION['admin_name'] ?? $_SESSION['username'] ?? '';

$msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($category) || empty($content)) {
        $msg = "Please fill in all fields.";
    } else {
        // Look up the current user's id
        $username = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? null;
        $userId = null;
        if ($username !== null) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($userId);
            $stmt->fetch();
            $stmt->close();
        }

        if ($userId === null) {
            $msg = "Could not find your user account. If you're logged in as an admin, make sure your admin account is Valid.";
        } else {
            // Handle file upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }
                $imagePath = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                    $image = $imagePath;
                }
            }

            $stmt = $conn->prepare("INSERT INTO posts (title, category, content, image, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $category, $content, $image, $userId);

            if ($stmt->execute()) {
                $msg = "Post created successfully.";
            } else {
                $msg = "Error creating post: " . $stmt->error;
            }
            $stmt->close();
        }
    }
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
    if ($isAdmin) {
        // Include the sidebar
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
    
    <div class="main-content" style="margin-top:70px;">
        <h1>Create Post</h1>
        <p>Please fill out the form below to create a new post.</p>
        <!-- Post creation form -->
        <form method="post" enctype="multipart/form-data">
            <span class="msg"><?php echo htmlspecialchars($msg); ?></span>
            <br>
            <div class="form-group">
                <label for="title">Post Title:</label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="category">Post Category:</label>
                <select name="category" id="category" required>
                    <option value="General Discussion">General</option>
                    <option value="Programming Support">Programming Support</option>
                    <option value="Assignment updates">Assignment updates</option>
                    <?php if ($isAdmin): ?>
                        <option value="Announcements">Admin Announcements</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Post Content:</label>
                <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Post Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="create" class="btn-submit">Create Post</button>   
        </form>
        <div class="actions">
        <?php if ($isAdmin): ?>
            <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <?php endif; ?>
        <a href="posts.php" class="btn"><i class="fas fa-eye"></i> View Posts</a>
        </div>
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>

</body>
</html>