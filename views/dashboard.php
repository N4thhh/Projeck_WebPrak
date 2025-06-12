<?php

$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../functions/transaction_functions.php';
//require_once __DIR__ . '/../functions/wallet_functions.php';?>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>