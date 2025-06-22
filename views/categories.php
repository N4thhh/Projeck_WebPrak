<?php
$page_title = 'Kategori';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/category_functions.php';

if (!$is_logged_in) {
    header("Location: auth/login.php");
    exit();
}

// Jika mau hapus
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    hapusKategori($conn, $id); // <- pastikan $conn dikirim
    $_SESSION['success_message'] = "Kategori berhasil dihapus.";
    header("Location: categories.php");
    exit();
}

// Ambil list kategori
$kategori_list = getAllKategoriByUser($conn, $user_id); // <- pastikan $conn dikirim
// function ini harus ada di category_functions.php
?>

<section class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-emerald-700">Kategori Keuangan</h1>
        <div class="flex gap-2">
            <a href="categories/category_add.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md">+ Tambah Kategori</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-4 text-green-700 bg-green-100 border border-green-400 rounded px-4 py-2">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-3 gap-4">
        <?php while ($kategori = $kategori_list->fetch_assoc()): ?>
            <div class="bg-emerald-50 p-5 rounded-lg border hover:shadow-xl transition-all duration-300 transform hover:scale-105 relative">
                <h3 class="text-lg font-semibold text-emerald-800 flex items-center gap-2">
                    <?= $kategori['type'] === 'income' ? '💰' : '🛒' ?> <?= htmlspecialchars($kategori['name']) ?>
                </h3>
                <p class="text-sm text-gray-600 mt-1">Tipe: <strong><?= $kategori['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' ?></strong></p>
                <div class="absolute top-4 right-4 flex gap-2 text-sm">
                    <a href="categories/category_edit.php?id=<?= $kategori['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                    <a href="?delete_id=<?= $kategori['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
