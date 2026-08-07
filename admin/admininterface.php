<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DF Admin</title>
    <link rel="icon" href="../media/icon.png" type="image/png">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="hideSidebar()">Close</button>
        <img src="../media/icon.png" alt="logo" class="logo ">
        <h2>FORUM Admin</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Students</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="posts.php"><i class="fas fa-comments"></i> Posts</a></li>
            <li><a href="groups.php"><i class="fas fa-users"></i> Groups</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button>
        <div><h1>Discussion forum</h1></div>
        <div class="admin-info">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminName); ?>
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
            if (event.key === 's' || event.key === 'S') {
            document.getElementById('sidebar').classList.toggle('active');
            }
        });


    </script>
</body>
</html>