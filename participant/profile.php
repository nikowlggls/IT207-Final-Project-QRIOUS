<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once '../dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. User Info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// 2. Stats
$stats_stmt = $conn->prepare("SELECT COUNT(*) as total_taken, SUM(score) as total_points FROM quiz_attempts WHERE user_id = ?");
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

$total_taken = $stats['total_taken'] ?? 0;
$total_points = $stats['total_points'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-RIOUS | My Profile</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important; }
        
        body { 
            font-family: 'Courier New', monospace; 
            background-color: #f5e6e0; 
            background-image: linear-gradient(#e8d6cf 1px, transparent 1px),
                              linear-gradient(90deg, #e8d6cf 1px, transparent 1px);
            background-size: 25px 25px;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px; 
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .profile-wrapper {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr; /* Default: Single Column for Mobile */
            gap: 30px;
            animation: float 5s ease-in-out infinite;
        }

        /* Desktop View (Two Columns) */
        @media (min-width: 768px) {
            .profile-wrapper {
                grid-template-columns: 300px 1fr;
            }
        }

        /* LEFT SIDE: Identity Card */
        .identity-card {
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 8px 8px 0 #c98f7a;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .avatar { 
            width: 120px; height: 120px; 
            background: #e3a693; 
            border-radius: 50%; 
            margin: 0 auto 20px; 
            display: flex; justify-content: center; align-items: center; 
            font-size: 50px; color: white; 
            border: 5px solid #fff; 
            box-shadow: 0 10px 20px rgba(201, 143, 122, 0.3);
        }

        /* RIGHT SIDE: Details */
        .details-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background: #fff;
            border: 3px solid #c98f7a;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 8px 8px 0 #c98f7a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .stat-box {
            background: #fdfaf9;
            border: 2px solid #e8d6cf;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-box span { display: block; font-size: 28px; font-weight: bold; color: #c98f7a; }
        .stat-box label { font-size: 11px; font-weight: bold; color: #8d6e63; text-transform: uppercase; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .info-item label { display: block; font-size: 10px; color: #c98f7a; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .info-item span { font-size: 16px; color: #5d4037; font-weight: bold; }

        /* BUTTONS */
        .btn-group { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }

        .btn {
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            font-family: inherit;
            transition: 0.3s;
        }

        .btn-edit { background: #e3a693; color: white; box-shadow: 4px 4px 0 #c98f7a; }
        .btn-back { background: transparent; color: #c98f7a; border: 2px solid #c98f7a; }

        .btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 #c98f7a; }
    </style>
</head>
<body>

<div class="profile-wrapper">
    <div class="identity-card">
        <div class="avatar"><?php echo strtoupper(substr($user['fullname'], 0, 1)); ?></div>
        <h2 style="color: #5d4037; margin-bottom: 5px;"><?php echo htmlspecialchars($user['fullname']); ?></h2>
        <p style="font-size: 13px; color: #a67c6d; margin-bottom: 25px;">@<?php echo htmlspecialchars($user['username']); ?></p>
        
        <div class="btn-group">
            <a href="edit_profile.php" class="btn btn-edit">EDIT PROFILE</a>
            <a href="participant_dashboard.php" class="btn btn-back">DASHBOARD</a>
        </div>
    </div>

    <div class="details-container">
        <div class="info-card">
            <h3 style="font-size: 12px; color: #c98f7a; margin-bottom: 15px; letter-spacing: 2px;">PERFORMANCE STATS</h3>
            <div class="stats-grid">
                <div class="stat-box">
                    <span><?php echo $total_taken; ?></span>
                    <label>Quizzes Taken</label>
                </div>
                <div class="stat-box">
                    <span><?php echo $total_points; ?></span>
                    <label>Total Points</label>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3 style="font-size: 12px; color: #c98f7a; margin-bottom: 15px; letter-spacing: 2px;">PERSONAL INFORMATION</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Section</label>
                    <span><?php echo htmlspecialchars($user['section'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <label>Birthday</label>
                    <span><?php echo !empty($user['birthday']) ? date('M d, Y', strtotime($user['birthday'])) : 'Not set'; ?></span>
                </div>
                <div class="info-item">
                    <label>Contact No.</label>
                    <span><?php echo htmlspecialchars($user['contact_no'] ?? 'Not set'); ?></span>
                </div>
                <div class="info-item">
                    <label>Email Address</label>
                    <span style="font-size: 14px;"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>