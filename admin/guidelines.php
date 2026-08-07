<?php
require 'db_connect.php';
session_start();
$adminName = $_SESSION['username'] ?? $_SESSION['admin_name'];

// Fetch user and check suspension (as per previous updates)
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
    <title>Forum Guidelines</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        ul { list-style-type: disc; margin-left: 20px; }
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
    <h1>Forum Guidelines</h1>
    <p>Welcome to our school discussion forum! These guidelines help keep discussions respectful, productive, and safe for teachers and students. Follow them to make the most of our Facebook-like posts and WhatsApp-style groups.</p>
    
    <h2>Key Rules</h2>
    <ul>
        <li><strong>Stay on Topic</strong>: Stick to school-related discussions, like class topics, resources, or group projects. Avoid personal attacks or off-topic posts.</li>
        <li><strong>Be Respectful</strong>: Treat everyone kindly—no bullying, harassment, or discriminatory language. Encourage positive interactions and model civil disagreement.</li>
        <li><strong>Participate Actively</strong>: Reply to at least two peers per discussion to build community. Use clear, concise posts. Instructors' involvement boosts enthusiasm.</li>
        <li><strong>No Spam or Ads</strong>: Don't post irrelevant links, promotions, or repetitive content.</li>
        <li><strong>Protect Privacy</strong>: Don't share personal info without consent. Report any concerns to admins.</li>
        <li><strong>Follow School Policies</strong>: All content must align with school rules—no cheating, plagiarism, or inappropriate media.</li>
        <li><strong>Be Patient and Thorough</strong>: Read the entire thread before replying to avoid repetition. Assign discussion leaders on a rotating basis for engagement.</li>
        <li><strong>Establish Ground Rules</strong>: At the start of groups or threads, set expectations for honesty, confidentiality, and focus.</li>
    </ul>
    
    <h2>Consequences</h2>
    <p>Violations may lead to warnings, post removal, or suspension. Contact admins if you see issues. Be present and approachable as a user or teacher.</p>
    
    <a href="resources.php" class=" btn btn-primary mt-2" ><i class="fas fa-arrow-left"></i> Back to Resources</a>
</div> 
</body>
</html>