<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $username = $_SESSION['username']; // For regular users
} else if (!isset($_SESSION['user_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $username = $_SESSION['admin_name']; // For admins
} else {
    // Not logged in, redirect to login page
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIITDF</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="icon" href="../media/icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.max.css" rel="stylesheet"> -->
     <style>
        .sidebar-image {
            width: 200%;
            height: auto;
            margin-top: 20px;
            overflow: hidden;
        }
        .sidebar {
            overflow: hidden;
        }
     </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="hideSidebar()">Close</button>
        <img src="../media/icon.png" alt="logo" class="logo ">
        <h2> Discuss forum</h2>
        <ul>
            <li><a href="posts.php"><i class="fas fa-file"></i>Posts</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="my_posts.php"><i class="fas fa-comments"></i> My Posts</a></li>
            <li><a href="resources.php"><i class="fas fa-book"></i> Extras</a></li> 
            <li><a href="groups.php"><i class="fas fa-users"></i> Groups</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <img src="../media/vector-graphics-abstract-art-design-illustration-png-favpng-wxFRAhhegABzn7FPpE6y0NKRc-removebg-preview.png" alt="" class="sidebar-image">
    </div>
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button>
        <div><h1> Discussion forum</h1></div>
        <div class="admin-info">
            <a href="profile.php" style="text-decoration:none;color:#333;">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
            </a>
        </div>
    </div>
    <script>
        function hideSidebar() {
            document.getElementById('sidebar').classList.remove('active');
        }

        function showSidebar() {
            document.getElementById('sidebar').classList.add('active');
        }

        // Toggle sidebar visibility
        document.querySelector('.close-btn').addEventListener('click', hideSidebar);

        // Optional: Add a button to open sidebar if needed
        // Example:
        // <button onclick="showSidebar()">Open Sidebar</button>

        // Toggle sidebar with a keypress (e.g., 's' key)
        document.addEventListener('keydown', function(event) {
            if (event.key === 'j' || event.key === 'S') {
            document.getElementById('sidebar').classList.toggle('active');
            }
        });


    </script>
</body>
</html>