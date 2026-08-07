<?php

// adminacc.php

require 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminlogin.php');
    exit();
}

// Admin name for display
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

// Handle status update (resolve)
if (isset($_POST['resolve']) && isset($_POST['report_id'])) {
    $rid = intval($_POST['report_id']);
    $conn->query("UPDATE reports SET status='resolved' WHERE id=$rid");
    header('Location: ' . $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit();
}

// Handle delete
if (isset($_POST['delete']) && isset($_POST['report_id'])) {
    $rid = intval($_POST['report_id']);
    $conn->query("DELETE FROM reports WHERE id=$rid");
    header('Location: ' . $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit();
}

// Fetch filter/search params
$filter_status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
$allowed_statuses = ['open', 'resolved'];
if ($filter_status !== '' && in_array($filter_status, $allowed_statuses, true)) {
    $where[] = "r.status = '" . $conn->real_escape_string($filter_status) . "'";
}
if ($search) {
    $where[] = "(r.reason LIKE '%" . $conn->real_escape_string($search) . "%' OR r.reported_by LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Fetch reports
$sql = "SELECT r.*, p.content AS post_content, u.username AS reporter_name FROM reports r LEFT JOIN posts p ON r.post_id = p.id LEFT JOIN users u ON r.reported_by = u.id $where_sql ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminacc.css">
    <link rel="stylesheet" href="posts.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, .user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-btns button {
            margin-right: 8px;
            padding: 5px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
        }
        .resolve-btn { background: #27ae60; }
        .delete-btn { background: #e74c3c; }
        .status-open { color: #e67e22; font-weight: bold; }
        .status-resolved { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'admininterface.php'; ?>
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button>
        <div><h1>Reports</h1></div>
        <div class="admin-info">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminName); ?>
        </div>
    </div>
    <div class="main-content" style="margin-top:70px;">
        <div class="actions">
        <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <form class="search-bar" method="get" action="">
            <input type="text" name="search" placeholder="Search reports or user id..." value="<?= htmlspecialchars($search) ?>">
            <select name="status">
                <option value="">All Status</option>
                <option value="open" <?= $filter_status=='open'?'selected':'' ?>>Open</option>
                <option value="resolved" <?= $filter_status=='resolved'?'selected':'' ?>>Resolved</option>
            </select>
            <button type="submit"><i class="fa fa-search"></i> Filter</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Reported By</th>
                    <th>Post</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Reported At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['reporter_name'] ?? $row['reported_by']) ?></td>
                        <td>
                            <?php if ($row['post_id']): ?>
                                <a href="./posts.php#post-<?= $row['post_id'] ?>" target="_blank"><?= htmlspecialchars(mb_strimwidth($row['post_content'], 0, 40, "...")) ?></a>
                            <?php else: ?>
                                [Deleted]
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <?php if ($row['status'] == 'resolved'): ?>
                                <span class="status-resolved">Resolved</span>
                            <?php else: ?>
                                <span class="status-open">Open</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></td>
                        <td class="action-btns">
                            <?php if ($row['status'] != 'resolved'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="resolve" class="resolve-btn"><i class="fa fa-check"></i> Resolve</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this report?');">
                                <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" class="delete-btn"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;color:#888;">No reports found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?= date('Y'); ?> Starkices. All rights reserved.</p>
    </div>
</body>
</html>