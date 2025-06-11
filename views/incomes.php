<?php
$page_title = 'Add Income';
require_once __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) $_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $account_id = (int) $_POST['account_id'];
    $transaction_date = $_POST['transaction_date'];

    if ($amount > 0 && !empty($category_id) && !empty($account_id) && !empty($transaction_date)) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, account_id, category_id, type, amount, description, transaction_date) VALUES (?, ?, ?, 'income', ?, ?, ?)");
            $stmt->bind_param("iiisds", $user_id, $account_id, $category_id, $amount, $description, $transaction_date);
            $stmt->execute();

            $stmt_update = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ? AND user_id = ?");
            $stmt_update->bind_param("dii", $amount, $account_id, $user_id);
            $stmt_update->execute();

            $conn->commit();
            
            $_SESSION['success_message'] = "Pemasukan untuk '" . htmlspecialchars($description) . "' berhasil dicatat!";
            
            header("Location: " . $base_url . "/views/incomes.php");
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $error_message = "Gagal menambahkan pemasukan: " . $exception->getMessage();
        }
    } else {
        $error_message = "Harap isi semua kolom yang wajib diisi.";
    }
}

$categories_result = $conn->query("SELECT id, name FROM categories WHERE type = 'income'");
$accounts_result = $conn->query("SELECT id, name, balance FROM accounts WHERE user_id = $user_id");
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Catat Pemasukan Baru</h2>
        
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">';
            echo '<strong class="font-bold">Berhasil!</strong>';
            echo '<span class="block sm:inline"> ' . $_SESSION['success_message'] . '</span>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $error_message; ?></span>
            </div>
        <?php endif; ?>

        <form action="incomes.php" method="POST">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-gray-700 font-semibold mb-2">Jumlah (Amount)</label>
                    <input type="number" id="amount" name="amount" step="0.01" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                 <div>
                    <label for="account_id" class="block text-gray-700 font-semibold mb-2">Tujuan Dana (Akun)</label>
                    <select id="account_id" name="account_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Akun --</option>
                        <?php while ($account = $accounts_result->fetch_assoc()): ?>
                            <option value="<?= $account['id'] ?>"><?= htmlspecialchars($account['name']) ?> (Rp <?= number_format($account['balance']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="category_id" class="block text-gray-700 font-semibold mb-2">Kategori</label>
                <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                <input type="text" id="description" name="description" required placeholder="Contoh: Gaji bulan ini"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="mt-4">
                <label for="transaction_date" class="block text-gray-700 font-semibold mb-2">Tanggal</label>
                <input type="date" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="mt-6 text-right">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>