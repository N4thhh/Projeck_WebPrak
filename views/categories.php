<?php
$page_title = 'Categories';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-emerald-700">Kategori Keuangan</h1>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md">
            + Tambah Kategori
        </button>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        <!-- Contoh kategori -->
        <div class="bg-emerald-50 p-4 rounded-lg border hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-emerald-800">Makanan & Minuman</h3>
            <p class="text-sm text-gray-600 mt-1">Pengeluaran sehari-hari untuk makan, minum, ngopi.</p>
        </div>
        <div class="bg-emerald-50 p-4 rounded-lg border hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-emerald-800">Transportasi</h3>
            <p class="text-sm text-gray-600 mt-1">Ongkos perjalanan, bensin, parkir, dll.</p>
        </div>
        <div class="bg-emerald-50 p-4 rounded-lg border hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-emerald-800">Hiburan</h3>
            <p class="text-sm text-gray-600 mt-1">Langganan streaming, nonton, hangout, game.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
