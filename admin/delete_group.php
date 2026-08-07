<?php
require 'db_connect.php';
session_start();

//handle delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];
    
    // Check if the group exists
    $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ?");
    $stmt->bind_param("i", $groupId);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Group exists, proceed to delete
        $stmt->close();
        
        // Delete group members
        $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        
        // Delete group messages
        $stmt = $conn->prepare("DELETE FROM group_messages WHERE group_id = ?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        
        // Delete the group itself
        $stmt = $conn->prepare("DELETE FROM groups WHERE id = ?");
        $stmt->bind_param("i", $groupId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Group deleted successfully.";
            header("Location: groups.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to delete group.";
        }
    } else {
        $_SESSION['error'] = "Group not found.";
    }
    
    $stmt->close();
}
