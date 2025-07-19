<?php
// auth.php (Handles both Signup & Login in a single page)

// Start session
session_start();

// Database connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "user_auth";

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$signup_error = "";
$signup_success = "";
$login_error = "";

// Handle Signup
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $signup_error = "⚠️ All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "⚠️ Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $signup_error = "⚠️ Passwords do not match!";
    } else {
        // Check if user exists
        $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $signup_error = "⚠️ Username or Email already exists!";
        } else {
            // Hash and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($conn, $insert_query)) {
                $signup_success = "🎉 Registration successful! Please login.";
            } else {
                $signup_error = "❌ Something went wrong. Try again.";
            }
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $username_email = mysqli_real_escape_string($conn, $_POST['username_email']);
    $password = $_POST['password'];

    // Validation
    if (empty($username_email) || empty($password)) {
        $login_error = "⚠️ All fields are required!";
    } else {
        $query = "SELECT * FROM users WHERE username='$username_email' OR email='$username_email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
                header("Location: dashboard.php");
                exit();
            } else {
                $login_error = "❌ Invalid password!";
            }
        } else {
            $login_error = "❌ User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auth | User System</title>
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 400px;
            position: relative;
        }
        h2 {
            text-align: center;
            color: #ff6f61;
            margin-bottom: 20px;
        }
        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .tab {
            cursor: pointer;
            font-weight: bold;
            color: #ff6f61;
        }
        .tab.active {
            border-bottom: 2px solid #ff6f61;
        }
        form {
            display: none;
        }
        form.active {
            display: block;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        input[type="submit"] {
            background: #ff6f61;
            color: #fff;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background: #e65c50;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        function showTab(tabName) {
            var tabs = document.getElementsByClassName('tab');
            var forms = document.getElementsByTagName('form');

            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            for (var i = 0; i < forms.length; i++) {
                forms[i].classList.remove('active');
            }

            document.getElementById(tabName + '-tab').classList.add('active');
            document.getElementById(tabName + '-form').classList.add('active');
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>User Authentication</h2>
        <div class="tabs">
            <div class="tab active" id="signup-tab" onclick="showTab('signup')">Sign Up</div>
            <div class="tab" id="login-tab" onclick="showTab('login')">Login</div>
        </div>

        <!-- Signup Form -->
        <form id="signup-form" class="active" method="POST" action="">
            <?php if ($signup_error): ?>
                <div class="message error"><?php echo $signup_error; ?></div>
            <?php elseif ($signup_success): ?>
                <div class="message success"><?php echo $signup_success; ?></div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" name="signup" value="Sign Up">
        </form>

        <!-- Login Form -->
        <form id="login-form" method="POST" action="">
            <?php if ($login_error): ?>
                <div class="message error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <input type="text" name="username_email" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="login" value="Login">
        </form>
    </div>
</body>
</html>
