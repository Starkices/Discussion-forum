<?php
require 'db_connect.php';
session_start();
if (!isset($_SESSION['admin_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    // check if user is suspended
    if ($user['status'] === '(suspended)') {
        // User is suspended, redirect to suspended page
        session_start();
        echo "<script>window.location.href = 'suspended.php';</script>";
        exit();
    }else{}

} else if (!isset($_SESSION['user_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // User is logged in, redirect to user dashboard
    
} else if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])) {
    // Not logged in, redirect to login page
    header('Location: ../login.php');
    exit();
}

$adminName = $_SESSION['admin_name'] ?? '';
$username = $_SESSION['username'] ?? '';
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>discussion</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         .chatbox {
            height: 60vh;
            max-height: 60vh;
            overflow-y: auto;
            margin-bottom: 20px;
            background: url('../media/1VSFxbp.jpg');
        }
        .chatbox-footer {
            position: static;
            bottom: 30px;
            left: 0;
            width: 100%;
            background-color: #f1f1f1;
            padding: 10px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            max-height: 20vh;
            border-radius: 20px;
            height: 100px;
        }
        .chatbox-footer form{
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            background-color: white;
            border-radius: 10px;
        }
        .form-group1 {
            flex: 1;
            width: 70%;
        }
        .form-group {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 20%;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            /* background-color: #e9ecef; */
            width: fit-content;
            max-width: 70%;
        }
        .message1 { 
            justify-self: right;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            /* background-color: #e9ecef; */
            width: fit-content;
            max-width: 70%; 
        }
        .navigate {
            position: fixed;
            right: 50px;
            bottom: 30vh;
            border-radius: 50%;
        }
        .chatbox-footer form .btn{
            margin: 0;
            padding: 0;
        }
        .menu{
            z-index: 10000;
            position: absolute;
            top: 50px;
            right: 10px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px;

        }
        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
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
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button> 
            <?php
            $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
            $stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            if ($group_id <= 0) {
            echo '<p style="text-align:center;">no found</p>';
            } else {
                $group_result = $stmt->get_result();
                if ($group_result->num_rows > 0) {
                    while ($row = $group_result->fetch_assoc()) {
                        echo '<div><h1>' . htmlspecialchars($row['group_name']) . '</h1></div>';
                        if (!$is_admin) {
                            echo '<button class="more-options" onclick="toggleDropdown()" aria-label="More options" style="background:none;border:none;cursor:pointer;"> <i class="fas fa-ellipsis-v"></i> </button>';
                        } 
                        // Dropdown menu for more options
                        echo '<div class="menu" id="dropdownMenu" style="display:none;">';
                        echo '<ul>'; 
                        echo '<li><a href="view_group-profile.php?group_id=' . htmlspecialchars($group_id) . '">View Group Profile</a></li>';
                        // Check if the current user is an admin in this group
                        $member_role = '';
                        if (isset($user['id'])) {
                            $role_stmt = $conn->prepare("SELECT role FROM group_members WHERE group_id = ? AND user_id = ?");
                            $role_stmt->bind_param("ii", $group_id, $user['id']);
                            $role_stmt->execute();
                            $role_result = $role_stmt->get_result();
                            if ($role_row = $role_result->fetch_assoc()) {
                                $member_role = $role_row['role'];
                            }
                            $role_stmt->close();
                        }
                        if ($member_role === 'admin' ) {
                            echo '<li><form method="GET" action="edit_group.php" style="display:inline;">';
                            echo '<input type="hidden" name="group_id" value="' . htmlspecialchars($group_id) . '">';
                            echo '<button type="submit" style="background:none;border:none;color:#007bff;cursor:pointer;">Edit Group</button>';
                            echo '</form></li>';
                            echo '<li><form method="POST" action="delete_group.php" style="display:inline;">';
                            echo '<input type="hidden" name="group_id" value="' . htmlspecialchars($group_id) . '">';
                            echo '<button type="submit" style="background:none;border:none;color:#007bff;cursor:pointer;">Delete Group</button>';
                            echo '</form></li>';
                        }
                        echo '<li><form method="GET" action="leave_group.php" style="display:inline;">';
                        echo '<input type="hidden" name="group_id" value="' . htmlspecialchars($group_id) . '">';
                        echo '<button type="submit" style="background:none;border:none;color:#007bff;cursor:pointer;">Leave Group</button>';
                        echo '</form></li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align:center;">no found</p>';
                }
            }
            $stmt->close(); 
            ?>  
        
    </div>
    <div class="main-content" style="margin-top:70px; max-width: 100vh; margin-left:auto; margin-right:auto;">
        <div class="actions">
        <?php
        // Check if the user is logged in as an admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Show admin-specific actions
            echo '<a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>';
            // echo '<a href="manage_posts.php" class="btn"><i class="fas fa-edit"></i> Edit Posts</a>';
            echo '<a href="groups.php" class="btn"><i class="fas fa-arrow-left"></i> Back to group table</a>';
        }
        ?> 
        </div>
        <h2>Group Chat</h2>
        <div class="chatbox" style="border:1px solid #ccc; padding:20px; background-color:#f9f9f9; border-radius:5px;">
        <?php
        //create chatbox 
        if ($group_id <= 0) {
            echo '<p style="text-align:center;">No group selected.</p>';
            exit();
        }else {
        $fstmt = $conn->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
        $fstmt->bind_param("ii", $group_id, $user['id']);
        $fstmt->execute();
        $member_result = $fstmt->get_result();
        if ($member_result->num_rows>0 || $_SESSION['admin_logged_in'] = true) {
            // User is a member, continue with the page
            $fstmt->close(); 
            //Display chat messages like Whatsapp
        $stmt = $conn->prepare("SELECT * FROM group_messages WHERE group_id = ? ORDER BY sent_at ASC");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $messages_result = $stmt->get_result();
        if ($messages_result->num_rows > 0) {
            while ($message = $messages_result->fetch_assoc()) {
                $sender_id = $message['user_id'];
                $stmt_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $stmt_user->bind_param("i", $sender_id);
                $stmt_user->execute();
                $user_result = $stmt_user->get_result();
                $user = $user_result->fetch_assoc();
                //check if message is by the user
                if ($user['username'] == $username) {
                    echo '<div class="message1" ondblclick="deleteMessage(' . $message['id'] . ')"  style="background-color:#c8e6c9;">';
                echo '<strong>me:</strong> ';
                //make the message auto break
                echo '<span>' . nl2br(htmlspecialchars($message['message'])) . '</span>';
                if (!empty($message['attachment'])) {
                    echo '<br><a href="' . htmlspecialchars($message['attachment']) . '" target="_blank">View Attachment</a>';
                }
                echo '<br>';
                echo '<p>' . $message['sent_at'] ? date('d M Y, H:i', strtotime($message['sent_at'])) : 'N/A' . '</p>';
                echo '</div>';
                } else {
                    echo '<div class="message"  style="background-color:#c8e6c9;">';
                echo '<strong>' . htmlspecialchars($user['username']) . ':</strong> ';
                echo '<span>' . nl2br(htmlspecialchars($message['message'])) . '</span>';
                if (!empty($message['attachment'])) {
                    echo '<br><a href="' . htmlspecialchars($message['attachment']) . '" target="_blank">View Attachment</a>';
                }
                echo '<br>';
                echo '<p>' . $message['sent_at'] ? date('d M Y, H:i', strtotime($message['sent_at'])) : 'N/A' . '</p>';
                echo '</div>';
                }
                
            }
        } else {
            echo '<p style="text-align:center; color:white;">No messages found.</p>';
        }
        } else {
            die('<p style="text-align:center; color:white;">You are not a member of this group.</p>'); ; 
            
        }
        }
        
        ?>
        <!-- button to navigate to last message -->
         <button class="navigate btn btn-secondary mt-3" onclick="navigate()" id="navigate" ><i class="fas fa-arrow-down"></i></button>
        </div>
        <div class="chatbox-footer send_message" id="sendMessageForm"> 
        <form method="POST" action="send_message.php" enctype="multipart/form-data">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">
            <div class="form-group1"> 
                <textarea style="border: none; " name="message" placeholder="Send a message to the group" id="message" class="form-control" rows="2" ></textarea> 
            </div> 
            
            <div class="form-group">
            <button class="btn   " type="button" onclick="document.getElementById('message').value = '';"><i class="fas fa-times"></i></button> 
            <button class="btn " type="button" onclick="document.getElementById('attachment').click();"><i class="fas fa-paperclip"></i></button>
            <input type="file" name="attachment" id="attachment" style="display:none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.docx,.txt">
            <button class="btn " type="submit" ><i class="fas fa-paper-plane"></i></button>
            </div>
        </form>
        </div>
    
    <script>
        function toggleDropdown() {
            event.stopPropagation(); // Prevent click from propagating to the document
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        } 
        //navigate to the last message
        function navigate() {
            const chatbox = document.querySelector('.chatbox');
            chatbox.scrollTop = chatbox.scrollHeight;

        }
        //delete message
        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_message.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Reload the chatbox to reflect changes
                        location.reload();
                    }
                };
                xhr.send('message_id=' + messageId + '&group_id=<?php echo htmlspecialchars($group_id); ?>');
            }
        }
        </script>
</body>
</html>