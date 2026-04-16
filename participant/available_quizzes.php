<?php
session_start();
// STEP 1: Ensure PHP uses the correct Timezone for comparison
date_default_timezone_set('Asia/Manila');

require_once '../dbconnect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Participant') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['id'] ?? $_SESSION['user_id'];
$student_name = $_SESSION['fullname'] ?? ''; 

$user_query = $conn->query("SELECT section FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();
$user_section = $user['section'] ?? '';

$urgent_reminders = [];
$now = new DateTime(); // Current time in Manila

$all_quizzes = [];
if (!empty($user_section)) {
    // Using Prepared Statements for security
    $sql = "SELECT q.*, qa.deadline, 
            (SELECT COUNT(*) FROM student_submissions WHERE quiz_id = q.id AND student_name = ?) as is_taken
            FROM quizzes q 
            JOIN quiz_assignments qa ON q.id = qa.quiz_id 
            WHERE qa.section_name = ? 
            ORDER BY q.id DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_name, $user_section);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while($row = $result->fetch_assoc()) {
            $all_quizzes[] = $row;

            // CHECK FOR POP-UP REMINDER
            if (!empty($row['deadline']) && $row['is_taken'] == 0) {
                $deadline = new DateTime($row['deadline']);
                
                // Get difference in seconds
                $diffInSeconds = $deadline->getTimestamp() - $now->getTimestamp();
                
                // Logic: If deadline is in the future AND less than or equal to 1 hour (3600s)
                if ($diffInSeconds > 0 && $diffInSeconds <= 3600) {
                    $minutes_left = floor($diffInSeconds / 60);
                    $time_display = ($minutes_left > 0) ? $minutes_left . " minutes" : "less than a minute";
                    
                    $urgent_reminders[] = [
                        'title' => strtoupper($row['quiz_title']),
                        'time' => $time_display
                    ];
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-RIOUS | Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important; }
        body { font-family: 'Courier New', monospace; background-color: #f5e6e0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fdfaf9; border: 3px solid #c98f7a; box-shadow: 10px 10px 0 #c98f7a; padding: 30px; border-radius: 15px; }
        .quiz-item { background: white; border: 2px solid #e8d6cf; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-radius: 10px; transition: 0.3s; }
        .quiz-item.done { opacity: 0.6; background: #eee; border-style: dashed; }
        .btn-start { background: #e3a693; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border: 2px solid #c98f7a; box-shadow: 4px 4px 0 #c98f7a; }
        .btn-done { background: #ccc; color: #666; padding: 12px 25px; font-weight: bold; border: 2px solid #999; cursor: not-allowed; }
        .btn-back { color: #c98f7a; text-decoration: none; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <a href="participant_dashboard.php" class="btn-back">← BACK TO DASHBOARD</a>
    <h1 style="color: #5d4037; margin: 15px 0;">🔥 QUIZ PORTAL</h1>
    <hr style="border: 1px dashed #c98f7a; margin-bottom: 25px;">

    <?php if (count($all_quizzes) > 0): ?>
        <?php foreach($all_quizzes as $row): 
            $is_done = $row['is_taken'] > 0;
        ?>
            <div class="quiz-item <?php echo $is_done ? 'done' : ''; ?>">
                <div>
                    <h3 style="margin:0; <?php echo $is_done ? 'text-decoration: line-through;' : ''; ?>">
                        📝 <?php echo strtoupper(htmlspecialchars($row['quiz_title'])); ?>
                    </h3>
                    <div style="font-size: 11px; color: #c98f7a; margin-top: 5px;">
                        <span>⏱️ <?php echo $row['time_limit']; ?>m</span> | 
                        <span>📅 Deadline: <?php echo date('M d, h:i A', strtotime($row['deadline'])); ?></span>
                    </div>
                </div>
                
                <?php if($is_done): ?>
                    <span class="btn-done">COMPLETED ✓</span>
                <?php else: ?>
                    <a href="take_quiz.php?id=<?php echo $row['id']; ?>" class="btn-start">START QUIZ →</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #c98f7a;">No quizzes found for your section.</p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($urgent_reminders)): ?>
        <?php foreach ($urgent_reminders as $rem): ?>
            Swal.fire({
                title: '<strong>RUSH REMINDER! ⏰</strong>',
                icon: 'warning',
                html: 'The quiz <b><?php echo $rem['title']; ?></b> will close in <b><?php echo $rem['time']; ?></b>!',
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: 'TAKE IT NOW! ✍️',
                confirmButtonColor: '#e3a693',
                background: '#fdfaf9',
                color: '#5d4037',
                backdrop: `rgba(227, 166, 147, 0.4)`
            });
        <?php endforeach; ?>
    <?php endif; ?>
});
</script>

</body>
</html>