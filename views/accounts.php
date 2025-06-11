<?php
$page_title = 'Manage Accounts';
require_once __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_account'])) {
    $name = htmlspecialchars($_POST['name']);
    $initial_balance = (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['initial_balance']);

    if (!empty($name)) {
        $stmt_check = $conn->prepare("SELECT id FROM accounts WHERE user_id = ? AND name = ?");
        $stmt_check->bind_param("is", $user_id, $name);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = "Anda sudah memiliki akun dengan tipe '$name'.";
        } else {
            $stmt = $conn->prepare("INSERT INTO accounts (user_id, name, balance) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $user_id, $name, $initial_balance);
            
            if ($stmt->execute()) {
                header("Location: " . $base_url . "/views/accounts.php?status=success");
                exit();
            } else {
                $error_message = "Gagal menambahkan akun. Silakan coba lagi.";
            }
        }
    } else {
        $error_message = "Tipe Akun tidak boleh kosong.";
    }
}

$stmt_get_accounts = $conn->prepare("SELECT id, name, balance FROM accounts WHERE user_id = ? ORDER BY name ASC");
$stmt_get_accounts->bind_param("i", $user_id);
$stmt_get_accounts->execute();
$accounts_result = $stmt_get_accounts->get_result();
?>

<div class="max-w-4xl mx-auto">

    <div class="bg-white p-6 rounded-xl shadow-md mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tambah Akun Baru</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $error_message; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">Akun baru berhasil ditambahkan!</span>
            </div>
        <?php endif; ?>

        <form action="accounts.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Tipe Akun</label>
                    <select id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Rekening Bank">Rekening Bank</option>
                        <option value="Dompet Digital">Dompet Digital</option>
                    </select>
                </div>
                <div>
                    <label for="initial_balance" class="block text-gray-700 font-semibold mb-2">Total Saldo Awal</label>
                    <input type="number" id="initial_balance" name="initial_balance" placeholder="0" value="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Pilih tipe akun. Jika Anda punya lebih dari satu rekening bank, jumlahkan saldonya.</p>
            <div class="mt-6 text-right">
                <button type="submit" name="add_account"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Tambah Akun
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Akun Anda</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Tipe Akun</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Saldo Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y">
                    <?php if ($accounts_result && $accounts_result->num_rows > 0): ?>
                        <?php while($row = $accounts_result->fetch_assoc()): ?>
                            <tr>
                                <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="py-3 px-4 text-right text-lg font-semibold text-emerald-700">
                                    Rp <?= number_format($row['balance'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center py-6 text-gray-500">Anda belum memiliki akun.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>