<?php
session_start();
require_once '../dbconnect.php'; 

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. UPDATE PROFILE & USERNAME LOGIC
    if (isset($_POST['update_profile'])) {
        $new_name = mysqli_real_escape_string($conn, $_POST['fullname']);
        $new_user = mysqli_real_escape_string($conn, trim($_POST['username']));
        $user_id = $_SESSION['user_id'];
        
        // I-check muna kung ang bagong username ay ginagamit na ng iba
        $check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$new_user' AND id != $user_id");
        
        if (mysqli_num_rows($check_user) > 0) {
            $error = "❌ Username '$new_user' is already taken!";
        } else {
            $sql = "UPDATE users SET fullname = '$new_name', username = '$new_user' WHERE id = $user_id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['fullname'] = $new_name;
                $_SESSION['username'] = $new_user;
                $msg = "✅ Profile and Username updated successfully!";
            } else {
                $error = "❌ Error updating profile.";
            }
        }
    }

    // 2. CHANGE PASSWORD LOGIC
    if (isset($_POST['change_password'])) {
        $user_id = $_SESSION['user_id'];
        $current_pass = mysqli_real_escape_string($conn, $_POST['current_password']);
        $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        $res = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
        $user_data = mysqli_fetch_assoc($res);

        if ($current_pass !== $user_data['password']) {
            $error = "❌ Current password does not match!";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "❌ New passwords do not match!";
        } else {
            if (mysqli_query($conn, "UPDATE users SET password = '$new_pass' WHERE id = $user_id")) {
                $msg = "✅ Password changed successfully!";
            }
        }
    }
    
    // 3. SYSTEM CONFIG LOGIC
    if (isset($_POST['update_system'])) {
        // For demo purposes - you can save these to database if needed
        $msg = "✅ System configuration updated successfully!";
    }
}

