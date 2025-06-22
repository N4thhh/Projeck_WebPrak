<?php
require_once __DIR__ . '/../includes/db.php';

function tambahKategori($user_id, $name, $type) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $name, $type);
    return $stmt->execute();
}

function hapusKategori($conn, $id) {
    $id = (int)$id;
    return mysqli_query($conn, "DELETE FROM categories WHERE id = $id AND user_id = ".$_SESSION['user_id']);
}

function getAllKategoriByUser($conn, $user_id) {
    $user_id = (int)$user_id;
    return mysqli_query($conn, "SELECT * FROM categories WHERE user_id = $user_id");
}


function getKategoriById($conn, $id) {
    $id = (int)$id; // casting ke integer untuk keamanan
    $query = "SELECT * FROM categories WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function updateKategori($id, $user_id, $name, $type) {
    global $conn;
    $stmt = $conn->prepare("UPDATE categories SET name = ?, type = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $name, $type, $id, $user_id);
    return $stmt->execute();
}
