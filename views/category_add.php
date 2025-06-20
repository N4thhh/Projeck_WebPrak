<?php
$page_title = 'Tambah Kategori';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/category_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $total = (float) $_POST['total_pengeluaran'];
    $persen = (int) $_POST['persen_anggaran'];

    if (tambahKategori($nama, $deskripsi, $total, $persen)) {
        header("Location: categories.php");
        exit();
    } else {
        $error_message = "Gagal menambahkan kategori.";
    }
}
?>

<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-emerald-700">Tambah Kategori</h2>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="block mb-2">Nama Kategori</label>
        <input type="text" name="nama" required class="w-full border px-3 py-2 rounded-lg mb-4">

        <label class="block mb-2">Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="w-full border px-3 py-2 rounded-lg mb-4"></textarea>

        <label class="block mb-2">Total Pengeluaran (Rp)</label>
        <input type="number" name="total_pengeluaran" step="0.01" class="w-full border px-3 py-2 rounded-lg mb-4">

        <label class="block mb-2">% dari Anggaran</label>
        <input type="number" name="persen_anggaran" min="0" max="100" class="w-full border px-3 py-2 rounded-lg mb-4">

        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg">Simpan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
