<?php
// Fallback router for booking form
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("booking.php");
