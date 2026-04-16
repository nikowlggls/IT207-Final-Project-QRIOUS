<?php
// Enable error reporting for debugging (remove once working)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../dbconnect.php';

// Security Check - Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

// Force no cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin';

// ===========================================
// GAMITIN ANG TAMANG COLUMNS NG users TABLE
// ===========================================

// 1. KUHAIN ANG MGA SUBMISSIONS
$query = "SELECT 
            qa.id as attempt_id,
            qa.user_id,
            qa.quiz_id,
            qa.score,
            qa.total_questions,
            qa.status,
            qa.completed_at,
            u.fullname as student_name,
            qz.quiz_title
          FROM quiz_attempts qa
          LEFT JOIN users u ON qa.user_id = u.id
          LEFT JOIN quizzes qz ON qa.quiz_id = qz.id
          ORDER BY qa.completed_at DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// 2. COMPUTE PARA SA PIE CHART
$stats_query = "SELECT status, COUNT(*) as count FROM quiz_attempts GROUP BY status";
$stats_result = mysqli_query($conn, $stats_query);

$total_submissions = 0;
$passed_count = 0;
$failed_count = 0;

if ($stats_result) {
    while ($row = mysqli_fetch_assoc($stats_result)) {
        $total_submissions += $row['count'];
        if ($row['status'] == 'Passed') {
            $passed_count = $row['count'];
        } else if ($row['status'] == 'Failed') {
            $failed_count = $row['count'];
        }
    }
}

