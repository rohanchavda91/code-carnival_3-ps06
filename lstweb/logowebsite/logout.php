<?php
session_start();

include("config.php");
if (file_exists("includes/order_helpers.php")) {
    include_once("includes/order_helpers.php");
    $userId = (int)($_SESSION['user_id'] ?? 0);
    $historyId = (int)($_SESSION['login_history_id'] ?? 0);

    if ($userId > 0 || $historyId > 0) {
        record_user_logout($conn, $userId, $historyId);
    }
}

$_SESSION = [];
session_unset();
session_destroy();

header("Location: login.php?msg=" . urlencode("You have been logged out successfully."));
exit;
