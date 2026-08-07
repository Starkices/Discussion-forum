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
    <title>FAQ</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        details { margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php include 'interface.php'; ?>
    <div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
        <?php
// ... (keep existing session and suspension check)

// Fetch dynamic FAQs
$result = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
?>

<!-- ... -->

<div class="main-content" style="margin-top:70px; max-width: 800px; margin-left:auto; margin-right:auto;">
    <h1>Frequently Asked Questions (FAQ)</h1>
    <p>Find answers to common questions about our school discussion forum. If you need more help, contact an admin.</p>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <details>
                <summary><?php echo htmlspecialchars($row['question']); ?></summary>
                <p><?php echo nl2br(htmlspecialchars($row['answer'])); ?></p>
            </details>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No FAQs available yet.</p>
    <?php endif; ?>
    
    <!-- Static fallback or additional ones if needed -->
    <details>
        <summary>How do I create a Q&A forum?</summary>
        <p>Use the groups feature to set up a dedicated space for questions, monitored by teachers.</p>
    </details>
    
    <details>
        <summary>What's the purpose of discussions?</summary>
        <p>They provide opportunities to interact with content, peers, and instructors for better learning.</p>
    </details>
    
    <details>
        <summary>Can I participate without skipping?</summary>
        <p>Yes, engage regularly—don't just talk before/after; use the forum for ongoing chats.</p>
    </details>
    
    <a href="resources.php" class=" btn btn-primary mt-2"><i class="fas fa-arrow-left"></i> Back to Resources</a>
</div>
</body>
</html> 