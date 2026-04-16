<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once '../dbconnect.php'; 

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

// DELETE LOGIC
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: manage_quizzes.php?msg=Quiz Deleted");
        exit;
    }
}

// AJAX LOGIC - Kunin ang active sections
if (isset($_GET['get_active_sections'])) {
    $q_id = intval($_GET['get_active_sections']);
    $now = date('Y-m-d H:i:s');
    $res = $conn->query("SELECT section_name, deadline FROM quiz_assignments WHERE quiz_id = $q_id AND deadline > '$now' ORDER BY section_name ASC");
    $data = [];
    while($r = $res->fetch_assoc()) { 
        $data[] = $r; 
    }
    echo json_encode($data);
    exit;
}

// ASSIGN SECTION LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_section'])) {
    $quiz_id = intval($_POST['quiz_id']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $section_name = mysqli_real_escape_string($conn, strtoupper(trim($_POST['section_name'])));
    $status = 'active';
    $assigned_by = $_SESSION['user_id'];
    
    if (empty($section_name)) {
        header("Location: manage_quizzes.php?msg=" . urlencode("Please enter a section name!"));
        exit;
    }
    
    $check = $conn->query("SELECT id FROM quiz_assignments WHERE quiz_id = $quiz_id AND LOWER(section_name) = LOWER('$section_name')");
    if ($check && $check->num_rows > 0) {
        $conn->query("UPDATE quiz_assignments SET deadline = '$deadline', status = '$status', assigned_at = NOW() WHERE quiz_id = $quiz_id AND LOWER(section_name) = LOWER('$section_name')");
        $msg = "Section '$section_name' updated!";
    } else {
        $conn->query("INSERT INTO quiz_assignments (quiz_id, section_name, deadline, assigned_by, status) VALUES ($quiz_id, '$section_name', '$deadline', $assigned_by, '$status')");
        $msg = "Section '$section_name' assigned!";
    }
    header("Location: manage_quizzes.php?msg=" . urlencode($msg));
    exit;
}

$userName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin';

// SEARCH LOGIC
$search = "";
$where_clause = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause = "WHERE q.id = '$search' 
                     OR q.quiz_title LIKE '%$search%' 
                     OR q.category LIKE '%$search%' 
                     OR u.username LIKE '%$search%'";
}

