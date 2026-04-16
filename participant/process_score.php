<?php
session_start();
require_once '../dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = intval($_POST['quiz_id']);
    $user_id = $_SESSION['user_id']; 
    $user_answers = $_POST['ans'] ?? []; 
    
    $score = 0;

    // 1. Kunin ang mga tamang sagot mula sa database
    $q_query = $conn->query("SELECT id, correct_answer FROM questions WHERE quiz_id = $quiz_id");
    $total_questions = $q_query->num_rows;

    if ($total_questions > 0) {
        while ($row = $q_query->fetch_assoc()) {
            $q_id = $row['id'];
            $correct_ans = trim($row['correct_answer']);
            
            if (isset($user_answers[$q_id])) {
                $submitted_ans = trim($user_answers[$q_id]);
                
                // Case-insensitive comparison (Para kahit "Database" o "database" ang itype, tama pa rin)
                if (strtolower($submitted_ans) === strtolower($correct_ans)) {
                    $score++;
                }
            }
        }
    }

    // 2. Compute Status
    $percentage = ($total_questions > 0) ? ($score / $total_questions) * 100 : 0;
    $status = ($percentage >= 50) ? 'Passed' : 'Failed';

    // 3. I-SAVE SA DATABASE (quiz_attempts table)
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (user_id, quiz_id, score, total_questions, status, completed_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiis", $user_id, $quiz_id, $score, $total_questions, $status);
    
    if ($stmt->execute()) {
        // 4. REDIRECT SA RESULTS.PHP (Pinapasa ang data sa URL)
        header("Location: results.php?quiz_id=$quiz_id&score=$score&total=$total_questions");
        exit;
    } else {
        die("Database Error: " . $conn->error);
    }
} else {
    header('Location: participant_dashboard.php');
    exit;
}
?>