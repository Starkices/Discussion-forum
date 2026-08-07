<?php
session_start();
require 'db_connect.php';

//fetch user ID from database
$username = $_SESSION['username'] ?? $_SESSION['admin_name'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK && $group_id && $message !== '') {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = basename($_FILES['attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'txt'];

        if (in_array($file_ext, $allowed_extensions)) {
            $upload_dir = 'uploads/';
            $attachment = $upload_dir . uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $attachment);
            
            $stmt = $conn->prepare("INSERT INTO group_messages (group_id, user_id, message, attachment, sent_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiss", $group_id, $user_id, $message, $attachment);
            $stmt->execute();
            $stmt->close();
            // Redirect to the group chat page after sending the message
            header("Location: group.php?group_id=$group_id");
            exit;
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF, PDF, DOCX, and TXT files are allowed.";
            exit;
        }
    }elseif (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = basename($_FILES['attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'txt'];

        if (in_array($file_ext, $allowed_extensions)) {
            $upload_dir = 'uploads/';
            $attachment = $upload_dir . uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $attachment);
            
            $stmt = $conn->prepare("INSERT INTO group_messages (group_id, user_id, attachment, sent_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $group_id, $user_id, $attachment);
            $stmt->execute();
            $stmt->close();
            // Redirect to the group chat page after sending the message
            header("Location: group.php?group_id=$group_id");
            exit;
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF, PDF, DOCX, and TXT files are allowed.";
            exit;
        }
    }elseif ($group_id && $message !== '') {
        $stmt = $conn->prepare( "INSERT INTO group_messages (group_id, user_id, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $group_id, $user_id, $message);
        $stmt->execute();
        $stmt->close();
        // Redirect to the group chat page after sending the message 
        header( "Location: group.php?group_id=$group_id");
        exit;
    } else {
        echo "Group ID and message cannot be empty. ";
    }
} else {
    echo  "Invalid request method. ";
}
?>