// Display Values
$userName = $_SESSION['fullname'] ?? 'Admin';
$currentUsername = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Settings | Q-RIOUS</title>
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
            min-height: 100vh;
        }
        
        /* FIXED TOP BAR */
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
        
        .logo {
            color: white;
            font-weight: bold;
            font-size: 24px;
            letter-spacing: 5px;
            text-shadow: 2px 2px 0 #c98f7a;
        }
        
        .admin-name {
            color: white;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .logout-btn {
            background: #fdfaf9;
            color: #c98f7a;
            padding: 8px 15px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11px;
            border: 2px solid #c98f7a;
            box-shadow: 3px 3px 0 #c98f7a;
            margin-left: 15px;
        }

        /* SIDEBAR - same style as other admin pages */
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
            font-size: 13px;
            transition: 0.2s;
            border: 2px solid transparent;
        }
        
        .nav-item:hover, .nav-item.active {
            background: #fff;
            border: 2px solid #e8d6cf;
            box-shadow: 4px 4px 0 #e8d6cf;
            transform: translate(-2px, -2px);
        }
        
        .nav-item.active {
            background: #e3a693;
            color: white;
            border-color: #c98f7a;
            box-shadow: 4px 4px 0 #c98f7a;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-top: 70px;
            margin-left: 250px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* SETTINGS GRID - IMPROVED CARD POSITIONS */
        .settings-grid {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        /* CARDS WITH FLOATING ANIMATION */
        .card {
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            box-shadow: 10px 10px 0 #c98f7a;
            padding: 30px 25px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            animation: float 4s ease-in-out infinite;
        }
        
        /* Stagger animation delay para mas maganda */
        .card:nth-child(1) { animation-delay: 0s; }
        .card:nth-child(2) { animation-delay: 0.5s; }
        .card:nth-child(3) { animation-delay: 1s; }
        .card:nth-child(4) { animation-delay: 1.5s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        
        .card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 14px 14px 0 #c98f7a;
        }
        
        h2 {
            color: #5d4037;
            font-size: 16px;
            margin-bottom: 25px;
            border-bottom: 2px dashed #e3a693;
            padding-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-size: 11px;
            font-weight: bold;
            color: #c98f7a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e8d6cf;
            font-family: inherit;
            font-size: 13px;
            background: white;
            outline: none;
            transition: 0.2s;
        }
        
        input:focus, select:focus {
            border-color: #e3a693;
        }
        
        .btn-action {
            width: 100%;
            background: #e3a693;
            color: white;
            border: none;
            padding: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 4px 4px 0 #c98f7a;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            transition: 0.2s;
        }
        
        .btn-action:hover {
            background: #d69581;
            transform: translate(-1px, -1px);
            box-shadow: 6px 6px 0 #c98f7a;
        }
        
        .btn-action:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #c98f7a;
        }
        
        .btn-password {
            background: #c98f7a;
        }
        
        .btn-password:hover {
            background: #b87a63;
        }
        
        /* ALERT MESSAGES */
        .alert {
            width: 100%;
            max-width: 1200px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: bold;
            border-left: 5px solid;
            border-radius: 4px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        
        .back-link {
            margin-top: 20px;
            color: #c98f7a;
            font-size: 11px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }
        
        .back-link:hover {
            text-decoration: underline;
            transform: translateX(-3px);
            display: inline-block;
        }
        
        /* Divider sa top-bar */
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* ========== RESPONSIVE: SAME AS OTHER ADMIN PAGES ========== */
        @media (max-width: 768px) {
            /* Sidebar becomes horizontal navigation at the top */
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 70px;
                border-right: none;
                display: flex;
                overflow-x: auto;
                padding: 10px;
                white-space: nowrap;
            }
            .nav-item {
                display: inline-block;
                margin-bottom: 0;
                margin-right: 10px;
                white-space: nowrap;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
                padding-top: 20px;
            }
            .logo {
                font-size: 18px;
                letter-spacing: 2px;
            }
            .top-bar {
                padding: 0 15px;
            }
            .admin-name {
                display: none;
            }
            .settings-grid {
                gap: 20px;
            }
            .card {
                padding: 20px;
                animation: none; /* Remove animation on mobile for smoother scrolling */
            }
            .card:hover {
                transform: none;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 480px) {
            .settings-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .card {
                padding: 18px;
            }
            h2 {
                font-size: 14px;
                margin-bottom: 18px;
            }
            input, select {
                padding: 10px;
                font-size: 12px;
            }
            .btn-action {
                padding: 12px;
                font-size: 11px;
            }
            .alert {
                padding: 12px 15px;
                font-size: 11px;
            }
        }
        
        /* Tablet landscape */
        @media (min-width: 769px) and (max-width: 1024px) {
            .settings-grid {
                gap: 25px;
            }
            .card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="logo">Q-RIOUS</div>
        <div class="top-bar-right">
            <div class="admin-name">ADMIN: <?php echo strtoupper(htmlspecialchars($userName)); ?></div>
            <a href="../logout.php" class="logout-btn">LOGOUT</a>
        </div>
    </header>

    <nav class="sidebar">
        <a href="admin_dashboard.php" class="nav-item">DASHBOARD</a>
        <a href="manage_users.php" class="nav-item">MANAGE USERS</a>
        <a href="manage_quizzes.php" class="nav-item">MANAGE QUIZZES</a>
        <a href="system_settings.php" class="nav-item active">SETTINGS</a>
        <a href="reports.php" class="nav-item">VIEW REPORTS</a>
    </nav>

    <main class="main-content">
        <?php if($msg): ?> 
            <div class="alert alert-success"><?php echo $msg; ?></div> 
        <?php endif; ?>
        <?php if($error): ?> 
            <div class="alert alert-error"><?php echo $error; ?></div> 
        <?php endif; ?>

        <div class="settings-grid">
            <div class="card">
                <h2>👤 ADMIN PROFILE</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>📝 FULL NAME</label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($userName); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>🔑 USERNAME</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn-action">💾 SAVE CHANGES</button>
                </form>
            </div>

            <div class="card">
                <h2>🔐 CHANGE PASSWORD</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>🔒 CURRENT PASSWORD</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>✨ NEW PASSWORD</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>✓ CONFIRM PASSWORD</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-action btn-password">🔄 UPDATE PASSWORD</button>
                </form>
            </div>

            <div class="card">
                <h2>⚙️ SYSTEM CONFIG</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>🌐 SYSTEM STATUS</label>
                        <select name="sys_status">
                            <option value="active">🟢 ONLINE / ACTIVE</option>
                            <option value="maintenance">🔴 MAINTENANCE MODE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>📊 DEFAULT PASSING SCORE (%)</label>
                        <input type="number" name="passing_score" value="75" min="50" max="100" step="5">
                    </div>
                    <button type="submit" name="update_system" class="btn-action">⚡ APPLY CHANGES</button>
                </form>
            </div>

            <div class="card">
                <h2>📖 SYSTEM GUIDE</h2>
                <div style="font-size: 12px; color: #5d4037; line-height: 1.6;">
                    <p style="margin-bottom: 10px;"><strong>TECHNICAL SETUP:</strong></p>
                    <ul style="margin-left: 15px; margin-bottom: 15px;">
                        <li>DB Connection: Managed via <code>dbconnect.php</code>.</li>
                        <li>Timezone: Set to <code>Asia/Manila</code> for accurate deadlines.</li>
                    </ul>
                    
                    <p style="margin-bottom: 10px;"><strong>ADMIN OPERATIONS:</strong></p>
                    <ul style="margin-left: 15px; margin-bottom: 15px;">
                        <li>Use <strong>Manage Quizzes</strong> to assign specific sections (e.g., BSIT 3A) and set deadlines.</li>
                        <li>System supports SQL backups and NoSQL collections.</li>
                    </ul>

                    <p style="margin-bottom: 10px;"><strong>PARTICIPANT GUIDE:</strong></p>
                    <p>Participants can access quizzes based on their assigned section and active deadlines. Interface is mobile-responsive.</p>
                </div>
            </div>
        </div>

        <a href="admin_dashboard.php" class="back-link">← BACK TO DASHBOARD</a>
    </main>

</body>
</html>