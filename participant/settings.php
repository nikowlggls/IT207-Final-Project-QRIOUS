<?php
session_start();
// Lalabas sa participant folder para mahanap ang dbconnect
require_once '../dbconnect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Participant') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 1. Kuhanin ang Current Data ng User para sa placeholders
$user_query = $conn->prepare("SELECT fullname, username, password FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$userData = $user_query->get_result()->fetch_assoc();

// 2. Handle Form Submission
if (isset($_POST['update_settings'])) {
    $new_fullname = $_POST['fullname'];
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validation
    if ($current_pass !== $userData['password']) {
        $error_msg = "Incorrect current password.";
    } else {
        if (!empty($new_pass)) {
            if ($new_pass !== $confirm_pass) {
                $error_msg = "New passwords do not match.";
            } else {
                // Update Name and Password
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssi", $new_fullname, $new_pass, $user_id);
                if ($stmt->execute()) { $success_msg = "Settings updated successfully!"; }
            }
        } else {
            // Update Name only
            $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
            $stmt->bind_param("si", $new_fullname, $user_id);
            if ($stmt->execute()) { $success_msg = "Name updated successfully!"; }
        }
    }
    // Refresh user data after update
    header("Refresh:2");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Q-RIOUS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; cursor: url('https://cur.cursors-4u.net/games/gam-4/gam373.cur'), auto !important; }
        
        body {
            font-family: 'Courier New', monospace;
            background-color: #f5e6e0;
            background-image: linear-gradient(#e8d6cf 1px, transparent 1px),
                              linear-gradient(90deg, #e8d6cf 1px, transparent 1px);
            background-size: 20px 20px;
            display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;
        }

        .settings-box {
            background: #fdfaf9;
            width: 100%; max-width: 450px;
            padding: 30px;
            border: 3px solid #c98f7a;
            box-shadow: 8px 8px 0 #c98f7a;
            text-align: center;
        }

        h2 { color: #5d4037; letter-spacing: 3px; margin-bottom: 20px; font-size: 24px; }
        .section-label { font-size: 10px; font-weight: bold; color: #c98f7a; display: block; text-align: left; margin: 15px 0 5px 0; text-transform: uppercase; }

        input {
            width: 100%; padding: 12px; margin-bottom: 10px;
            border: 2px solid #e8d6cf; background: #fff;
            font-family: inherit; outline: none;
        }

        input:focus { border-color: #e3a693; background: #fffafa; }

        .btn-save {
            width: 100%; padding: 15px; background: #e3a693; color: white;
            border: none; font-weight: bold; font-family: inherit;
            box-shadow: 4px 4px 0 #c98f7a; margin-top: 15px; transition: 0.2s;
        }

        .btn-save:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 #c98f7a; }

        .alert { padding: 10px; font-size: 11px; margin-bottom: 15px; border: 1px solid; }
        .success { background: #e7f4e4; color: #2e7d32; border-color: #a5d6a7; }
        .error { background: #fbe9e7; color: #d84315; border-color: #ffab91; }

        .back-btn { display: inline-block; margin-top: 25px; color: #c98f7a; text-decoration: none; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

<div class="settings-box">
    <h2>SETTINGS</h2>

    <?php if($success_msg): ?> <div class="alert success"><?php echo $success_msg; ?></div> <?php endif; ?>
    <?php if($error_msg): ?> <div class="alert error"><?php echo $error_msg; ?></div> <?php endif; ?>

    <form method="POST">
        <span class="section-label">Personal Information</span>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($userData['fullname']); ?>" placeholder="Full Name" required>
        <input type="text" value="@<?php echo htmlspecialchars($userData['username']); ?>" disabled style="background: #eee; color: #888;">

        <span class="section-label">Security (Leave blank to keep password)</span>
        <input type="password" name="new_password" placeholder="New Password">
        <input type="password" name="confirm_password" placeholder="Confirm New Password">

        <span class="section-label" style="color: #d84315;">Confirm Identity</span>
        <input type="password" name="current_password" placeholder="Current Password" required>

        <button type="submit" name="update_settings" class="btn-save">SAVE CHANGES</button>
    </form>

    <a href="participant_dashboard.php" class="back-btn">← RETURN TO DASHBOARD</a>
</div>

</body>
</html>