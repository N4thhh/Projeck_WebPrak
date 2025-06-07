<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "http://localhost/Projeck_WebPrak";
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
?>