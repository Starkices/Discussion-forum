<?php
require ('db_connect.php');
session_start();

$adminName = $_SESSION['admin_name'] ?? '';
$username = $_SESSION['username'] ?? '';
//handle edit group requests
$msg = "";
$groupId = $_GET['group_id'] ?? null;
if (isset($_POST['create']) && isset($_GET['group_id'])) {
    $groupId = $_GET['group_id'];
    
    // Check if the group exists
    $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ?");
    $stmt->bind_param("i", $groupId);
    $stmt->execute();
    $stmt->store_result(); 
    if ($stmt->num_rows === 0) {
        $msg = "Group not found.";
        $stmt->close();
        exit();
    } else {
        // Prepare the update statement
        $maxMembers = $_POST['max_members'] ?? '';
        $apointAdmin = $_POST['apoint_admin'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if(!empty($apointAdmin)) {
            $stmt = $conn->prepare("UPDATE groups g JOIN group_members gm ON g.id = gm.group_id SET g.max_members = ?, g.group_name = ?, g.group_description = ?, gm.role = 'admin' WHERE g.id = ? AND gm.user_id = ?");
            $stmt->bind_param("issii", $maxMembers, $name, $description, $groupId, $apointAdmin);
        } elseif (empty($apointAdmin)) {
            $stmt = $conn->prepare("UPDATE groups SET max_members = ?, group_name = ?, group_description = ? WHERE id = ?");
            $stmt->bind_param("issi", $maxMembers, $name, $description, $groupId);
        } 
        
        if ($stmt->execute()) {
            $msg = "Group updated successfully.";
            $stmt->close();
        } else {
            $msg = "Error updating group: " . $conn->error;
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
    <title>Edit Group</title>
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
        // Include the sidebar
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
     
    <div class="main-content" style="margin-top:70px;">
        <h1>Edit Group</h1>
        <p>Edit your group below and click "Update group" to save changes.</p>
        <!-- Post creation form -->
        <form method="post" >
            <span class="msg"><?php echo $msg; ?></span>
            <br>
            <?php
            $stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $group = $result->fetch_assoc();
            } else {
                die(" Group not found. Please check the group ID."); ;
                exit();
            }
            ?>
            <div class="form-group">
                <label for="max_members">Max Members</label>
                <input type="number" name="max_members" id="max_members" value="<?php echo htmlspecialchars($group['max_members'] ?? ''); ?>" >
            </div>
            <div class="form-group">
                <label for="apoint_admin">Assign Admin</label>
                <select name="apoint_admin" id="apoint_admin">
                    <option value="">Select Admin</option>
                    <?php
                    // Fetch all users to populate the dropdown
                    $stmt = $conn->prepare("SELECT u.id, u.username FROM users u JOIN group_members gm ON u.id = gm.user_id WHERE gm.group_id = ? AND gm.role = 'member'");
                    $stmt->bind_param("i", $groupId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['username']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Group Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($group['group_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Group Description</label>
                <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($group['group_description']); ?></textarea>
            </div>
            <input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
            <div class="form-group">
                <button type="submit" name="create" class="btn-submit">Update Group</button>
            </div>  
        </form>
        <div class="actions">
        <?php
        if(isset($_SESSION['admin_logged_in'])){
            echo '<a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>';
            echo '<a href="groups.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Groups</a>';
        }elseif(isset($_SESSION['username'])) { 
            echo '<a href="group.php?group_id=' . $groupId . '" class="btn"><i class="fas fa-arrow-left"></i> Back to Group</a>';
        }
        ?>
        </div>
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> Starkices. All rights reserved.</p>

</body>
</html>