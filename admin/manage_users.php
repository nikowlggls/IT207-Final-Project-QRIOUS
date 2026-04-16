<?php
session_start();
require_once '../dbconnect.php';

// Security Check: Siguradong Admin lang ang pwedeng makakita nito
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin';
$msg = "";

if (isset($_POST['add_instructor'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = "Instructor";

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "⚠ USERNAME ALREADY EXISTS!";
    } else {
        $sql = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$password', '$role')";
        if (mysqli_query($conn, $sql)) {
            $msg = "✅ INSTRUCTOR ACCOUNT CREATED!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Q-RIOUS</title>
    <style>
        * {
            cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5e6e0;
            background-image: linear-gradient(#e8d6cf 1px, transparent 1px),
                              linear-gradient(90deg, #e8d6cf 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* 1. FIXED TOP BAR */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: #e3a693;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            border-bottom: 4px solid #c98f7a;
            z-index: 1000;
        }

        .logo { color: white; font-weight: bold; font-size: 24px; letter-spacing: 5px; text-shadow: 2px 2px 0 #c98f7a; }

        .logout-btn {
            background: #fdfaf9;
            color: #c98f7a;
            padding: 8px 15px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11px;
            border: 2px solid #c98f7a;
            box-shadow: 3px 3px 0 #c98f7a;
        }

        /* 2. SIDEBAR */
        .sidebar {
            width: 250px;
            background: #fdfaf9;
            border-right: 4px solid #c98f7a;
            position: fixed;
            top: 70px;
            bottom: 0;
            padding: 30px 15px;
            z-index: 999;
        }

        .nav-item {
            display: block;
            padding: 15px;
            color: #5d4037;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 10px;
            border: 2px solid transparent;
            font-size: 13px;
        }

        .nav-item:hover, .nav-item.active {
            background: #fff;
            border-color: #e8d6cf;
            box-shadow: 4px 4px 0 #e8d6cf;
            transform: translate(-2px, -2px);
        }

        .nav-item.active { background: #e3a693; color: white; border-color: #c98f7a; box-shadow: 4px 4px 0 #c98f7a; }

        /* 3. MAIN CONTENT */
        .main-content {
            margin-top: 70px;
            margin-left: 250px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* REGISTER BOX WITH FLOATING ANIMATION */
        .manage-box {
            width: 100%;
            max-width: 600px;
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            box-shadow: 10px 10px 0 #c98f7a;
            padding: 30px;
            animation: float 4s ease-in-out infinite;
            margin-bottom: 40px;
        }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        h2 { color: #5d4037; text-align: center; border-bottom: 2px solid #e8d6cf; padding-bottom: 10px; margin-bottom: 20px; font-size: 18px; }

        input { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #e8d6cf; font-family: inherit; }

        button { width: 100%; padding: 15px; background: #e3a693; color: white; border: none; font-weight: bold; cursor: pointer; box-shadow: 0 4px 0 #c98f7a; }

        /* TABLE STYLES */
        .table-wrapper { width: 100%; max-width: 800px; overflow-x: auto; background: white; border: 3px solid #c98f7a; box-shadow: 8px 8px 0 #e8d6cf; }
        .user-list { width: 100%; border-collapse: collapse; min-width: 500px; }
        th, td { border: 1px solid #e8d6cf; padding: 12px; text-align: left; font-size: 13px; }
        th { background: #e3a693; color: white; text-transform: uppercase; }
        tr:nth-child(even) { background: #fdfaf9; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; top: 70px; border-right: none; display: flex; overflow-x: auto; padding: 10px; }
            .nav-item { margin-bottom: 0; margin-right: 10px; white-space: nowrap; }
            .main-content { margin-left: 0; padding-top: 90px; }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="logo">Q-RIOUS</div>
        <div class="profile-section">
            <a href="../logout.php" class="logout-btn">LOGOUT</a>
        </div>
    </header>

    <nav class="sidebar">
        <a href="admin_dashboard.php" class="nav-item">DASHBOARD</a>
        <a href="manage_users.php" class="nav-item active">MANAGE USERS</a>
        <a href="manage_quizzes.php" class="nav-item">MANAGE QUIZZES</a>
        <a href="system_settings.php" class="nav-item">SETTINGS</a>
        <a href="reports.php" class="nav-item">VIEW REPORTS</a>
    </nav>

    <main class="main-content">
        <div class="manage-box">
            <h2>CREATE INSTRUCTOR ACCOUNT</h2>
            <?php if($msg) echo "<p style='color: #d28d77; text-align:center; font-weight:bold; margin-bottom:10px;'>$msg</p>"; ?>
            <form method="POST">
                <input type="text" name="fullname" placeholder="FULL NAME (e.g. Prof. Juan)" required>
                <input type="text" name="username" placeholder="USERNAME" required>
                <input type="password" name="password" placeholder="PASSWORD" required>
                <button type="submit" name="add_instructor">REGISTER INSTRUCTOR</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="user-list">
                <thead>
                    <tr>
                        <th>FULL NAME</th>
                        <th>USERNAME</th>
                        <th>ROLE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['fullname']) . "</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['role']) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>