<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../functions/user_functions.php';


if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $user_id_to_delete = $_GET['id'];

    if ($user_id_to_delete == $_SESSION['user_id']) {
        deleteUser($conn, $user_id_to_delete);
        session_start();

        $_SESSION = array();

        session_destroy();

         header("Location: " . $base_url);
        exit;
    }


    if (deleteUser($conn, $user_id_to_delete)) {
        $_SESSION['message'] = "User has been successfully deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }

    header("Location: manage_users.php");
    exit();

} else {
    header("Location: manage_users.php");
    exit();
}