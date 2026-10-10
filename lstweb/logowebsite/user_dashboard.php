<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php?msg=" . urlencode("Please login to view your dashboard."));
    exit;
}

header("Location: myprofile.php");
exit;
