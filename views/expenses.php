<?php
$page_title = 'Add Expense';
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
    $wallet_id = (int) $_POST['wallet_id'];
    $transaction_date = $_POST['transaction_date'];

    if ($amount > 0 && !empty($category_id) && !empty($wallet_id) && !empty($transaction_date)) {
        
        $stmt_balance_check = $conn->prepare("SELECT balance FROM wallets WHERE id = ? AND user_id = ?");
        $stmt_balance_check->bind_param("ii", $wallet_id, $user_id);
        $stmt_balance_check->execute();
        $result = $stmt_balance_check->get_result();
        $wallet = $result->fetch_assoc();

        if ($wallet && $wallet['balance'] >= $amount) {
            $conn->begin_transaction();
            try {
                $stmt_insert = $conn->prepare("INSERT INTO transactions (user_id, wallet_id, category_id, type, amount, description, transaction_date) VALUES (?, ?, ?, 'expense', ?, ?, ?)");
                $stmt_insert->bind_param("iiisds", $user_id, $wallet_id, $category_id, $amount, $description, $transaction_date);
                $stmt_insert->execute();

                $stmt_update = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ? AND user_id = ?");
                $stmt_update->bind_param("dii", $amount, $wallet_id, $user_id);
                $stmt_update->execute();

                $conn->commit();
                
                $_SESSION['success_message'] = "Pengeluaran '" . htmlspecialchars($description) . "' berhasil dicatat!";
                header("Location: " . $base_url . "/views/expenses.php");
                exit();

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $error_message = "Gagal menambahkan pengeluaran: " . $exception->getMessage();
            }
        } else {
            $error_message = "Saldo di dompet tidak mencukupi.";
        }
    } else {
        $error_message = "Mohon lengkapi semua data yang wajib diisi.";
    }
}

// ✅ FIXED: Gunakan 'nama' bukan 'name'
$categories_result = $conn->query("SELECT id, nama FROM categories");
$wallets_result = $conn->query("SELECT id, name, balance FROM wallets WHERE user_id = $user_id");
?>

<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Catat Pengeluaran Baru</h2>
        
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p class="font-bold">Berhasil!</p><p>' . $_SESSION['success_message'] . '</p></div>';
            unset($_SESSION['success_message']);
        }
        if (isset($error_message)) {
            echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p class="font-bold">Error!</p><p>' . $error_message . '</p></div>';
        }
        ?>

        <form action="expenses.php" method="POST">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-gray-700 font-semibold mb-2">Jumlah</label>
                    <input type="number" id="amount" name="amount" step="0.01" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="wallet_id" class="block text-gray-700 font-semibold mb-2">Dompet Sumber</label>
                    <select id="wallet_id" name="wallet_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Dompet --</option>
                        <?php while ($wallet = $wallets_result->fetch_assoc()): ?>
                            <option value="<?= $wallet['id'] ?>"><?= htmlspecialchars($wallet['name']) ?> (Rp <?= number_format($wallet['balance']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="category_id" class="block text-gray-700 font-semibold mb-2">Kategori</label>
                <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Kategori --</option>
                    <?php if ($categories_result) { while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['nama']) ?></option>
                    <?php endwhile; } ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                <input type="text" id="description" name="description" required placeholder="Contoh: Makan siang di warung"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            
            <div class="mt-4">
                <label for="transaction_date" class="block text-gray-700 font-semibold mb-2">Tanggal</label>
                <input type="date" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
