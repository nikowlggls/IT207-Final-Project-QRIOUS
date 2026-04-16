<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once '../dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// 1. Kuhanin ang existing data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Handle Form Submission
if ($_SERVER['REQUEST_SESSION'] == 'POST' || isset($_POST['update_profile'])) {
    $fullname   = $_POST['fullname'];
    $bio        = $_POST['bio'];
    $section    = $_POST['section'];
    $birthday   = $_POST['birthday'];
    $contact_no = $_POST['contact_no'];

    $update_stmt = $conn->prepare("UPDATE users SET fullname=?, bio=?, section=?, birthday=?, contact_no=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $fullname, $bio, $section, $birthday, $contact_no, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['fullname'] = $fullname; // Update session para mag-reflect sa dashboard
        header("Location: profile.php?msg=Profile Updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-RIOUS | Edit Profile</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important; }
        
        body { 
            font-family: 'Courier New', monospace; 
            background-color: #f5e6e0; 
            background-image: linear-gradient(#e8d6cf 1px, transparent 1px),
                              linear-gradient(90deg, #e8d6cf 1px, transparent 1px);
            background-size: 25px 25px;
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 20px; 
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .edit-container {
            width: 100%;
            max-width: 800px;
            background: #fdfaf9;
            border: 3px solid #c98f7a;
            border-radius: 25px;
            box-shadow: 12px 12px 0 #c98f7a;
            padding: 40px;
            animation: float 6s ease-in-out infinite;
        }

        .form-header {
            text-align: left;
            margin-bottom: 30px;
            border-left: 6px solid #e3a693;
            padding-left: 15px;
        }

        .form-header h1 { color: #5d4037; font-size: 28px; text-transform: uppercase; }

        /* Form Grid - Desktop Friendly */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 columns on desktop */
            gap: 20px;
        }

        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        
        /* Bio/Fullname span across 2 columns if needed */
        .full-width { grid-column: span 2; }

        label {
            font-size: 11px;
            font-weight: bold;
            color: #c98f7a;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        input, textarea {
            background: #fff;
            border: 2px solid #e8d6cf;
            padding: 12px 15px;
            border-radius: 10px;
            font-family: inherit;
            color: #5d4037;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        input:focus, textarea:focus { border-color: #c98f7a; background: #fffafa; }

        .btn-save {
            background: #e3a693;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: bold;
            font-family: inherit;
            font-size: 16px;
            box-shadow: 4px 4px 0 #c98f7a;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-save:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 #c98f7a; background: #d99580; }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #c98f7a;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .edit-container { padding: 25px; }
        }
    </style>
</head>
<body>

<div class="edit-container">
    <div class="form-header">
        <h1>Update Profile</h1>
        <p style="color: #a67c6d; font-size: 12px;">Keep your information up to date.</p>
    </div>

    <form action="" method="POST">
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>

            <div class="form-group full-width">
                <label>Bio / Headline</label>
                <textarea name="bio" rows="2"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Section</label>
                <input type="text" name="section" value="<?php echo htmlspecialchars($user['section'] ?? ''); ?>" placeholder="e.g. BSIT 2H-G1">
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Birthday</label>
                <input type="date" name="birthday" value="<?php echo $user['birthday']; ?>">
            </div>

            <div class="form-group">
                <label>Username (Read-only)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="background: #f5e6e0; color: #888;">
            </div>
        </div>

        <button type="submit" name="update_profile" class="btn-save">💾 SAVE CHANGES</button>
        <a href="profile.php" class="cancel-link">Cancel and Discard Changes</a>
    </form>
</div>

</body>
</html>