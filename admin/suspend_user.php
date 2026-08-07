<?php
// suspend_user.php

// Database connection (update with your credentials)
include 'db_connect.php';

// Get user ID from POST
$user_id = isset($_POST['username']);
if (!$user_id) {
    die('No username provided.');
}

// Calculate suspension end date (1 month from now)
$suspend_date = new DateTime();
//convert to string
$suspend_date = $suspend_date->format('Y-m-d H:i:s');

// Update user status in the database
$username = $_POST['username'];

$suspend_until = (new DateTime())->modify('+1 month')->format('Y-m-d H:i:s');


// Prepare the suspended username
$suspended = '(suspended)';

// Prepare and execute the update query
$stmt = $conn->prepare("UPDATE users SET status = ?, created_at = ? WHERE username = ?");
$status = 'suspended';
$stmt->bind_param("sss", $suspended,  $suspend_date, $username);

if ($stmt->execute()) {
    echo "<script>alert('User suspended until $suspend_until.')</script>";
    echo "<script>window.location.href = 'delete_user.php?username=" . $username . "';</script>";
} else {
    echo "Error suspending user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>