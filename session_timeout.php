<?php
// session_timeout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timeout duration (in seconds)
$timeout_duration = 300; // 5 minutes

// Check if "last_activity" is set
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        // Timeout: destroy session and redirect to login
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>
