<?php
session_start();
require_once '../dbconnect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Participant') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Participant';

$stats_query = $conn->prepare("SELECT COUNT(*) as total_taken, SUM(score) as total_points FROM quiz_attempts WHERE user_id = ?");
$stats_query->bind_param("i", $user_id);
$stats_query->execute();
$stats = $stats_query->get_result()->fetch_assoc();

$total_points = $stats['total_points'] ?? 0;
$total_taken = $stats['total_taken'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-RIOUS | Student Dashboard</title>
    <style>
        * {
            box-sizing: border-box; 
            margin: 0; 
            padding: 0;
            cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5e6e0;
            background-image: linear-gradient(#e8d6cf 1px, transparent 1px),
                              linear-gradient(90deg, #e8d6cf 1px, transparent 1px);
            background-size: 25px 25px;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* FLOATING ANIMATION KEYFRAMES */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* TOP NAVIGATION */
        .navbar {
            background: #fdfaf9;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5%;
            border-bottom: 3px solid #c98f7a;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(201, 143, 122, 0.2);
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #c98f7a;
            letter-spacing: 4px;
        }

        .nav-links { display: flex; gap: 30px; }
        .nav-links a {
            text-decoration: none;
            color: #5d4037;
            font-weight: bold;
            font-size: 13px;
            transition: 0.3s;
        }
        .nav-links a:hover { color: #e3a693; border-bottom: 2px solid #e3a693; }

        /* USER DROPDOWN */
        .user-menu { position: relative; display: inline-block; }
        .user-btn {
            background: #e3a693;
            color: white;
            padding: 8px 18px;
            border: 2px solid #c98f7a;
            font-family: inherit;
            font-weight: bold;
            box-shadow: 4px 4px 0 #c98f7a;
        }

        .dropdown-box {
            display: none;
            position: absolute;
            right: 0;
            background: #fdfaf9;
            min-width: 180px;
            border: 3px solid #c98f7a;
            box-shadow: 6px 6px 0 #c98f7a;
            margin-top: 10px;
            animation: float 3s ease-in-out infinite; /* Floating effect for dropdown too */
        }

        .dropdown-box a {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            color: #5d4037;
            font-weight: bold;
            font-size: 12px;
        }
        .dropdown-box a:hover { background: #e8d6cf; }
        .user-menu:hover .dropdown-box { display: block; }

        /* MAIN CONTENT AREA */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .welcome-header {
            text-align: left;
            margin-bottom: 40px;
            border-left: 6px solid #c98f7a;
            padding-left: 20px;
            /* Header Float */
            animation: float 4s ease-in-out infinite;
        }

        .welcome-header h1 { font-size: 32px; color: #5d4037; text-transform: uppercase; }

        /* DASHBOARD GRID (LANDSCAPE CARDS) */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .card {
            background: #fff;
            border: 3px solid #c98f7a;
            padding: 30px 40px;
            box-shadow: 8px 8px 0 #c98f7a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
            /* Apply Floating Animation */
            animation: float 5s ease-in-out infinite;
        }

        /* Iba-ibahin ang bilis para hindi sabay-sabay lumutang (more natural) */
        .card:nth-child(2) { animation-delay: 0.5s; }
        .card:nth-child(3) { animation-delay: 1s; }
        .card:nth-child(4) { animation-delay: 1.5s; }

        .card:hover { 
            transform: translateX(10px) scale(1.02);
            background: #fffafa;
            animation-play-state: paused; /* Stop floating when hovered */
        }

        .stat-group { display: flex; flex-direction: column; }
        .stat-label { font-size: 13px; color: #8d6e63; font-weight: bold; text-transform: uppercase; }
        .stat-val { font-size: 42px; font-weight: bold; color: #c98f7a; }

        .action-card { text-decoration: none; cursor: pointer; }
        .action-info h3 { color: #5d4037; font-size: 22px; }
        .action-info p { font-size: 13px; color: #8d6e63; }

        .icon-box {
            font-size: 30px;
            background: #f5e6e0;
            width: 60px; height: 60px;
            display: flex; justify-content: center; align-items: center;
            border-radius: 50%;
            border: 2px solid #e3a693;
        }

        @media (min-width: 900px) {
            .dashboard-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 600px) {
            .nav-links { display: none; }
            .card { flex-direction: column; text-align: center; gap: 15px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="brand">Q-RIOUS</div>
    <div class="nav-links">
        <a href="participant_dashboard.php">DASHBOARD</a>
        <a href="available_quizzes.php">QUIZZES</a>
        <a href="my_results.php">GRADES</a>
    </div>
    <div class="user-menu">
        <button class="user-btn">HI, <?php echo htmlspecialchars($userName); ?>! ▼</button>
        <div class="dropdown-box">
            <a href="profile.php">👤 MY PROFILE</a>
            <a href="settings.php">⚙️ SETTINGS</a>
            <hr style="border: 1px solid #e8d6cf; margin: 5px 0;">
            <a href="../logout.php" style="color: #d84315;">🚪 LOGOUT</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-header">
        <h1>Student Portal</h1>
        <p style="color: #8d6e63;">Monitoring your progress and achievements.</p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="stat-group">
                <span class="stat-label">Total Earned Points</span>
                <span class="stat-val"><?php echo $total_points; ?></span>
            </div>
            <div class="icon-box">🏆</div>
        </div>

        <div class="card">
            <div class="stat-group">
                <span class="stat-label">Quizzes Completed</span>
                <span class="stat-val"><?php echo $total_taken; ?></span>
            </div>
            <div class="icon-box">📊</div>
        </div>

        <a href="available_quizzes.php" class="card action-card">
            <div class="action-info">
                <h3>TAKE A QUIZ</h3>
                <p>New challenges are waiting for you.</p>
            </div>
            <div class="icon-box" style="background: #e3a693; color: white;">📝</div>
        </a>

        <a href="my_results.php" class="card action-card">
            <div class="action-info">
                <h3>MY GRADES</h3>
                <p>Review your previous performance.</p>
            </div>
            <div class="icon-box" style="background: #e3a693; color: white;">➜</div>
        </a>
    </div>
</div>

</body>
</html>