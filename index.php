<?php
require_once __DIR__ . '/includes/config.php';

if ($is_logged_in) {
    header("Location: " . $base_url . "/views/dashboard.php");
} else {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

?>