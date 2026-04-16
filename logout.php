<?php
session_start();

// 1. Burahin ang lahat ng session data para hindi na sila "Logged In"
$_SESSION = array();

// 2. Patayin ang session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Tuluyan nang i-destroy ang session sa server
session_destroy();

// 4. REDIRECTION: Dito natin babaguhin papuntang index.php
header("Location: index.php");
exit();
?>