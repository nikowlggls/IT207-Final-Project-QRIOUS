<?php
session_start();
require_once '../dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kunin ang data mula sa URL parameters (?quiz_id=X&score=Y&total=Z)
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;

// Kunin ang Quiz Title
$quiz_query = $conn->query("SELECT quiz_title FROM quizzes WHERE id = $quiz_id");
$quiz = $quiz_query->fetch_assoc();

$percent = ($total > 0) ? round(($score / $total) * 100, 1) : 0;
$status = ($percent >= 50) ? "PASSED" : "FAILED";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q-RIOUS | Quiz Result</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5e6e0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(10px);
            padding: 40px; 
            border: 2px solid white; 
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center; 
            max-width: 450px; 
            width: 90%;
        }
        .quiz-title { color: #c98f7a; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .score-big { font-size: 60px; color: #e3a693; margin: 15px 0; font-weight: bold; }
        .status-badge { 
            padding: 10px 25px; 
            border-radius: 30px; 
            color: white; 
            font-weight: bold; 
            display: inline-block; 
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .passed { background: #28a745; }
        .failed { background: #dc3545; }
        .btn-container { display: flex; flex-direction: column; gap: 10px; }
        .btn { 
            padding: 12px; 
            background: #e3a693; 
            color: white; 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: bold; 
            transition: 0.3s; 
        }
        .btn:hover { background: #c98f7a; transform: translateY(-2px); }
        .btn-secondary { background: #5d4037; }
    </style>
</head>
<body>
    <div class="card">
        <div class="quiz-title">📚 <?php echo htmlspecialchars($quiz['quiz_title'] ?? 'QUIZ'); ?></div>
        <h2 style="color: #5d4037; margin: 0;">Quiz Completed!</h2>
        
        <div class="score-big"><?php echo $score; ?> / <?php echo $total; ?></div>
        
        <p style="font-size: 18px; margin-top: 0; color: #5d4037;">Final Grade: <b><?php echo $percent; ?>%</b></p>
        
        <div class="status-badge <?php echo strtolower($status); ?>">
            <?php echo $status; ?>
        </div>
        
        <div class="btn-container">
            <a href="my_results.php" class="btn">📊 VIEW ALL MY RESULTS</a>
            <a href="participant_dashboard.php" class="btn btn-secondary">🏠 GO TO DASHBOARD</a>
        </div>
    </div>
</body>
</html>