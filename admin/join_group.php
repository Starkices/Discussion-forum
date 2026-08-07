<?php
// join_group.php
session_start();

require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $joinId = intval($_GET['id']);

    // Prepare and execute join query if not already a member
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) SELECT ?, ? FROM dual WHERE NOT EXISTS (SELECT * FROM group_members WHERE group_id = ? AND user_id = ?)");
    //fetch user ID from database
    $userId = null;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $adminName);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();
    $stmt->bind_param("iiii", $joinId, $userId, $joinId, $userId);
    // Check if max members limit is reached
    $stmt = $conn->prepare("SELECT max_members FROM groups WHERE id = ?");
    $stmt->bind_param("i", $joinId);
    $stmt->execute();
    $stmt->bind_result($maxMembers);
    $stmt->fetch();
    $stmt->close();
    if ($maxMembers > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ?");
        $stmt->bind_param("i", $joinId);
        $stmt->execute();
        $stmt->bind_result($currentMembers);
        $stmt->fetch();
        $stmt->close();

        if ($currentMembers < $maxMembers) {
            if ($stmt->execute()) {
                header("Location: resources.php?msg=Successfully+joined+the+group");
                exit();
            } else {
                echo "Error joining group.";
            }
        } else {
            echo "Group is full. Cannot join.";
        }
    } else {
        if ($stmt->execute()) {
                header("Location: resources.php?msg=Successfully+joined+the+group");
                exit();
            } else {
                echo "Error joining group.";
            }
    }
} else {
    echo "No post ID specified.";
}
?>