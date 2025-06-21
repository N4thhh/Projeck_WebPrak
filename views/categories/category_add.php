<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../functions/category_functions.php';

if (!$is_logged_in) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = $_POST['type'];

    if (!empty($name) && in_array($type, ['income', 'expense'])) {
        if (tambahKategori($user_id, $name, $type)) {
            $_SESSION['success_message'] = "Kategori berhasil ditambahkan!";
            header("Location: ../categories.php");
            exit();
        } else {
            $error_message = "Gagal menambahkan kategori.";
        }
    } else {
        $error_message = "Mohon isi semua bidang dengan benar.";
    }
}
?>

<div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-emerald-700">Tambah Kategori Baru</h2>

    <?php if (isset($error_message)): ?>
        <div class="text-red-600 mb-3"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label class="block mb-2 font-medium text-gray-700">Nama Kategori</label>
        <input type="text" name="name" class="w-full px-4 py-2 border rounded mb-4" required>

        <label class="block mb-2 font-medium text-gray-700">Tipe</label>
        <select name="type" class="w-full px-4 py-2 border rounded mb-4" required>
            <option value="expense">Pengeluaran</option>
            <option value="income">Pemasukan</option>
        </select>

        <div class="text-right">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">Tambah</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
