<?php
function getAllUsers($conn) {
    $users = [];
    $query = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

function createUser($conn, $username, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    $result = mysqli_query($conn, $query);

    return $result;
}

function deleteUser($conn, $userId) {
    $query = "DELETE FROM users WHERE id = $userId";
    
    $result = mysqli_query($conn, $query);

    return $result;
}
?>