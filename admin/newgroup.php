<?php
$msg = "";
// Start session to manage user authentication
session_start();
require_once('db_connect.php');
// Example: Check if user is logged in (customize as needed)
$adminName = $_SESSION['admin_name'] ?? $_SESSION['username'];
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    // check if user is suspended
    if ($user['status'] === '(suspended)') {
        // User is suspended, redirect to suspended page
        session_start();
        echo "<script>window.location.href = 'suspended.php';</script>";
        exit();
    }else{}

} else if (!isset($_SESSION['user_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // User is logged in, redirect to user dashboard
    
} else if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])) {
    // Not logged in, redirect to login page
    header('Location: ../login.php');
    exit();
} 


$username = $_SESSION['username'] ?? $_SESSION['admin_name'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $GroupName = mysqli_real_escape_string($conn, $_POST['GroupName']);
    $password = mysqli_real_escape_string($conn, $_POST['password']) ?? '';
    $content = mysqli_real_escape_string($conn, $_POST['content']);
 
    if (empty($GroupName) || empty($content)) {
        $msg = "Please fill in all fields.";
        exit();
    }
    $senderResult = $conn->query("SELECT email FROM users WHERE id = $user_id");
    $senderRow = $senderResult->fetch_assoc(); 
    $senderEmail = $senderRow['email'] ?? '';
    
    
    $recieverResult = $conn->query("SELECT admin_email FROM settings LIMIT 1");
    $recieverRow = $recieverResult->fetch_assoc();
    $recieverEmail = $recieverRow['admin_email'] ?? '';
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $id = 0; // Initialize ID to 0, it will be auto-incremented by the database
    $sql = '';
    // Insert user into the database
    if(empty($password)){
        $sql = "INSERT INTO groups (id, group_name, group_description, created_by, created_at) VALUES ('$id', '$GroupName', '$content', '$user_id', NOW())";
    } else {
        $sql = "INSERT INTO groups (id, group_name, group_description, password, created_by, created_at) VALUES ('$id', '$GroupName', '$content', '$hashedPassword', '$user_id', NOW())";
    }
    if (mysqli_query($conn, $sql)) {
        $msg = "group added successfully.";

        //make sure the email is not as a spam

        //send email message via PHPMailer
        require '../vendor/autoload.php';



        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        // SMTP configuration
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8'; // Set character encoding to UTF-8
        $mail->SMTPDebug = 0; // Disable debug output
        $mail->isHTML(true); // Set email format to HTML
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Host='smtp.gmail.com';
        $mail->SMTPAuth= true;
        $mail->Username   = "$recieverEmail";       // your full Gmail address
        $mail->Password   = 'iujaeonpsimhleqy';          // app password only
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // $mail->SMTPDebug = 2; // or use 3 for even more detail
        // $mail->Debugoutput = 'html'; 
        $mail->setFrom("$senderEmail", 'NIITDF Admin');
        // Add recipient email address
        $mail->addAddress($recieverEmail);

        $mail->Subject = 'Group Created Notification';
        // Customize the email body as needed 
        $mail->Body = "<p>Dear Admin,</p>
                       <p>A new group named <strong>$GroupName</strong> has been created by <strong>$username</strong>.</p>
                       <p>Group Description: $content</p>
                       <p>Best regards,<br>NIITDF Team</p>";
        // Send the email
        if($mail->send()){
            $msg = "group added successfully. Email sent";
        }
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add User</title>
    <link rel="stylesheet" href="adminacc.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="posts.css">
    <style>
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .form-group {
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
            letter-spacing: 0.02em;
        }
        .form-group input {
            width: 95%;
            padding: 10px 12px;
            border: 1.5px solid #bfc9d9;
            border-radius: 6px;
            font-size: 1rem;
            background: #f6f8fa;
            transition: border 0.2s;
        }
        .form-group input:focus {
            border: 1.5px solid #007bff;
            outline: none;
            background: #fff;
        }
        .btn {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
            box-shadow: 0 4px 16px rgba(0,123,255,0.12);
        }
        span {
            color: #e74c3c;
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
            text-align: center;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 0 20px;
            
        }
        .actions a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #f1f1f1;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        footer p {
            margin: 0;
            color: #555;
        }
        #show-password-container {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #show-password {
            width: auto;
            height: auto;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // Include the sidebar
        include 'admininterface.php';
    } else {
        include 'interface.php';
    }
    ?>
    <div class="main-content" style="margin-top:70px;">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <span><?php echo $msg; ?></span>
            <div class="form-group">
                <label for="GroupName">Group Name:</label>
                <input type="text" id="GroupName" name="GroupName" required>
            </div>
            <div class="form-group">
                <label for="password">Password(optional):</label>
                <input type="password" id="password" name="password"  >
                <div id="show-password-container">
                    <input type="checkbox" id="show-password" onclick="togglePassword()">
                    <label for="show-password">Show Password</label>
                </div>
                <script>
                    function togglePassword() {
                        var passwordField = document.getElementById("password");
                        if (passwordField.type === "password") {
                            passwordField.type = "text";
                        } else {
                            passwordField.type = "password";
                        }
                    }
                </script>
            </div>
            <div class="form-group">
                <label for="content">descripion:</label>
                <textarea id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Add group</button>
        </form>
        <div class="actions">
            <?php
        // Check if the user is logged in as an admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Show admin-specific actions
            echo '<a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>'; 
        }
        ?>
            <a href="groups.php" class="btn"><i class="fas fa-users"></i> View groups</a>
        </div>
    </div>
    <p class="footer" style="margin-top: 40px; text-align: center; color: #888;">&copy; <?php echo date('Y'); ?> NIITDF. All rights reserved.</p>
</body>
</html>