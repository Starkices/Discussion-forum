<?php
// adminacc.php

require 'db_connect.php';

session_start();
// Example: Check if user is logged in (customize as needed)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminlogin.php');
    exit();
}

// Example admin name
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Handle bulk delete
$bulk_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    if (is_array($_POST['post_ids']) && count($_POST['post_ids']) > 0) {
        $post_ids = array_map('intval', $_POST['post_ids']);
        $ids_placeholder = implode(',', $post_ids);
        
        // Prepare and execute the delete query
        $delete_query = "DELETE FROM posts WHERE id IN ($ids_placeholder)";
        $delete_result = $conn->query($delete_query);
        if ($delete_result) {
            $bulk_msg = 'Selected posts deleted successfully.';
        } else {
            $bulk_msg = 'Error deleting posts: ' . $conn->error;
        }
    } else {
        $bulk_msg = 'No posts selected for deletion.';
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Manage Posts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="adminacc.css">
    <link rel="stylesheet" href="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="adminacc.css"> -->
    <style>
        .search-bar {
    margin-bottom: 18px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}

.search-bar input[type="text"] {
    border-radius: 8px;
    border: 1px solid #c7e0ff;
    padding: 8px 14px;
    font-size: 1em;
    background: #fafdff;
    transition: border 0.2s;
}

.search-bar input[type="text"]:focus {
    border: 1.5px solid #4f8cff;
    outline: none;
}

.search-bar button,
.category-filter button {
    background: linear-gradient(90deg, #4f8cff 0%, #6ed6ff 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(79, 140, 255, 0.09);
}

.search-bar button:hover,
.category-filter button:hover {
    background: linear-gradient(90deg, #357ae8 0%, #4fd6ff 100%);
}

        .table-wrapper {
            max-height: 300px; /* Adjust as needed */
            overflow-y: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #f9fafb;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(99,102,241,0.05);
            animation: fadeIn 1.2s;
            
        }
        thead {
            position: sticky;
            top: 0; 
            z-index: 10;
            background: #0056b3;
            color: #fff;
        }
        th, td {
            padding: 13px 10px;
            text-align: left;
        }

        th {
            background-color: #0056b3;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 2px solid #4f46e5;
        }

        tbody tr {
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: #e0e7ff;
            cursor: pointer;
            animation: rowHighlight 0.3s;
        }

        @keyframes rowHighlight {
            from { background: #f9fafb; }
            to { background: #e0e7ff; }
        }

        td {
            font-size: 0.98rem;
            color: #333;
            vertical-align: middle;
        }

        .post-img {
            max-width: 70px;
            max-height: 50px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(99,102,241,0.10);
            border: 1px solid #e0e7ff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .post-img:hover {
            transform: scale(1.2) rotate(-2deg);
            box-shadow: 0 6px 24px rgba(99,102,241,0.18);
            z-index: 2;
        }

        .bulk-btn {
            background-color: #b91c1c;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s, transform 0.1s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 2px 8px rgba(239,68,68,0.08);
        }

        .bulk-btn:hover {
            background-color:rgb(107, 13, 13);
            transform: scale(1.04);
        }

        .update-msg {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
            padding: 12px 18px;
            border-radius: 7px;
            margin-bottom: 18px;
            font-size: 1.08rem;
            font-weight: 500;
            display: inline-block;
            animation: fadeIn 1.1s;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #6366f1;
            transition: box-shadow 0.2s;
        }
        input[type="checkbox"]:focus {
            box-shadow: 0 0 0 2px #6366f155;
        }

        /* Dynamic lively accent animation */
        .bulk-btn, .search-bar button {
            position: relative;
            overflow: hidden;
        }
        .bulk-btn::after, .search-bar button::after {
            content: "";
            position: absolute;
            left: -80px;
            top: 0;
            width: 60px;
            height: 100%;
            background: rgba(255,255,255,0.08);
            transform: skewX(-20deg);
            animation: accentMove 2.5s linear infinite;
            pointer-events: none;
        }
        @keyframes accentMove {
            0% { left: -80px; }
            100% { left: 120%; }
        }

        @media (max-width: 900px) {
            table, th, td {
            font-size: 0.92rem;
            }
        }

        @media (max-width: 600px) {
            table, th, td {
            font-size: 0.85rem;
            }
            .post-img {
            max-width: 40px;
            max-height: 30px;
            }
        }
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
    </style>
</head>
<body>
    <?php include 'admininterface.php'; ?>
     
    <div class="main-content" style="margin-top:70px;">
        <div class="actions">
            <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <a href="posts.php" class="btn"><i class="fas fa-eye"></i> Back to posts</a>
        </div>
        <?php if (!empty($bulk_msg)): ?>
            <div class="update-msg"><?= htmlspecialchars($bulk_msg) ?></div>
        <?php endif; ?>
        <form class="search-bar" method="get" action="">
            <input type="text" name="search" placeholder="Search content or poster..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fa fa-search"></i> Search</button>
        </form>
        <form method="post" action="">
        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Content</th>
                    <th>Image</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            if(!empty($search)) {
                $search = $conn->real_escape_string($search);
                $query = "SELECT * FROM posts WHERE content LIKE '%$search%' OR user_id LIKE '%$search%' ORDER BY created_at DESC";
            } else {
                $query = "SELECT * FROM posts ORDER BY created_at DESC";
            }
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td><input type="checkbox" name="post_ids[]" value="' . htmlspecialchars($row['id']) . '"></td>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['user_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['content']) . '</td>';
                    if (!empty($row['image'])) {
                        echo '<td><img src="' . htmlspecialchars($row['image']) . '" alt="Post Image" class="post-img"></td>';
                    } else {
                        echo '<td>No Image</td>';
                    }
                    echo '<td>' . htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['created_at']))) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">No posts found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
        </div>
            <button type="submit" name="bulk_delete" class="bulk-btn"><i class="fas fa-trash"></i> Delete Selected</button>
        </form>
        <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>
    </div>
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('post_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</body>
</html>