<?php
// Saved Address router - redirects to Profile Address Tab
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Location: myprofile.php?tab=address");
exit;
