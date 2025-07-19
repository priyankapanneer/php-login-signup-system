<?php
// dashboard.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to auth.php if not logged in
    header("Location: auth.php");
    exit();
}

// Get username from session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | User Auth System</title>
    <style>
        body {
            background: linear-gradient(to right, #c2e9fb, #81a4fd);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            text-align: center;
            width: 400px;
        }
        h1 {
            color: #4a47a3;
            margin-bottom: 10px;
        }
        p {
            color: #555;
            margin-bottom: 30px;
        }
        a.logout-btn {
            background: #4a47a3;
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        a.logout-btn:hover {
            background: #3a378b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>! 👋</h1>
        <p>You have successfully logged in to your dashboard.</p>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
