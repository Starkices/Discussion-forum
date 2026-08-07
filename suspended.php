<?php

include './admin/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $suspended_date = new DateTime($row['created_at']);
    $status = $row['status'];
    $suspend_expiration = $suspended_date->modify('+1 month')->format('Y-m-d H:i:s'); // Assuming suspension lasts for 1 month
    // Assuming 'created_at' is used for suspension expiration, adjust as necessary

    //check if the account suspension is still valid
    $current_date = new DateTime();
    if ($current_date > $suspended_date) {
        $suspended = false; // Suspension has expired
        // Optionally, you can update the status in the database to null
        $update_sql = "UPDATE users SET status = NULL WHERE username = '$username'";
        mysqli_query($conn, $update_sql);

        // Optionally, you can redirect to a different page or show a message
        header("Location: login.php?message=Your suspension has expired. You can log in again.");
        exit();

    } else {
        $suspended = true; // Suspension is still valid
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Suspended</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8d7da; color: #721c24; text-align: center; padding-top: 50px; }
        .container { background: #fff; border: 1px solid #f5c6cb; display: inline-block; padding: 30px 50px; border-radius: 8px; }
        h1 { margin-bottom: 20px; }
        .date { font-weight: bold; color: #b71c1c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Account Suspended</h1>
        <p>Your account has been suspended.</p>
        <?php if ($suspended && $suspend_expiration): ?>
            <p>Suspend expiration date: <span class="date"><?php echo htmlspecialchars($suspend_expiration); ?></span></p>
        <?php endif; ?>
        <p>If you believe this is a mistake, please contact support.</p>
    </div>
</body>
</html>