<?php
require_once('./admin/db_connect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Step 1: Check if the username exists using prepared statements
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Username exists, verify password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, check if the user is suspended
                if ($user['status'] === '(suspended)') {
                    // User is suspended, redirect to suspended page
                    session_start();
                    $_SESSION['username'] = $username;
                    echo "<script>window.location.href = 'suspended.php';</script>";
                    exit();
                } else {
                    // Password is correct, set session variables
                    session_start();
                $_SESSION['username'] = $username;
                $_SESSION['user_logged_in'] = true;
                header("Location: ./admin/posts.php");
                exit();
                }
            } else {
                echo "<script>alert('Invalid password.');</script>";
            }
        } else {
            echo "<script>alert('Username does not exist.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="../media/icon.png" type="image/png">
</head>

<body>
    <canvas id="bg-canvas"></canvas>
    <div class="login-container">
        <img src="../media/icon.png" alt="logo" class="logo ">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?> " method="post">
            <label for="userid">Username</label>
            <input type="text" id="username" name="username" required autocomplete="username">
            <label for="password">Passkey</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            <div id="show-password-container">
                <h5><input type="checkbox" id="show-password" onclick="togglePassword()">Show password</h5>
            </div>
            
            <script>
                function togglePassword() {
                    const pwd = document.getElementById('password');
                    pwd.type = pwd.type === 'password' ? 'text' : 'password';
                }
            </script>
            <span style="color: rgb(116, 28, 28); " title="Hint">&#9888; The username might be your id number</span>
            <button type="submit" name="submit">Sign In</button>
        </form>
    </div>
    <script>
        // Abstract animated background: floating colorful circles
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let w, h;

        function resize() {
            w = window.innerWidth;
            h = window.innerHeight;
            canvas.width = w;
            canvas.height = h;
        }
        window.addEventListener('resize', resize);
        resize();

        // Circle properties
        const colors = ['#e43f5a', '#9daaf2', '#f4eeff', '#16213e', '#21e6c1'];
        const circles = [];
        for (let i = 0; i < 32; i++) {
            circles.push({
                x: Math.random() * w,
                y: Math.random() * h,
                r: 30 + Math.random() * 40,
                dx: (Math.random() - 0.5) * 1.2,
                dy: (Math.random() - 0.5) * 1.2,
                color: colors[Math.floor(Math.random() * colors.length)],
                alpha: 0.15 + Math.random() * 0.15
            });
        }

        function animate() {
            ctx.clearRect(0, 0, w, h);
            for (let c of circles) {
                ctx.globalAlpha = c.alpha;
                ctx.beginPath();
                ctx.arc(c.x, c.y, c.r, 0, 2 * Math.PI);
                ctx.fillStyle = c.color;
                ctx.fill();
                c.x += c.dx;
                c.y += c.dy;
                // Bounce off edges
                if (c.x - c.r < 0 || c.x + c.r > w) c.dx *= -1;
                if (c.y - c.r < 0 || c.y + c.r > h) c.dy *= -1;
            }
            ctx.globalAlpha = 1.0;
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>

</html>