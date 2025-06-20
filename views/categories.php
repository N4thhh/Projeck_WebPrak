<?php
$page_title = 'Kategori';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/category_functions.php';

// Cek dan proses hapus
if (isset($_GET['delete_id'])) {
    hapusKategori((int)$_GET['delete_id']);
    header("Location: categories.php");
    exit();
}

// Ambil data kategori
$kategori_list = getAllKategori();
?>

<section class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-emerald-700">Kategori Keuangan</h1>
        <div class="flex gap-2">
            <a href="category_add.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md">+ Tambah Kategori</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <?php while ($kategori = $kategori_list->fetch_assoc()): ?>
            <div class="bg-emerald-50 p-5 rounded-lg border hover:shadow-xl transition-all duration-300 transform hover:scale-105 relative">
                <h3 class="text-lg font-semibold text-emerald-800"><?= htmlspecialchars($kategori['nama']) ?></h3>
                <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($kategori['deskripsi']) ?></p>
                <p class="text-sm text-gray-800 font-medium mt-2">Total: <span class="font-bold text-emerald-700">Rp <?= number_format($kategori['total_pengeluaran'], 0, ',', '.') ?></span></p>
                <div class="w-full h-2 mt-2 bg-emerald-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-600" style="width: <?= $kategori['persen_anggaran'] ?>%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1"><?= $kategori['persen_anggaran'] ?>% dari anggaran</p>
                <div class="absolute top-4 right-4 flex gap-2 text-sm">
                    <a href="category_edit.php?id=<?= $kategori['id'] ?>" class="text-blue-500 hover:underline">Edit</a>

                    <a href="?delete_id=<?= $kategori['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
