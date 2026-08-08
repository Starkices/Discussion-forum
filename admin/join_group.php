<?php
// join_group.php

session_start();
require_once 'db_connect.php';

// Where to send the user back to after any outcome
define('GROUPS_PAGE', 'groups.php');

function redirect_with_message(string $message): void {
    header('Location: ' . GROUPS_PAGE . '?msg=' . urlencode($message));
    exit();
}

// Determine login state safely (isset() first avoids "undefined array key" warnings)
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$isUser = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

if (!$isAdmin && !$isUser) {
    header('Location: ../login.php');
    exit();
}

// Resolve the current user's id
$username = $_SESSION['username'] ?? $_SESSION['admin_name'] ?? null;
$user_id = null;
if ($username !== null) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
}

if ($user_id === null) {
    redirect_with_message("Could not find your user account.");
}

// Accept either ?join_group_id= or ?id= so this works with either link style
$rawGroupId = $_GET['join_group_id'] ?? $_GET['id'] ?? null;
if ($rawGroupId === null) {
    redirect_with_message("No group specified.");
}
$group_id = intval($rawGroupId);

// Look up the group, including its password hash and member cap
$stmt = $conn->prepare("SELECT password, max_members FROM groups WHERE id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$stmt->bind_result($group_password_hash, $maxMembers);
$found = $stmt->fetch();
$stmt->close();

if (!$found) {
    redirect_with_message("Group not found.");
}

// Check the group password, if one is set
if (!empty($group_password_hash)) {
    $input_password = $_GET['group_password'] ?? '';
    if (!password_verify($input_password, $group_password_hash)) {
        redirect_with_message("Incorrect group password.");
    }
}

// Already a member?
$stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $group_id, $user_id);
$stmt->execute();
$stmt->store_result();
$alreadyMember = $stmt->num_rows > 0;
$stmt->close();

if ($alreadyMember) {
    redirect_with_message("You are already a member of this group.");
}

// Enforce the member cap, if one is set
if ($maxMembers > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($currentMembers);
    $stmt->fetch();
    $stmt->close();

    if ($currentMembers >= $maxMembers) {
        redirect_with_message("Group is full. Cannot join.");
    }
}

// Join the group
$stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $group_id, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    redirect_with_message("Successfully joined the group.");
} else {
    $error = $stmt->error;
    $stmt->close();
    redirect_with_message("Error joining group: " . $error);
}