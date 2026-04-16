<?php
session_start();
require_once '../dbconnect.php';

// Security Check: Siguradong Admin lang ang pwedeng makakita nito
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

// Display Name base sa database
$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Q-RIOUS | Admin Dashboard</title>

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
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .logo {
            color: white;
            font-weight: bold;
            font-size: clamp(18px, 4vw, 24px);
            letter-spacing: 5px;
            text-shadow: 2px 2px 0 #c98f7a;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
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
            transition: 0.2s;
        }

        .logout-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0 #c98f7a;
        }

        /* 2. SIDEBAR - consistent with other admin pages */
        .sidebar {
            width: 250px;
            background: #fdfaf9;
            border-right: 4px solid #c98f7a;
            position: fixed;
            top: 70px;
            bottom: 0;
            padding: 30px 15px;
            z-index: 999;
            transition: 0.3s;
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
            transition: 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            background: #fff;
            border-color: #e8d6cf;
            box-shadow: 4px 4px 0 #e8d6cf;
            transform: translate(-2px, -2px);
        }

        .nav-item.active {
            background: #e3a693;
            color: white;
            border-color: #c98f7a;
            box-shadow: 4px 4px 0 #c98f7a;
        }

        /* 3. MAIN CONTENT */
        .main-content {
            margin-top: 70px;
            margin-left: 250px;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            min-height: calc(100vh - 70px);
            transition: 0.3s;
        }

        /* MAS MALAKING CONTAINER */
        .dashboard-container {
            width: 100%;
            max-width: 800px;
            text-align: center;
            padding: 20px;
        }

        /* HEADER TEXT - MAS MALAKI */
        .dashboard-container h1 {
            color: #5d4037;
            letter-spacing: 3px;
            margin-bottom: 10px;
            font-size: 32px;
            text-shadow: 2px 2px 0 rgba(201, 143, 122, 0.3);
        }

        .dashboard-container p {
            color: #c98f7a;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            margin-bottom: 50px;
        }

        /* VERTICAL CARDS - MAS MALAKI AT MAALAT */
        .card-grid {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
        }

        .card {
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            padding: 35px 25px;
            transition: 0.3s;
            box-shadow: 10px 10px 0 #c98f7a;
            cursor: pointer;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }

        .card:hover {
            transform: translate(-4px, -4px);
            box-shadow: 15px 15px 0 #c98f7a;
        }

        .card h3 {
            color: #5d4037;
            font-size: 24px;
            margin-bottom: 12px;
            letter-spacing: 2px;
        }

        .card p {
            color: #c98f7a;
            font-size: 13px;
            text-transform: none;
            letter-spacing: 0;
            margin-bottom: 0;
        }

        /* 4. DEVICE FRIENDLY ADJUSTMENTS */
        @media (max-width: 768px) {
            /* Sidebar becomes horizontal navigation at the top */
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 70px;
                border-right: none;
                border-bottom: 4px solid #c98f7a;
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
            .dashboard-container {
                padding: 15px;
                max-width: 100%;
            }
            .dashboard-container h1 {
                font-size: 24px;
            }
            .dashboard-container p {
                font-size: 12px;
                margin-bottom: 35px;
            }
            .card {
                padding: 25px 20px;
            }
            .card h3 {
                font-size: 20px;
            }
            .card p {
                font-size: 11px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px 12px;
            }
            .dashboard-container {
                padding: 10px;
            }
            .dashboard-container h1 {
                font-size: 20px;
            }
            .dashboard-container p {
                font-size: 10px;
                margin-bottom: 30px;
            }
            .card-grid {
                gap: 18px;
            }
            .card {
                padding: 20px 15px;
            }
            .card h3 {
                font-size: 18px;
            }
            .card p {
                font-size: 10px;
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
        <a href="admin_dashboard.php" class="nav-item active">DASHBOARD</a>
        <a href="manage_users.php" class="nav-item">MANAGE USERS</a>
        <a href="manage_quizzes.php" class="nav-item">MANAGE QUIZZES</a>
        <a href="system_settings.php" class="nav-item">SETTINGS</a>
        <a href="reports.php" class="nav-item">VIEW REPORTS</a>
    </nav>

    <main class="main-content">
        <div class="dashboard-container">
            <h1>⚡ ADMIN DASHBOARD</h1>
            <p>System Control Center</p>

            <div class="card-grid">
                <div class="card" onclick="location.href='manage_users.php';">
                    <h3>👥 MANAGE USERS</h3>
                    <p>Control user accounts</p>
                </div>
                <div class="card" onclick="location.href='manage_quizzes.php';">
                    <h3>📋 MANAGE QUIZZES</h3>
                    <p>Edit quiz content</p>
                </div>
                <div class="card" onclick="location.href='system_settings.php';">
                    <h3>⚙️ SETTINGS</h3>
                    <p>Configuration</p>
                </div>
                <div class="card" onclick="location.href='reports.php';">
                    <h3>📊 VIEW REPORTS</h3>
                    <p>System analytics</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>