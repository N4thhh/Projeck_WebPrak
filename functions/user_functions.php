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

function getUserById($conn, $userId) {
    $userId = (int)$userId;
    $query = "SELECT id, username, email FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function updateUser($conn, $userId, $username) {
    $username = mysqli_real_escape_string($conn, $username);

    $query = "UPDATE users SET username = '$username' WHERE id = $userId";

    return mysqli_query($conn, $query);
}
?>