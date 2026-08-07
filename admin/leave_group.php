<?php
require ('db_connect.php');
session_start();
$username = $_SESSION['username'];
$groupId = $_GET['group_id'] ?? null;
if (!$groupId) {
    echo "Group ID is required.";
    exit;
}else {
    // Check if the user is a member of the group
    $stmt = $conn->prepare("SELECT * FROM group_members WHERE user_id = (SELECT id FROM users WHERE username = ?) AND group_id = ?");
    $stmt->bind_param("si", $username, $groupId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User is a member, proceed to leave the group
        $stmt = $conn->prepare("DELETE FROM group_members WHERE user_id = (SELECT id FROM users WHERE username = ?) AND group_id = ?");
        $stmt->bind_param("si", $username, $groupId);
        if ($stmt->execute()) {
            echo "You have left the group successfully.";
            // Optionally, redirect to the groups page or show a success message
            header("Location: groups.php");
            exit();
        } else {
            echo "Error leaving the group: " . $conn->error;
        }
    } else {
        echo "You are not a member of this group.";
    }
    $stmt->close();
}