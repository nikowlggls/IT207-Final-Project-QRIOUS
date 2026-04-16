<?php
session_start();
require_once '../dbconnect.php';

// 1. Siguraduhin na may naka-login na user_id (ito ang ginamit natin sa process_score.php)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. QUERY: Gamit ang user_id para makuha ang record sa quiz_attempts
// Nag-JOIN tayo sa quizzes table para makuha ang Title ng quiz
$query = "SELECT a.*, q.quiz_title 
          FROM quiz_attempts a 
          JOIN quizzes q ON a.quiz_id = q.id 
          WHERE a.user_id = '$user_id' 
          ORDER BY a.completed_at DESC";

$result = mysqli_query($conn, $query);
$submissions = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $submissions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results | Q-RIOUS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5e6e0; padding: 40px; }
        .container { max-width: 1000px; margin: auto; background: #fdfaf9; border: 2px solid #c98f7a; box-shadow: 12px 12px 0 #c98f7a; padding: 40px; border-radius: 10px; }
        
        .chart-container { background: #fff; border: 2px solid #e8d6cf; padding: 25px; margin-bottom: 30px; border-radius: 8px; }
        .bar-wrapper { margin-bottom: 15px; }
        .bar-label { display: flex; justify-content: space-between; font-size: 13px; color: #5d4037; margin-bottom: 5px; font-weight: bold; }
        .bar-bg { height: 25px; background: #fce4ec; border: 1px solid #e8d6cf; border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 100%; transition: width 0.8s ease; }

        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th { background: #e3a693; color: white; padding: 12px; text-transform: uppercase; font-size: 14px; }
        td { padding: 12px; text-align: center; border: 1px solid #e8d6cf; color: #5d4037; font-size: 14px; }
        .status-passed { color: #2ecc71; font-weight: bold; }
        .status-failed { color: #e74c3c; font-weight: bold; }
        
        .back-btn { text-decoration:none; color:#c98f7a; font-weight:bold; display: inline-block; margin-bottom: 20px; transition: 0.3s; }
        .back-btn:hover { color: #5d4037; transform: translateX(-5px); }
    </style>
</head>
<body>

<div class="container">
    <a href="participant_dashboard.php" class="back-btn">← BACK TO DASHBOARD</a>
    <h2 style="text-align:center; color:#c98f7a; margin-bottom: 30px; text-transform:uppercase; letter-spacing: 2px;">My Quiz Performance</h2>

    <div class="chart-container">
        <h4 style="margin-bottom:15px; color:#c98f7a; border-bottom: 1px solid #eee; padding-bottom: 10px;">LATEST ACTIVITIES</h4>
        <?php if (empty($submissions)): ?>
            <p style="text-align:center; color:#999; padding: 20px;">No records found. Take a quiz to see your performance!</p>
        <?php else: ?>
            <?php 
            // I-display ang top 5 latest results sa bar chart
            foreach (array_slice($submissions, 0, 5) as $sub): 
                $percent = ($sub['total_questions'] > 0) ? round(($sub['score'] / $sub['total_questions']) * 100) : 0;
                $color = ($percent >= 50) ? '#2ecc71' : '#e74c3c';
            ?>
            <div class="bar-wrapper">
                <div class="bar-label">
                    <span><?php echo htmlspecialchars($sub['quiz_title']); ?></span>
                    <span><?php echo $percent; ?>%</span>
                </div>
                <div class="bar-bg">
                    <div class="bar-fill" style="width: <?php echo $percent; ?>%; background: <?php echo $color; ?>;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>QUIZ TITLE</th>
                <th>SCORE</th>
                <th>STATUS</th>
                <th>DATE COMPLETED</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $s): 
                $is_passed = (strtolower($s['status']) == 'passed');
            ?>
            <tr>
                <td style="text-align: left; font-weight: bold;"><?php echo htmlspecialchars($s['quiz_title']); ?></td>
                <td><?php echo $s['score']; ?> / <?php echo $s['total_questions']; ?></td>
                <td class="<?php echo $is_passed ? 'status-passed' : 'status-failed'; ?>">
                    <?php echo strtoupper($s['status']); ?>
                </td>
                <td><?php echo date('M d, Y | h:i A', strtotime($s['completed_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>