<?php
require_once __DIR__ . '/../includes/db.php';

function tambahKategori($user_id, $name, $type) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $name, $type);
    return $stmt->execute();
}

function getAllKategoriByUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function hapusKategori($id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    return $stmt->execute();
}

function getKategoriById($id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateKategori($id, $user_id, $name, $type) {
    global $conn;
    $stmt = $conn->prepare("UPDATE categories SET name = ?, type = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $name, $type, $id, $user_id);
    return $stmt->execute();
}