if ($total_submissions > 0) {
    $pass_percentage = round(($passed_count / $total_submissions) * 100, 2);
    $fail_percentage = round(($failed_count / $total_submissions) * 100, 2);
} else {
    $pass_percentage = 0;
    $fail_percentage = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Reports | Q-RIOUS</title>
    <meta http-equiv="cache-control" content="no-cache">
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
        
        /* CONTAINER - with floating animation */
        .container {
            width: 100%;
            max-width: 1200px;
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            box-shadow: 12px 12px 0 #c98f7a;
            padding: 40px;
            animation: float 4s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        
        /* HEADER SECTION */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .title-section h2 {
            color: #5d4037;
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .timestamp {
            font-size: 11px;
            color: #c98f7a;
            font-weight: bold;
        }
        
        .refresh-btn {
            background: #c98f7a;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-family: monospace;
            font-weight: bold;
            font-size: 11px;
            box-shadow: 3px 3px 0 #a06852;
            transition: 0.2s;
        }
        
        .refresh-btn:hover {
            background: #b07a64;
            transform: translate(-1px, -1px);
            box-shadow: 5px 5px 0 #a06852;
        }
        
        /* ANALYTICS GRID - responsive */
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .chart-card {
            background: white;
            border: 2px solid #e8d6cf;
            padding: 25px;
            text-align: center;
            transition: 0.2s;
        }
        
        .chart-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #e8d6cf;
        }
        
        .chart-card strong {
            color: #5d4037;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* PIE CHART */
        .pie-chart {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            margin: 15px auto;
            background: conic-gradient(
                #2ecc71 0% <?php echo $pass_percentage; ?>%, 
                #e74c3c <?php echo $pass_percentage; ?>% 100%
            );
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 12px;
            font-weight: bold;
            flex-wrap: wrap;
        }
        
        .legend span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* BAR CHART */
        .bar-bg {
            height: 40px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 15px;
            border: 1px solid #c98f7a;
        }
        
        .bar-fill {
            height: 100%;
            background: #c98f7a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }
        
        /* TABLE - responsive */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
            border: 2px solid #c98f7a;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 600px;
        }
        
        th {
            background: #e3a693;
            color: white;
            padding: 15px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            border: 1px solid #c98f7a;
        }
        
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #e8d6cf;
            color: #5d4037;
            font-size: 13px;
        }
        
        tr:nth-child(even) {
            background: #faf5f3;
        }
        
        .status-pass {
            color: #2ecc71;
            font-weight: bold;
        }
        
        .status-fail {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #c98f7a;
            font-size: 11px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }
        
        .back-link:hover {
            text-decoration: underline;
            transform: translateX(-3px);
        }
        
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
            .container {
                padding: 25px 20px;
                animation: none;
            }
            .analytics-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .title-section h2 {
                font-size: 18px;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }
            .chart-card {
                padding: 18px;
            }
            .pie-chart {
                width: 120px;
                height: 120px;
            }
            .legend {
                gap: 12px;
                font-size: 10px;
            }
            th, td {
                padding: 8px 10px;
                font-size: 11px;
            }
            .refresh-btn {
                padding: 8px 15px;
                font-size: 10px;
            }
            .timestamp {
                font-size: 9px;
            }
        }
        
        /* Tablet landscape */
        @media (min-width: 769px) and (max-width: 1024px) {
            .analytics-grid {
                gap: 20px;
            }
            .container {
                padding: 30px;
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
        <a href="system_settings.php" class="nav-item">SETTINGS</a>
        <a href="reports.php" class="nav-item active">VIEW REPORTS</a>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="header-section">
                <div class="title-section">
                    <h2>📊 STUDENT SUBMISSIONS REPORT</h2>
                    <div class="timestamp">
                        🕒 Last updated: <?php echo date('M d, Y h:i:s A'); ?>
                    </div>
                </div>
                <button class="refresh-btn" onclick="location.reload();">⟳ REFRESH DATA</button>
            </div>

            <div class="analytics-grid">
                <!-- PIE CHART CARD -->
                <div class="chart-card">
                    <strong>📈 PASS VS FAIL RATIO</strong>
                    <div class="pie-chart"></div>
                    <div class="legend">
                        <span style="color:#2ecc71;">● Passed (<?php echo $pass_percentage; ?>%)</span>
                        <span style="color:#e74c3c;">● Failed (<?php echo $fail_percentage; ?>%)</span>
                    </div>
                    <p style="font-size: 12px; margin-top: 15px; color: #5d4037;">
                        📋 Total Attempts: <strong><?php echo $total_submissions; ?></strong>
                    </p>
                </div>

                <!-- BAR CHART CARD -->
                <div class="chart-card">
                    <strong>🎯 SYSTEM-WIDE PERFORMANCE</strong>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width: 100%;">
                            TOTAL SUBMISSIONS: <?php echo $total_submissions; ?>
                        </div>
                    </div>
                    <p style="font-size: 11px; color: #c98f7a; margin-top: 15px; text-align: left;">
                        💡 Currently tracking performance across all active quizzes. Data updates in real-time.
                    </p>
                </div>
            </div>

            <!-- SUBMISSIONS TABLE -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>STUDENT NAME</th>
                            <th>QUIZ TITLE</th>
                            <th>SCORE</th>
                            <th>STATUS</th>
                            <th>DATE COMPLETED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_data = false;
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)): 
                                $has_data = true;
                                $display_name = !empty($row['student_name']) ? $row['student_name'] : 'Unknown User';
                                $display_title = !empty($row['quiz_title']) ? htmlspecialchars($row['quiz_title']) : "<span style='color:#e74c3c;'>⚠️ Untitled Quiz (Deleted)</span>";
                                $percent = ($row['total_questions'] > 0) ? ($row['score'] / $row['total_questions']) * 100 : 0;
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($display_name); ?></strong></td>
                            <td><?php echo $display_title; ?></td>
                            <td><?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?> 
                                (<?php echo round($percent, 1); ?>%)
                            </td>
                            <td class="<?php echo ($row['status'] == 'Passed') ? 'status-pass' : 'status-fail'; ?>">
                                <?php echo $row['status']; ?>
                            </td>
                            <td><?php echo date('M d, Y | h:i A', strtotime($row['completed_at'])); ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        }
                        
                        if (!$has_data): 
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px; color: #c98f7a;">
                                📭 No submissions found yet. Students haven't taken any quizzes.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <a href="admin_dashboard.php" class="back-link">← BACK TO DASHBOARD</a>
        </div>
    </main>

    <script>
        // Auto-refresh every 30 seconds (reduced from 15 to 30 for better UX)
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>

</body>
</html>