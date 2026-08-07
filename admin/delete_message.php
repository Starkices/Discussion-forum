<?php
require ('db_connect.php');
session_start();
$adminName = $_SESSION['username'];
$username = $_SESSION['username'];
//delete message functionality
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc(); 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_id']) && isset($_POST['group_id'])) {
    $messageId = $_POST['message_id'];
    $groupId = $_POST['group_id']; 

    //check if user is the poster of the message
    $stmt = $conn->prepare("SELECT user_id FROM group_messages WHERE id = ? AND group_id = ?");
    $stmt->bind_param("ii", $messageId, $groupId);
    $stmt->execute();
    $stmt->bind_result($posterId);
    $stmt->fetch();
    $stmt->close();

    if ($_SESSION['admin_logged_in'] || $user['id'] == $posterId) {
        // User is admin or the poster of the message
        $stmt = $conn->prepare("DELETE FROM group_messages WHERE id = ? AND group_id = ?");
        $stmt->bind_param("ii", $messageId, $groupId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Message deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete message.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "You do not have permission to delete this message.";
    }
}