$sql = "SELECT q.*, u.username as creator_username 
        FROM quizzes q 
        LEFT JOIN users u ON q.created_by = u.id 
        $where_clause
        ORDER BY q.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Manage Quizzes | Q-RIOUS</title>
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

        .logo { color: white; font-weight: bold; font-size: 24px; letter-spacing: 5px; text-shadow: 2px 2px 0 #c98f7a; }

        .logout-btn {
            background: #fdfaf9;
            color: #c98f7a;
            padding: 8px 15px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11px;
            border: 2px solid #c98f7a;
            box-shadow: 3px 3px 0 #c98f7a;
        }

        /* SIDEBAR - same as manage_users */
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
            border: 2px solid transparent;
            font-size: 13px;
        }

        .nav-item:hover, .nav-item.active {
            background: #fff;
            border-color: #e8d6cf;
            box-shadow: 4px 4px 0 #e8d6cf;
            transform: translate(-2px, -2px);
        }

        .nav-item.active { background: #e3a693; color: white; border-color: #c98f7a; box-shadow: 4px 4px 0 #c98f7a; }

        /* MAIN CONTENT */
        .main-content {
            margin-top: 70px;
            margin-left: 250px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* MANAGE CONTAINER - same floating box style */
        .manage-container {
            width: 100%;
            max-width: 1200px;
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            box-shadow: 10px 10px 0 #c98f7a;
            padding: 30px;
            animation: float 4s ease-in-out infinite;
            margin-bottom: 40px;
        }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        h1 { color: #5d4037; text-align: center; border-bottom: 2px solid #e8d6cf; padding-bottom: 10px; margin-bottom: 20px; font-size: 22px; }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* SEARCH BAR */
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 2px solid #e8d6cf;
            font-family: inherit;
            font-size: 13px;
            outline: none;
        }
        .search-input:focus { border-color: #c98f7a; }
        .btn-search {
            background: #e3a693;
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 4px 0 #c98f7a;
            cursor: pointer;
        }
        .btn-clear {
            background: #fff;
            color: #c98f7a;
            text-decoration: none;
            padding: 12px 15px;
            border: 2px solid #e8d6cf;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .btn-create {
            background: #e3a693;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 4px 0 #c98f7a;
            display: inline-block;
        }

        /* TABLE WRAPPER */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            background: white;
            border: 3px solid #c98f7a;
            box-shadow: 8px 8px 0 #e8d6cf;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }
        th, td {
            border: 1px solid #e8d6cf;
            padding: 12px;
            text-align: center;
            font-size: 13px;
        }
        th {
            background: #e3a693;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) { background: #fdfaf9; }

        /* ACTION BUTTONS */
        .btn-action {
            padding: 5px 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #c98f7a;
            display: inline-block;
            margin: 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-view { background: #fff; color: #c98f7a; }
        .btn-edit { background: #e3a693; color: white; border: none; }
        .btn-delete { background: #ff6b6b; color: white; border: none; }
        .btn-assign { background: #8fb9a8; color: white; border: none; }

        /* MODAL STYLES */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fdfaf9;
            margin: 5% auto;
            padding: 30px;
            border: 3px solid #c98f7a;
            width: 500px;
            max-width: 90%;
            border-radius: 20px;
            box-shadow: 10px 10px 0 #c98f7a;
        }
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            color: #5d4037;
            margin-bottom: 20px;
            text-align: center;
        }
        .modal label {
            display: block;
            font-weight: bold;
            color: #c98f7a;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .modal input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 2px solid #e8d6cf;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
        }
        .modal input[name="section_name"] {
            text-transform: uppercase;
        }
        .modal button {
            background: #e3a693;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 20px;
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #c98f7a;
        }
        .close:hover { color: #d9534f; }
        .helper-text {
            font-size: 11px;
            color: #c98f7a;
            margin-top: 5px;
        }
        .active-sections-box {
            margin-top: 20px;
            border-top: 2px dashed #e8d6cf;
            padding-top: 15px;
        }
        .active-title {
            font-size: 12px;
            font-weight: bold;
            color: #5d4037;
            margin-bottom: 10px;
        }
        .section-tag {
            background: #fff;
            border: 1px solid #c98f7a;
            padding: 8px 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            border-radius: 8px;
        }

        /* ========== RESPONSIVE: SAME AS MANAGE_USERS.PHP ========== */
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
                margin-bottom: 0;
                margin-right: 10px;
                white-space: nowrap;
                display: inline-block;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
                padding-top: 20px;
            }
            .logo { font-size: 18px; letter-spacing: 2px; }
            .top-bar { padding: 0 15px; }
            .manage-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="logo">Q-RIOUS</div>
        <div class="profile-section">
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
        <div class="manage-container">
            <h1>📋 ALL QUIZZES</h1>

            <div class="header-flex">
                <a href="create_quiz_admin.php" class="btn-create">+ CREATE NEW QUIZ</a>
            </div>

            <form method="GET" class="search-container">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by ID, title, category, or instructor..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">SEARCH</button>
                <?php if(!empty($search)): ?>
                    <a href="manage_quizzes.php" class="btn-clear">CLEAR</a>
                <?php endif; ?>
            </form>

            <?php if(isset($_GET['msg'])): ?>
                <p style="color: #c98f7a; margin-bottom: 10px; font-weight: bold; text-align: center;">[ <?php echo htmlspecialchars($_GET['msg']); ?> ]</p>
            <?php endif; ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>QUIZ TITLE</th>
                            <th>CREATOR</th>
                            <th>CATEGORY</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['quiz_title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['creator_username'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <a href="view_quiz.php?id=<?php echo $row['id']; ?>" class="btn-action btn-view">VIEW</a>
                                    <a href="edit_quiz_admin.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">EDIT</a>
                                    <button onclick="openAssignModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['quiz_title'])); ?>')" class="btn-action btn-assign">ASSIGN</button>
                                    <a href="manage_quizzes.php?delete_id=<?php echo $row['id']; ?>" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Are you sure?');">DEL</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="padding: 30px; text-align: center;">
                                <?php echo !empty($search) ? "No results for: '" . htmlspecialchars($search) . "'" : "No quizzes found."; ?>
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <a href="admin_dashboard.php" style="display: block; margin-top: 20px; color: #c98f7a; text-decoration: none; font-size: 11px; text-align: center;">← BACK TO DASHBOARD</a>
        </div>
    </main>

    <!-- ASSIGN MODAL -->
    <div id="assignModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-header">📌 ASSIGN QUIZ TO SECTION</div>
            <form method="POST" id="assignForm">
                <input type="hidden" name="quiz_id" id="modal_quiz_id">
                <label>Quiz: <span id="modal_quiz_title" style="font-weight: bold; color: #e3a693;"></span></label>
                
                <label>🎯 SECTION NAME</label>
                <input type="text" name="section_name" id="section_name" 
                       placeholder="e.g., BSIT 3A" required autocomplete="off">
                <div class="helper-text">💡 Auto-uppercase, case-insensitive</div>
                
                <label>📅 DEADLINE</label>
                <input type="datetime-local" name="deadline" id="deadline" required>
                
                <button type="submit" name="assign_section">ASSIGN TO SECTION →</button>
            </form>
            
            <div class="active-sections-box">
                <div class="active-title">📋 ACTIVE SECTIONS</div>
                <div id="active_list_content">
                    <div class="helper-text">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal(quizId, quizTitle) {
            document.getElementById('modal_quiz_id').value = quizId;
            document.getElementById('modal_quiz_title').innerText = quizTitle;
            document.getElementById('assignModal').style.display = 'block';
            
            const d = new Date();
            d.setDate(d.getDate() + 7);
            document.getElementById('deadline').value = d.toISOString().slice(0,16);

            fetch('manage_quizzes.php?get_active_sections=' + quizId)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if(data.length === 0) {
                        html = '<div class="helper-text">✨ No active sections.</div>';
                    } else {
                        data.forEach(item => {
                            html += `<div class="section-tag">
                                <span>📌 ${item.section_name}</span>
                                <span>⏰ ${new Date(item.deadline).toLocaleDateString()}</span>
                            </div>`;
                        });
                    }
                    document.getElementById('active_list_content').innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('active_list_content').innerHTML = '<div class="helper-text">⚠️ Unable to load sections.</div>';
                });
        }

        function closeModal() {
            document.getElementById('assignModal').style.display = 'none';
        }

        window.onclick = function(e) {
            const modal = document.getElementById('assignModal');
            if (e.target == modal) closeModal();
        }

        const sectionInput = document.getElementById('section_name');
        if (sectionInput) {
            sectionInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    </script>
</body>
</html>