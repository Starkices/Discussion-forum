<?php
require 'db_connect.php';
session_start();
$adminName = $_SESSION['username'] ?? $_SESSION['admin_name'];

// Suspension check (same as above)
$username = $_SESSION['username'] ?? $_SESSION['admin_name'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if ($user['status'] === '(suspended)') {
    echo "<script>window.location.href = 'suspended.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Start a Discussion</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        ol { list-style-type: decimal; margin-left: 20px; }
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
        <h1>How to Start a Discussion</h1>
        <p>Starting a discussion in our forum is easy and helps connect teachers and students. Use these tips to create engaging posts or group chats, like on Facebook or WhatsApp.</p>
        
        <h2>Steps to Get Started</h2>
        <ol>
            <li><strong>Choose a Topic</strong>: Pick something school-related, like a class question or project idea. Align it with learning objectives.</li>
            <li><strong>Craft a Clear Title</strong>: Use keywords, e.g., "Math Help: Solving Equations" to attract replies.</li>
            <li><strong>Write Your Post</strong>: Keep it short and purposeful. Ask open-ended questions to spark debate, like "What do you think about...?"</li>
            <li><strong>Add Media</strong>: Upload images or links if relevant, but keep it appropriate.</li>
            <li><strong>Post and Engage</strong>: Share in the main feed or a group. Reply to comments to keep the conversation going.</li>
            <li><strong>Be Creative</strong>: Use polls, threads, or role-playing for fun discussions.</li>
        </ol>
        
        <h2>Tips for Success</h2>
        <ul>
            <li>Encourage peers to join by tagging them.</li>
            <li>Monitor for replies and build on ideas.</li>
            <li>Avoid one-word posts—aim for thoughtful input.</li>
        </ul>
        
        <a href="resources.php" class=" btn btn-primary mt-2"><i class="fas fa-arrow-left"></i> Back to Resources</a>
    </div>
</body>
</html>