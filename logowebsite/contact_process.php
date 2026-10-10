<?php
// Fallback router for contact form
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("contact.php");
