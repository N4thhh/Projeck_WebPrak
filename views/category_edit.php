<?php
$page_title = 'Edit Kategori';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/category_functions.php';

if (!isset($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$id = (int)$_GET['id'];
$kategori = getKategoriById($id);

if (!$kategori) {
    echo "<p class='text-red-500 text-center mt-10'>Kategori tidak ditemukan.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $total = (float) $_POST['total_pengeluaran'];
    $persen = (int) $_POST['persen_anggaran'];

    if (updateKategori($id, $nama, $deskripsi, $total, $persen)) {
        header("Location: categories.php");
        exit();
    } else {
        $error_message = "Gagal memperbarui kategori.";
    }
}
?>

<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-emerald-700">Edit Kategori</h2>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="block mb-2">Nama Kategori</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($kategori['nama']) ?>" required
               class="w-full border px-3 py-2 rounded-lg mb-4">

        <label class="block mb-2">Deskripsi</label>
        <textarea name="deskripsi" rows="3"
                  class="w-full border px-3 py-2 rounded-lg mb-4"><?= htmlspecialchars($kategori['deskripsi']) ?></textarea>

        <label class="block mb-2">Total Pengeluaran (Rp)</label>
        <input type="number" name="total_pengeluaran" step="0.01"
               value="<?= $kategori['total_pengeluaran'] ?>" class="w-full border px-3 py-2 rounded-lg mb-4">

        <label class="block mb-2">% dari Anggaran</label>
        <input type="number" name="persen_anggaran" min="0" max="100"
               value="<?= $kategori['persen_anggaran'] ?>" class="w-full border px-3 py-2 rounded-lg mb-4">

        <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg">Simpan Perubahan</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
