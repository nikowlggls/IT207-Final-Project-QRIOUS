<?php
session_start();
require_once '../dbconnect.php';

// Check kung Admin at kung may ID na dala
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin' || !isset($_GET['id'])) {
    header('Location: manage_quizzes.php');
    exit;
}

$quiz_id = intval($_GET['id']);

// Kunin ang Quiz Info
$quiz_query = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id");
if ($quiz_query->num_rows == 0) {
    header('Location: manage_quizzes.php');
    exit;
}
$quiz = $quiz_query->fetch_assoc();

// Kunin ang lahat ng Questions para sa quiz na ito
$questions_query = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC");

$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>View Quiz Details | Q-RIOUS</title>
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
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        
        /* VIEW CONTAINER - with floating animation */
        .view-container {
            width: 100%;
            max-width: 950px;
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
        
        /* QUIZ INFO HEADER */
        .quiz-info-header {
            border-bottom: 2px solid #e3a693;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }
        
        .quiz-info-header h1 {
            color: #5d4037;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-size: 24px;
            word-break: break-word;
        }
        
        .meta-info {
            color: #c98f7a;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        /* QUESTION CARD */
        .q-card {
            background: white;
            border: 2px solid #e8d6cf;
            padding: 25px;
            margin-bottom: 25px;
            position: relative;
            box-shadow: 4px 4px 0 #e8d6cf;
            transition: 0.2s;
        }
        
        .q-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 8px 8px 0 #e8d6cf;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 10px;
            background: #e3a693;
            color: white;
            margin-bottom: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: 3px;
        }
        
        .q-text {
            font-size: 15px;
            line-height: 1.5;
            color: #5d4037;
            margin-bottom: 15px;
            word-break: break-word;
        }
        
        .q-text strong {
            color: #c98f7a;
        }
        
        /* OPTIONS GRID - responsive */
        .options-box {
            font-size: 13px;
            color: #7d6b65;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            background: #fafafa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .options-box div {
            padding: 5px;
            border-left: 3px solid #e8d6cf;
            padding-left: 10px;
        }
        
        .ans-highlight {
            color: #2e7d32;
            font-weight: bold;
            margin-top: 15px;
            border-top: 1px dashed #c98f7a;
            padding-top: 12px;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        
        .ans-highlight span {
            background: #e8f5e9;
            padding: 5px 12px;
            border-radius: 20px;
            word-break: break-word;
        }
        
        /* ACTION LINKS */
        .action-links {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn-edit-quiz {
            background: #e3a693;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 4px 4px 0 #c98f7a;
            transition: 0.2s;
            display: inline-block;
        }
        
        .btn-edit-quiz:hover {
            background: #d69581;
            transform: translate(-1px, -1px);
            box-shadow: 6px 6px 0 #c98f7a;
        }
        
        .back-link {
            color: #c98f7a;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            transition: 0.2s;
        }
        
        .back-link:hover {
            text-decoration: underline;
            transform: translateX(-3px);
            display: inline-block;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            border: 2px dashed #e8d6cf;
            color: #c98f7a;
            background: white;
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
            .view-container {
                padding: 25px 20px;
                animation: none;
            }
            .quiz-info-header h1 {
                font-size: 20px;
            }
            .options-box {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .q-card {
                padding: 18px;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 480px) {
            .view-container {
                padding: 18px 15px;
            }
            .quiz-info-header h1 {
                font-size: 18px;
            }
            .meta-info {
                font-size: 10px;
                gap: 12px;
            }
            .q-text {
                font-size: 13px;
            }
            .options-box {
                font-size: 11px;
                padding: 10px;
            }
            .badge {
                font-size: 9px;
                padding: 3px 8px;
            }
            .btn-edit-quiz {
                padding: 10px 18px;
                font-size: 11px;
            }
            .ans-highlight {
                font-size: 11px;
            }
            .action-links {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
        }
        
        /* Tablet landscape */
        @media (min-width: 769px) and (max-width: 1024px) {
            .view-container {
                padding: 30px;
            }
            .options-box {
                grid-template-columns: repeat(2, 1fr);
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
        <a href="manage_quizzes.php" class="nav-item active">MANAGE QUIZZES</a>
        <a href="system_settings.php" class="nav-item">SETTINGS</a>
        <a href="reports.php" class="nav-item">VIEW REPORTS</a>
    </nav>

    <main class="main-content">
        <div class="view-container">
            <div class="quiz-info-header">
                <h1>📖 <?php echo strtoupper(htmlspecialchars($quiz['quiz_title'])); ?></h1>
                <div class="meta-info">
                    <span>📂 CATEGORY: <?php echo strtoupper($quiz['category']); ?></span>
                    <span>⏱️ TIME LIMIT: <?php echo $quiz['time_limit']; ?> MINS</span>
                    <span>📊 TOTAL Qs: <?php echo $questions_query->num_rows; ?></span>
                </div>
            </div>

            <?php if ($questions_query->num_rows > 0): ?>
                <?php $i = 1; while($q = $questions_query->fetch_assoc()): ?>
                    <div class="q-card">
                        <span class="badge">📌 <?php echo strtoupper($q['question_type']); ?></span>
                        <div class="q-text">
                            <strong><?php echo $i++; ?>.</strong> <?php echo htmlspecialchars($q['question_text']); ?>
                        </div>
                        
                        <?php if ($q['question_type'] == 'Multiple Choice'): ?>
                            <div class="options-box">
                                <div>A. <?php echo htmlspecialchars($q['option_a']); ?></div>
                                <div>B. <?php echo htmlspecialchars($q['option_b']); ?></div>
                                <div>C. <?php echo htmlspecialchars($q['option_c']); ?></div>
                                <div>D. <?php echo htmlspecialchars($q['option_d']); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="ans-highlight">
                            ✅ CORRECT ANSWER: <span><?php echo htmlspecialchars($q['correct_answer']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>📭 No questions found for this quiz.</p>
                    <p style="margin-top: 10px; font-size: 11px;">Click "MODIFY QUESTIONS" to add questions.</p>
                </div>
            <?php endif; ?>

            <div class="action-links">
                <a href="manage_quizzes.php" class="back-link">← BACK TO QUIZ LIST</a>
                <a href="edit_quiz_admin.php?id=<?php echo $quiz_id; ?>" class="btn-edit-quiz">✏️ MODIFY QUESTIONS</a>
            </div>
        </div>
    </main>

</body>
</html>