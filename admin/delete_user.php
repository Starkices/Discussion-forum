<?php
// adminacc.php
require_once 'db_connect.php';

session_start();
// Example: Check if user is logged in (customize as needed)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Example admin name
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

// Check if the ban request is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $usernameToBan = mysqli_real_escape_string($conn, $_POST['username']);
    $deleteSql = "DELETE FROM users WHERE username = '$usernameToBan'";
    if (mysqli_query($conn, $deleteSql)) {
        header("Location: delete_user.php?msg=User+banned+successfully");
        exit();
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($conn) . "');</script>";
    }
}

// Check if the search query is set	
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT username, created_at FROM users WHERE username LIKE '%$searchQuery%'";

    if (empty($searchQuery)) {
        $sql = "SELECT username, created_at FROM users";
    }
    $result = mysqli_query($conn, $sql);

} else {
    $sql = "SELECT username, created_at FROM users";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NIITDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts & Font Awesome for icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminacc.css">
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
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .user-table th {
            background-color: #f2f2f2;
        }
        .user-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        #search-bar {
            margin: 20px 0;
            display: flex;
            justify-content: flex-end;
        }

        #search-bar form {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 4px 10px;
        }

        #search-bar input[type="text"] {
            border: none;
            outline: none;
            padding: 8px 12px;
            border-radius: 25px 0 0 25px;
            font-size: 16px;
            background: transparent;
            width: 200px;
            transition: width 0.3s;
        }

        #search-bar input[type="text"]:focus {
            width: 260px;
        }

        #search-bar button {
            border: none;
            background: #007bff;
            color: #fff;
            padding: 8px 16px;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        #search-bar button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php
    include 'admininterface.php';
    ?>
     
        <div class="main-content" style="margin-top:70px;">
        <div class="actions">
        <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="add_user.php" class="btn"><i class="fas fa-user-plus"></i> Add Students</a>
        </div>
        <h1>Registered Students</h1>
        <div id="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search Students..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div> 
        <table class="user-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Created At</th>
                    <th>actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('db_connect.php');

                // Check if the search query is set
                if (isset($_GET['search'])) {
                    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);
                    $sql = "SELECT username, created_at FROM users WHERE username LIKE '%$searchQuery%'";
                    $result = mysqli_query($conn, $sql);	
                    if (!$result) {
                        echo "<tr><td colspan='3'>Error: " . mysqli_error($conn) . "</td></tr>";
                    } elseif (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>";
                            echo "<form method='POST' action='delete_user.php' style='display:inline;'>";
                            echo "<input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>";
                            echo "<button type='submit' class='btn' onclick='return confirm(\"Are you sure you want to ban this user?\")'><i class='fas fa-trash-alt'></i> Kick</button>";
                            echo "</form>";
                            echo "<form method='POST' action='suspend_user.php' style='display:inline; margin-left:8px;'>";
                            echo "<input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>";
                            echo "<button type='submit' class='btn' style='background-color:#ffc107;  margin-top: 5px; color:#212529;' onclick='return confirm(\"Are you sure you want to suspend this user?\")'><i class='fas fa-user-slash'></i> Suspend</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No users found</td></tr>";
                    }
                } else {
                    $searchQuery = '';

                    $sql = "SELECT username, created_at, status FROM users";
                $result = mysqli_query($conn, $sql);             
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['username']) . (isset($row['status']) ? htmlspecialchars($row['status']) : '') . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>";
                        echo "<form method='POST' action='delete_user.php' style='display:inline;'>";
                        echo "<input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>";
                        echo "<button type='submit' class='btn' onclick='return confirm(\"Are you sure you want to delete this user?\")'><i class='fas fa-trash-alt'></i> Kick</button>";
                        echo "</form>";
                            echo "<form method='POST' action='suspend_user.php' style='display:inline; margin-left:8px;'>";
                            echo "<input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>";
                            echo "<button type='submit' class='btn' style='background-color:#ffc107; margin-top: 5px; color:#212529;' onclick='return confirm(\"Are you sure you want to suspend this user?\")'><i class='fas fa-user-slash'></i> Suspend</button>";
                            echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No users found</td></tr>";
                }
                }
                
                
                
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>