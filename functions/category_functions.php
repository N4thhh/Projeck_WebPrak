<?php
require_once __DIR__ . '/../includes/db.php';

function tambahKategori($nama, $deskripsi, $total, $persen) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO categories (nama, deskripsi, total_pengeluaran, persen_anggaran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nama, $deskripsi, $total, $persen);
    return $stmt->execute();
}

function getAllKategori() {
    global $conn;
    return $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
}

function hapusKategori($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function getKategoriById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateKategori($id, $nama, $deskripsi, $total, $persen) {
    global $conn;
    $stmt = $conn->prepare("UPDATE categories SET nama = ?, deskripsi = ?, total_pengeluaran = ?, persen_anggaran = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $nama, $deskripsi, $total, $persen, $id);
    return $stmt->execute();
}
