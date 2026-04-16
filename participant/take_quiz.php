

<?php
session_start();
require_once '../dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($quiz_id <= 0) { die("Invalid quiz ID."); }

// Kunin ang SECTION ng student mula sa users table
$user_query = $conn->query("SELECT section FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();
$user_section = $user['section'] ?? '';

// Kapag walang section ang student, redirect sa set_section.php
if (empty($user_section)) {
    header("Location: set_section.php?quiz_id=$quiz_id&return_to=take_quiz");
    exit;
}

// Check kung ang quiz ay naka-assign sa SECTION ng student (CASE-INSENSITIVE)
$assignment_check = $conn->query("
    SELECT * FROM quiz_assignments 
    WHERE quiz_id = $quiz_id 
    AND LOWER(section_name) = LOWER('$user_section')
    AND status = 'active'
");

if (!$assignment_check || $assignment_check->num_rows == 0) {
    echo "<script>
        alert('❌ You are not authorized to take this quiz.\\n\\nYour section: \"$user_section\"\\nThis quiz is not assigned to your section.');
        window.location.href='available_quizzes.php';
    </script>";
    exit;
}

// Kunin ang assignment details
$assignment = $assignment_check->fetch_assoc();
$deadline = $assignment['deadline'];

// Check deadline
$current_date = date('Y-m-d H:i:s');
if ($current_date > $deadline) {
    echo "<script>
        alert('⏰ Sorry, the deadline for this quiz has passed.\\nDeadline: $deadline');
        window.location.href='available_quizzes.php';
    </script>";
    exit;
}

// Check if already attempted
$check_attempt = $conn->query("SELECT id FROM quiz_attempts WHERE user_id = $user_id AND quiz_id = $quiz_id");
if ($check_attempt && $check_attempt->num_rows > 0) {
    echo "<script>
        alert('You have already completed this quiz.');
        window.location.href='participant_dashboard.php';
    </script>";
    exit;
}

// Kunin ang quiz details at questions
$quiz_res = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id");
$quiz = ($quiz_res && $quiz_res->num_rows > 0) ? $quiz_res->fetch_assoc() : die("Quiz not found.");

$questions_res = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id");
$questions_array = [];
while($row = $questions_res->fetch_assoc()) { 
    $questions_array[] = $row; 
}
shuffle($questions_array);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz | Q-RIOUS</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f5e6e0; 
            padding: 110px 20px 50px; 
            color: #5d4037; 
        }
        
        .timer-bar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 75px; 
            background: rgba(227, 166, 147, 0.85); 
            backdrop-filter: blur(12px); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 30px; 
            border-bottom: 4px solid #c98f7a; 
            z-index: 1000; 
            color: white; 
        }
        
        .timer-bar h2 { 
            margin: 0; 
        }
        
        .deadline-info { 
            font-size: 12px; 
            background: rgba(0,0,0,0.3); 
            padding: 5px 15px; 
            border-radius: 20px; 
        }
        
        .container { 
            max-width: 850px; 
            margin: auto; 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(15px); 
            border: 2px solid #c98f7a; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        
        .q-block { 
            background: white; 
            border-radius: 15px; 
            padding: 25px; 
            margin-bottom: 25px; 
            border-left: 8px solid #e3a693; 
            box-shadow: 5px 5px 15px rgba(0,0,0,0.05); 
            transition: 0.3s; 
        }
        
        .q-text { 
            font-size: 1.2rem; 
            font-weight: bold; 
            margin-bottom: 15px; 
            display: block; 
        }
        
        .type-badge { 
            font-size: 0.7rem; 
            background: #fce4ec; 
            color: #d81b60; 
            padding: 3px 10px; 
            border-radius: 12px; 
            margin-left: 10px; 
            text-transform: uppercase; 
        }
        
        .option-item { 
            display: flex; 
            align-items: center; 
            margin: 10px 0; 
            cursor: pointer; 
            padding: 8px 12px; 
            border-radius: 10px; 
            transition: background 0.2s; 
        }
        
        .option-item:hover { 
            background: #fce4ec; 
        }
        
        .option-item input { 
            width: 20px; 
            height: 20px; 
            margin-right: 12px; 
            accent-color: #c98f7a; 
            cursor: pointer; 
        }
        
        .option-item span {
            font-size: 15px;
            color: #5d4037;
        }
        
        .text-answer {
            width: 100%;
            padding: 14px;
            border: 2px solid #e8d6cf;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            background: white;
            font-family: 'Segoe UI', sans-serif;
            margin-top: 10px;
        }
        
        .text-answer:focus { 
            border-color: #c98f7a; 
            box-shadow: 0 0 0 3px rgba(201, 143, 122, 0.2);
        }
        
        .btn-submit { 
            background: #e3a693; 
            color: white; 
            padding: 18px; 
            border: none; 
            width: 100%; 
            font-size: 1.2rem; 
            font-weight: bold; 
            border-radius: 12px; 
            cursor: pointer; 
            margin-top: 20px; 
            transition: 0.3s; 
        }
        
        .btn-submit:hover { 
            background: #c98f7a; 
            transform: scale(0.98); 
        }
        
        .error-highlight { 
            border-left: 8px solid #d9534f !important; 
            background-color: #fff5f5 !important; 
        }
        
        .warning { 
            color: #d9534f; 
            font-size: 13px; 
            margin-top: 10px; 
            text-align: center; 
            display: none; 
        }
        
        .info-bar { 
            background: #e3a69320; 
            padding: 12px 20px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-wrap: wrap; 
            gap: 10px; 
        }
        
        .info-item { 
            font-size: 13px; 
            color: #5d4037; 
        }
        
        .info-label { 
            font-weight: bold; 
            color: #c98f7a; 
        }
        
        /* Radio group styling */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="timer-bar">
        <h2>⏳ TIME: <span id="timer">--:--</span></h2>
        <div class="deadline-info">📅 Deadline: <?php echo date('M d, Y h:i A', strtotime($deadline)); ?></div>
    </div>

    <div class="container">
        <h1 style="text-align: center; margin-bottom: 20px; color: #c98f7a;">📚 <?php echo strtoupper(htmlspecialchars($quiz['quiz_title'])); ?></h1>
        
        <div class="info-bar">
            <div class="info-item"><span class="info-label">👤 Your Section:</span> <?php echo htmlspecialchars($user_section); ?></div>
            <div class="info-item"><span class="info-label">📋 Total Questions:</span> <?php echo count($questions_array); ?></div>
            <div class="info-item"><span class="info-label">⏱️ Time Limit:</span> <?php echo $quiz['time_limit']; ?> minutes</div>
        </div>
        
        <form id="quizForm" method="POST" action="process_score.php">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

            <?php 
            $count = 1;
            foreach($questions_array as $q): 
                $qid = $q['id'];
                $db_type = trim($q['question_type']);
            ?>
                <div class="q-block" id="block_<?php echo $qid; ?>">
                    <span class="q-text">
                        <?php echo $count; ?>. <?php echo htmlspecialchars($q['question_text']); ?>
                        <span class="type-badge"><?php echo htmlspecialchars($db_type); ?></span>
                    </span>

                    <div class="options-container">
                        <?php 
                        // ==========================================
                        // MULTIPLE CHOICE
                        // ==========================================
                        if ($db_type == "Multiple Choice"): 
                        ?>
                            <?php foreach(['a', 'b', 'c', 'd'] as $l):
                                if(!empty(trim($q['option_'.$l]))): ?>
                                    <label class="option-item">
                                        <input type="radio" name="ans[<?php echo $qid; ?>]" value="<?php echo strtoupper($l); ?>" required>
                                        <span><?php echo strtoupper($l); ?>. <?php echo htmlspecialchars($q['option_'.$l]); ?></span>
                                    </label>
                                <?php endif;
                            endforeach; ?>
                        
                        <?php 
                        // ==========================================
                        // TRUE OR FALSE
                        // ==========================================
                        elseif ($db_type == "True or False" || $db_type == "True/False"): 
                        ?>
                            <label class="option-item">
                                <input type="radio" name="ans[<?php echo $qid; ?>]" value="True" required>
                                <span>✅ TRUE</span>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="ans[<?php echo $qid; ?>]" value="False" required>
                                <span>❌ FALSE</span>
                            </label>
                        
                        <?php 
                        // ==========================================
                        // FILL IN THE BLANKS
                        // ==========================================
                        elseif ($db_type == "Fill in the Blanks"): 
                        ?>
                            <input type="text" name="ans[<?php echo $qid; ?>]" class="text-answer" placeholder="Fill in the blank..." required autocomplete="off">
                        
                        <?php 
                        // ==========================================
                        // SHORT ANSWER / IDENTIFICATION (DEFAULT)
                        // ==========================================
                        else: 
                        ?>
                            <input type="text" name="ans[<?php echo $qid; ?>]" class="text-answer" placeholder="Type your answer here..." required autocomplete="off">
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
            $count++;
            endforeach; 
            ?>

            <button type="submit" class="btn-submit">✨ SUBMIT QUIZ ✨</button>
            <div id="formWarning" class="warning">⚠️ Please answer all questions before submitting.</div>
        </form>
    </div>

    <script>
        let timeLeft = <?php echo max(1, intval($quiz['time_limit'])) * 60; ?>;
        const timerDisplay = document.getElementById('timer');
        const quizForm = document.getElementById('quizForm');
        
        function updateTimer() {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            timerDisplay.innerText = `${m < 10 ? '0'+m : m}:${s < 10 ? '0'+s : s}`;
        }
        updateTimer();
        
        const countdown = setInterval(() => {
            if(timeLeft <= 0) { 
                clearInterval(countdown); 
                alert("Time's up! Submitting your quiz...");
                quizForm.submit(); 
            } else {
                timeLeft--;
                updateTimer();
            }
        }, 1000);

        quizForm.addEventListener('submit', function(e) {
            let allAnswered = true;
            let firstUnanswered = null;
            
            document.querySelectorAll('.q-block').forEach(block => {
                let answered = false;
                const radios = block.querySelectorAll('input[type="radio"]');
                const textInputs = block.querySelectorAll('input[type="text"]');
                
                if (radios.length > 0) {
                    for (let radio of radios) {
                        if (radio.checked) { 
                            answered = true; 
                            break; 
                        }
                    }
                }
                if (textInputs.length > 0 && !answered) {
                    for (let input of textInputs) {
                        if (input.value.trim() !== '') { 
                            answered = true; 
                            break; 
                        }
                    }
                }
                
                if (!answered) {
                    allAnswered = false;
                    block.classList.add('error-highlight');
                    if (!firstUnanswered) firstUnanswered = block;
                } else {
                    block.classList.remove('error-highlight');
                }
            });

            if (!allAnswered) {
                e.preventDefault();
                const warningDiv = document.getElementById('formWarning');
                warningDiv.style.display = 'block';
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                setTimeout(() => {
                    warningDiv.style.display = 'none';
                }, 3000);
            }
        });
        
        // Remove highlight when user types or clicks
        document.querySelectorAll('.q-block input').forEach(input => {
            input.addEventListener('change', function() {
                const block = this.closest('.q-block');
                block.classList.remove('error-highlight');
            });
            if (input.type === 'text') {
                input.addEventListener('input', function() {
                    const block = this.closest('.q-block');
                    block.classList.remove('error-highlight');
                });
            }
        });
    </script>
</body>
</html>

